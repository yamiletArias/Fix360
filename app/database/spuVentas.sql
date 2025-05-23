-- PROCEDIMIENTO ALMACENADOS DE VENTAS REAL

-- PROCEDIMINETO PARA REGISTRAR EMPRESA
DELIMITER $$

CREATE PROCEDURE spRegisterEmpresa (
  IN _nomcomercial VARCHAR(80),
  IN _razonsocial VARCHAR(80),
  IN _telefono VARCHAR(20),
  IN _correo VARCHAR(100),
  IN _ruc CHAR(11)
)
BEGIN
  INSERT INTO empresas (nomcomercial, razonsocial, telefono, correo, ruc)
  VALUES (_nomcomercial, _razonsocial, _telefono, _correo, _ruc);

  SELECT LAST_INSERT_ID() AS idempresa;
END$$

DELIMITER ;	

-- registro de venta con orden
DROP PROCEDURE IF EXISTS spRegisterVentaConOrden;
DELIMITER $$

CREATE PROCEDURE spRegisterVentaConOrden (
  IN _conOrden      BOOLEAN,
  IN _idadmin       INT,
  IN _idpropietario INT,
  IN _idcliente     INT,
  IN _idvehiculo    INT,
  IN _kilometraje   DECIMAL(10,2),
  IN _observaciones VARCHAR(255),
  IN _ingresogrua   BOOLEAN,
  IN _fechaingreso  DATETIME,
  IN _tipocom       ENUM('boleta','factura'),
  IN _fechahora     DATETIME,
  IN _numserie      VARCHAR(10),
  IN _numcom        VARCHAR(10),
  IN _moneda        VARCHAR(20),
  IN _idcolaborador INT
)
BEGIN
  DECLARE v_idorden INT DEFAULT NULL;
  DECLARE v_idventa INT DEFAULT 0;
  DECLARE v_fechaing DATETIME;

  SET v_fechaing = COALESCE(_fechaingreso, _fechahora);

  -- 1) Inserta orden de servicio si corresponde
  IF _conOrden THEN
    INSERT INTO ordenservicios (
      idadmin,
      idpropietario,
      idcliente,
      idvehiculo,
      kilometraje,
      observaciones,
      ingresogrua,
      fechaingreso,
      fechasalida,
      estado
    ) VALUES (
      _idadmin,
      _idpropietario,
      _idcliente,
      _idvehiculo,
      _kilometraje,
      _observaciones,
      _ingresogrua,
      v_fechaing,
      NULL,
      'A'
    );
    SET v_idorden = LAST_INSERT_ID();
  END IF;

  -- 2) Inserta venta
  INSERT INTO ventas (
    idcliente,
    idpropietario,
    idcolaborador,
    idvehiculo,
    tipocom,
    fechahora,
    numserie,
    numcom,
    moneda,
    kilometraje,
    justificacion,
    estado
  ) VALUES (
    _idcliente,
    _idpropietario,
    _idcolaborador,
    NULLIF(_idvehiculo,0),
    _tipocom,
    _fechahora,
    _numserie,
    _numcom,
    _moneda,
    NULLIF(_kilometraje,0),
    NULL,
    TRUE
  );
  SET v_idventa = LAST_INSERT_ID();

  -- 3) Devuelve IDs
  SELECT v_idventa AS idventa,
         v_idorden AS idorden;
END$$

DELIMITER ;
/*
SELECT
  v.idventa,
  v.tipocom,
  v.numserie,
  v.numcom,
  v.moneda,
  v.fechahora AS fecha_venta,
  v.kilometraje AS km_venta,
  o.idorden,
  o.fechaingreso,
  o.kilometraje AS km_orden,
  o.observaciones,
  o.ingresogrua,
  d.iddetorden,
  s.servicio AS nombreservicio,
  d.precio,
  d.estado AS estado_servicio,
  m.namuser AS mecanico
FROM ventas v
LEFT JOIN ordenservicios o ON v.idcliente = o.idcliente AND DATE(v.fechahora) = DATE(o.fechaingreso)
LEFT JOIN detalleordenservicios d ON o.idorden = d.idorden
LEFT JOIN servicios s ON d.idservicio = s.idservicio
LEFT JOIN colaboradores m ON d.idmecanico = m.idcolaborador
ORDER BY v.idventa DESC, d.iddetorden;
*/
/*
-- 1) PROCEDIMIENTO DE REGISTRO DE VENTAS (cabecera)
DROP PROCEDURE IF EXISTS spuRegisterVenta;
DELIMITER $$
CREATE PROCEDURE spuRegisterVenta (
  IN _tipocom VARCHAR(50),
  IN _fechahora DATETIME, 
  IN _numserie VARCHAR(30),
  IN _numcom CHAR(20),
  IN _moneda CHAR(11),
  IN _idcliente INT,
  IN _idcolaborador INT,
  IN _idvehiculo INT,
  IN _kilometraje DECIMAL(10,2)
)
BEGIN
  INSERT INTO ventas (
    idcliente,
    idcolaborador,
    idvehiculo,
    tipocom,
    fechahora,
    numserie,
    numcom,
    moneda,
    kilometraje
  )
  VALUES (
    _idcliente,
    _idcolaborador,
    NULLIF(_idvehiculo, 0),
    _tipocom,
    _fechahora,
    _numserie,
    _numcom,
    _moneda,
    NULLIF(_kilometraje, 0)
  );
  SELECT LAST_INSERT_ID() AS idventa;
END$$
*/
-- 2) CALCULAR SALDO RESTANTE
DROP FUNCTION IF EXISTS calcularSaldoRestante;
DELIMITER $$
CREATE FUNCTION calcularSaldoRestante(
  _idkardex INT,
  _cantidad INT
) RETURNS INT
BEGIN
  DECLARE _saldoactual INT;

  -- Intentamos leer el último saldo de movimientos
  SELECT m.saldorestante
  INTO _saldoactual
  FROM movimientos m
  WHERE m.idkardex = _idkardex
  ORDER BY m.idmovimiento DESC
  LIMIT 1;

  -- Si no hay movimientos, usamos stockmax del kardex
  IF _saldoactual IS NULL THEN
    SELECT k.stockmax
    INTO _saldoactual
    FROM kardex k
    WHERE k.idkardex = _idkardex;
  END IF;

  -- Restamos la cantidad de la venta
  RETURN _saldoactual - _cantidad;
END$$
DELIMITER ;

-- 3) PROCEDIMIENTO DE REGISTRO DETALLE DE VENTA OBTENIENDO EL IDVENTA
DROP PROCEDURE IF EXISTS spuInsertDetalleVenta;
DELIMITER $$
CREATE PROCEDURE spuInsertDetalleVenta (
  IN _idventa INT,
  IN _idproducto INT,
  IN _cantidad INT,
  IN _numserie_detalle VARCHAR(50),
  IN _precioventa DECIMAL(7,2),
  IN _descuento DECIMAL(5,2)
)
BEGIN
  DECLARE _idkardex INT;
  DECLARE _idtipomov INT;
  DECLARE _saldoNuevo INT;
  -- Insertar en detalle de venta
  INSERT INTO detalleventa (
    idproducto, idventa, cantidad, numserie, precioventa, descuento
  )
  VALUES (
    _idproducto,
    _idventa,
    _cantidad,
    CASE 
      WHEN _numserie_detalle IS NULL THEN JSON_ARRAY() 
      ELSE JSON_ARRAY(_numserie_detalle) 
    END,
    _precioventa,
    _descuento
  );
  -- Traer idkardex del producto
  SELECT idkardex INTO _idkardex
  FROM kardex
  WHERE idproducto = _idproducto
  LIMIT 1;
  -- Obtener idtipomov para venta (flujo salida)
  SELECT idtipomov INTO _idtipomov
  FROM tipomovimientos
  WHERE flujo = 'salida' AND tipomov = 'venta'
  LIMIT 1;
  -- Calcular nuevo saldo restante
  SET _saldoNuevo = calcularSaldoRestante(_idkardex, _cantidad);
  -- Insertar en movimientos
  INSERT INTO movimientos (
    idkardex, 
    idtipomov, 
    fecha, 
    cantidad,
    preciounit,
    saldorestante
  )
  VALUES (
    _idkardex, 
    _idtipomov, 
    CURDATE(),
    _cantidad,
    _precioventa,
    _saldoNuevo
  );
END$$
DELIMITER ;

/*
DROP PROCEDURE IF EXISTS spuInsertDetalleVenta;
DELIMITER $$
CREATE PROCEDURE spuInsertDetalleVenta (
  IN _idventa INT,
  IN _idproducto INT,
  IN _cantidad INT,
  IN _numserie_detalle VARCHAR(50),
  IN _precioventa DECIMAL(7,2),
  IN _descuento DECIMAL(5,2)
)
BEGIN
  DECLARE _idkardex INT;
  DECLARE _idtipomov INT;
  DECLARE _saldoNuevo INT;
  -- Insertar en detalle de venta
  INSERT INTO detalleventa (
    idproducto, idventa, cantidad, numserie, precioventa, descuento
  )
  VALUES (
    _idproducto,
    _idventa,
    _cantidad,
    CASE 
      WHEN _numserie_detalle IS NULL THEN JSON_ARRAY() 
      ELSE JSON_ARRAY(_numserie_detalle) 
    END,
    _precioventa,
    _descuento
  );
  -- Traer idkardex del producto
  SELECT idkardex INTO _idkardex
  FROM kardex
  WHERE idproducto = _idproducto
  LIMIT 1;
  -- Obtener idtipomov para venta (flujo salida)
  SELECT idtipomov INTO _idtipomov
  FROM tipomovimientos
  WHERE flujo = 'salida' AND tipomov = 'venta'
  LIMIT 1;
  -- Calcular nuevo saldo restante
  SET _saldoNuevo = calcularSaldoRestante(_idkardex, _cantidad);
  -- Insertar en movimientos
  INSERT INTO movimientos (
    idkardex, idtipomov, fecha, cantidad, saldorestante
  )
  VALUES (
    _idkardex, _idtipomov, CURDATE(), _cantidad, _saldoNuevo
  );
END$$
DELIMITER ;
*/
-- 4) PROCEDIMINETO PARA OBTENER MONEDAS (soles & dolares)
DROP PROCEDURE IF EXISTS spuGetMonedasVentas;
DELIMITER $$
CREATE PROCEDURE spuGetMonedasVentas()
BEGIN
  SELECT 'Soles' AS moneda
  UNION
  SELECT 'Dólares' AS moneda
  UNION
  SELECT DISTINCT
    CASE
      WHEN UPPER(TRIM(moneda)) = 'SOLES' THEN 'Soles'
      WHEN UPPER(TRIM(moneda)) = 'DOLARES' THEN 'Dólares'
      ELSE NULL
    END AS moneda
  FROM ventas
  WHERE moneda IN ('SOLES', 'DOLARES');
END $$

-- 5) PROCEDIMIENTO PARA BUSCAR PRODUCTO (producto, stock, precio)
DROP PROCEDURE IF EXISTS buscar_producto;
DELIMITER $$
CREATE PROCEDURE buscar_producto(
  IN termino_busqueda VARCHAR(255)
)
BEGIN
  SELECT
    P.idproducto,
    CONCAT(S.subcategoria, ' ', P.descripcion) AS subcategoria_producto,
    P.precio,
    -- En lugar de sumar, tomamos el saldo restante más reciente
    (
      SELECT m2.saldorestante
      FROM movimientos m2
      WHERE m2.idkardex = k.idkardex
      ORDER BY m2.idmovimiento DESC
      LIMIT 1
    ) AS stock
  FROM productos P
  JOIN subcategorias S ON P.idsubcategoria = S.idsubcategoria
  JOIN kardex k       ON P.idproducto     = k.idproducto
  WHERE S.subcategoria LIKE CONCAT('%', termino_busqueda, '%')
     OR P.descripcion   LIKE CONCAT('%', termino_busqueda, '%')
  LIMIT 10;
END $$
DELIMITER ;

CALL buscar_producto("moto");

-- 6) PROCEDIMIENTO PARA MOSTRAR EL PROVEEDOR
DROP PROCEDURE IF EXISTS spuGetProveedores;
DELIMITER $$
CREATE PROCEDURE spuGetProveedores()
BEGIN
  SELECT DISTINCT 
    p.idproveedor,
    e.nomcomercial AS nombre_empresa
  FROM proveedores p
  INNER JOIN empresas e ON p.idempresa = e.idempresa
  LEFT JOIN compras c ON c.idproveedor = p.idproveedor;
END $$
call spuGetProveedores();

-- 7) PROCEDIMIENTO PARA REGISTRAR COMPRAS
DROP PROCEDURE IF EXISTS spuRegisterCompra;
DELIMITER $$
CREATE PROCEDURE spuRegisterCompra (
  IN _fechacompra DATE,
  IN _tipocom VARCHAR(50),
  IN _numserie VARCHAR(10),
  IN _numcom VARCHAR(10),
  IN _moneda VARCHAR(20),
  IN _idproveedor INT,
  IN _idcolaborador INT
)
BEGIN
  INSERT INTO compras (
    idproveedor,
    idcolaborador,
    fechacompra,
    tipocom,
    numserie,
    numcom,
    moneda
  )
  VALUES (
    _idproveedor,
    _idcolaborador,
    _fechacompra,
    _tipocom,
    _numserie,
    _numcom,
    _moneda
  );
  SELECT LAST_INSERT_ID() AS idcompra;
END $$

-- 8) PROCEDIMIENTO PARA REGISTRAR DETALLE DE COMPRA OBTENIENDO EL IDCOMPRA
DROP PROCEDURE IF EXISTS spuInsertDetalleCompra;
DELIMITER $$
CREATE PROCEDURE spuInsertDetalleCompra (
  IN _idcompra INT,
  IN _idproducto INT,
  IN _cantidad INT,
  IN _preciocompra DECIMAL(7,2),
  IN _descuento DECIMAL(5,2)
)
BEGIN
  DECLARE _idkardex INT;
  DECLARE _saldorestante INT;

  -- Insertar detallecompra
  INSERT INTO detallecompra (
    idproducto,
    idcompra,
    cantidad,
    preciocompra,
    descuento
  )
  VALUES (
    _idproducto,
    _idcompra,
    _cantidad,
    _preciocompra,
    _descuento
  );

  -- Obtener idkardex del producto
  SELECT idkardex INTO _idkardex
  FROM kardex
  WHERE idproducto = _idproducto
  LIMIT 1;

  -- Obtener saldo restante actual
  SELECT saldorestante INTO _saldorestante
  FROM movimientos
  WHERE idkardex = _idkardex
  ORDER BY idmovimiento DESC
  LIMIT 1;

  SET _saldorestante = IFNULL(_saldorestante, 0) + _cantidad;

  -- Insertar movimiento de entrada por compra
  INSERT INTO movimientos (
    idkardex,
    idtipomov,
    fecha,
    cantidad,
    saldorestante
  )
  VALUES (
    _idkardex,
    (SELECT idtipomov FROM tipomovimientos WHERE flujo = 'entrada' AND tipomov = 'compra' LIMIT 1),
    CURDATE(),
    _cantidad,
    _saldorestante
  );

END$$
DELIMITER ;

-- 9) PROCEDIMIENTO PARA REGISTRAR COTIZACION
DROP PROCEDURE IF EXISTS spuRegisterCotizaciones;
DELIMITER $$
CREATE PROCEDURE spuRegisterCotizaciones (
  IN _fechahora TIMESTAMP,
  IN _vigenciadias INT,
  IN _moneda VARCHAR(20),
  IN _idcolaborador INT,
  IN _idcliente INT
)
BEGIN
  INSERT INTO cotizaciones (
    idcliente,
    idcolaborador,
    fechahora,
    vigenciadias,
    moneda
  )
  VALUES (
    _idcliente,
    _idcolaborador,
    _fechahora,
    _vigenciadias,
    _moneda
  );
  SELECT LAST_INSERT_ID() AS idcotizacion;
END $$

-- 10) PROCEDIMIENTO PARA REGISTRAR EL DETALLE COTIZACION OBTENIENDO EL IDCOTIZACION
DROP PROCEDURE IF EXISTS spuInsertDetalleCotizacion;
DELIMITER $$
CREATE PROCEDURE spuInsertDetalleCotizacion (
  IN _idcotizacion INT,
  IN _idproducto INT,
  IN _cantidad INT,
  IN _precio DECIMAL(7,2),
  IN _descuento DECIMAL(5,2)
)
BEGIN
  INSERT INTO detallecotizacion (
    idproducto,
    idcotizacion,
    cantidad,
    precio,
    descuento
  )
  VALUES (
    _idproducto,
    _idcotizacion,
    _cantidad,
    _precio,
    _descuento
  );
END $$

-- 11) PROCEDIMIENTO PARA LA JUSTIFICACION DE LA COMPRA ELIMINADA = COMPRA ANULADA (con devolución de stock)
DROP PROCEDURE IF EXISTS spuDeleteCompra;
DELIMITER $$
CREATE PROCEDURE spuDeleteCompra (
  IN _idcompra      INT,
  IN _justificacion VARCHAR(255)
)
BEGIN
  DECLARE _idproducto INT;
  DECLARE _cantidad INT;
  DECLARE _idkardex INT;
  DECLARE _saldorestante INT;
  DECLARE _done INT DEFAULT FALSE;

  DECLARE cur CURSOR FOR 
    SELECT dc.idproducto, dc.cantidad
    FROM detallecompra dc
    WHERE dc.idcompra = _idcompra;

  DECLARE CONTINUE HANDLER FOR NOT FOUND SET _done = TRUE;
  DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
    ROLLBACK;
    RESIGNAL;
  END;

  START TRANSACTION;

    -- 1. Marcar la compra como anulada
    UPDATE compras
    SET estado = FALSE,
        justificacion = _justificacion
    WHERE idcompra = _idcompra;

    -- 2. Procesar cada producto del detalle de la compra
    OPEN cur;
    read_loop: LOOP
      FETCH cur INTO _idproducto, _cantidad;
      IF _done THEN
        LEAVE read_loop;
      END IF;

      -- 2.1 Obtener idkardex del producto
      SELECT k.idkardex INTO _idkardex
      FROM kardex k
      WHERE k.idproducto = _idproducto
      LIMIT 1;

      -- 2.2 Calcular nuevo saldo restante (restar la cantidad)
      SELECT saldorestante INTO _saldorestante
      FROM movimientos
      WHERE idkardex = _idkardex
      ORDER BY idmovimiento DESC
      LIMIT 1;

      SET _saldorestante = IFNULL(_saldorestante, 0) - _cantidad;

      -- 2.3 Insertar movimiento de devolución de compra (flujo: salida)
      INSERT INTO movimientos (idkardex, idtipomov, fecha, cantidad, saldorestante)
      VALUES (
        _idkardex,
        (SELECT idtipomov FROM tipomovimientos WHERE flujo = 'salida' AND tipomov = 'devolucion' LIMIT 1),
        CURDATE(),
        _cantidad,
        _saldorestante
      );

    END LOOP;
    CLOSE cur;

  COMMIT;
END$$
DELIMITER ;

-- 12) PRODEDIMIENTO PARA LA JUSTIFICACION DE LA VENTA ELIMINADA = VENTA ANULADA
-- REAL
DROP PROCEDURE IF EXISTS spuDeleteVenta;
DELIMITER $$
CREATE PROCEDURE spuDeleteVenta (
  IN _idventa      INT,
  IN _justificacion VARCHAR(255)
)
BEGIN
  DECLARE _idproducto INT;
  DECLARE _cantidad INT;
  DECLARE _idkardex INT;
  DECLARE _saldorestante INT;
  DECLARE _done INT DEFAULT FALSE;
  DECLARE v_fechahora DATETIME;
  DECLARE cur CURSOR FOR 
    SELECT dv.idproducto, dv.cantidad
    FROM detalleventa dv
    WHERE dv.idventa = _idventa;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET _done = TRUE;
  DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
    ROLLBACK;
    RESIGNAL;
  END;

  START TRANSACTION;
    -- Capturamos la fecha/hora original de la venta
    SELECT fechahora INTO v_fechahora
    FROM ventas
    WHERE idventa = _idventa;

    -- 1. Marcar la venta como anulada
    UPDATE ventas
    SET estado = FALSE,
        justificacion = _justificacion
    WHERE idventa = _idventa;

    -- 1.1. Marcar la(s) orden(es) de servicio como inactivas
    UPDATE ordenservicios
    SET estado = 'I'
    WHERE idcliente = (SELECT idcliente FROM ventas WHERE idventa = _idventa)
      AND DATE(fechaingreso) = DATE(v_fechahora);

    -- 2. Procesar cada producto de la venta
    OPEN cur;
    read_loop: LOOP
      FETCH cur INTO _idproducto, _cantidad;
      IF _done THEN
        LEAVE read_loop;
      END IF;
      -- 2.1 Obtener idkardex del producto
      SELECT k.idkardex INTO _idkardex
      FROM kardex k
      WHERE k.idproducto = _idproducto
      LIMIT 1;
      -- 2.2 Calcular nuevo saldo restante (suma)
      SELECT saldorestante INTO _saldorestante
      FROM movimientos
      WHERE idkardex = _idkardex
      ORDER BY idmovimiento DESC
      LIMIT 1;
      SET _saldorestante = IFNULL(_saldorestante, 0) + _cantidad;
      -- 2.3 Insertar movimiento de devolución
      INSERT INTO movimientos (idkardex, idtipomov, fecha, cantidad, saldorestante)
      VALUES (
        _idkardex,
        (SELECT idtipomov FROM tipomovimientos WHERE flujo = 'entrada' AND tipomov = 'devolucion' LIMIT 1),
        CURDATE(),
        _cantidad,
        _saldorestante
      );
    END LOOP;
    CLOSE cur;
  COMMIT;
END$$
DELIMITER ;
/*
DROP PROCEDURE IF EXISTS spuDeleteVenta;
DELIMITER $$
CREATE PROCEDURE spuDeleteVenta (
  IN _idventa      INT,
  IN _justificacion VARCHAR(255)
)
BEGIN
  DECLARE _idproducto INT;
  DECLARE _cantidad INT;
  DECLARE _idkardex INT;
  DECLARE _saldorestante INT;
  DECLARE _done INT DEFAULT FALSE;
  DECLARE cur CURSOR FOR 
    SELECT dv.idproducto, dv.cantidad
    FROM detalleventa dv
    WHERE dv.idventa = _idventa;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET _done = TRUE;
  DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
    ROLLBACK;
    RESIGNAL;
  END;
  START TRANSACTION;
    -- 1. Marcar la venta como anulada
    UPDATE ventas
    SET estado = FALSE,
        justificacion = _justificacion
    WHERE idventa = _idventa;
    -- 2. Procesar cada producto de la venta
    OPEN cur;
    read_loop: LOOP
      FETCH cur INTO _idproducto, _cantidad;
      IF _done THEN
        LEAVE read_loop;
      END IF;
      -- 2.1 Obtener idkardex del producto
      SELECT k.idkardex INTO _idkardex
      FROM kardex k
      WHERE k.idproducto = _idproducto
      LIMIT 1;
      -- 2.2 Calcular nuevo saldo restante (suma)
      SELECT saldorestante INTO _saldorestante
      FROM movimientos
      WHERE idkardex = _idkardex
      ORDER BY idmovimiento DESC
      LIMIT 1;
      SET _saldorestante = IFNULL(_saldorestante, 0) + _cantidad;
      -- 2.3 Insertar movimiento de devolución
      INSERT INTO movimientos (idkardex, idtipomov, fecha, cantidad, saldorestante)
      VALUES (
        _idkardex,
        (SELECT idtipomov FROM tipomovimientos WHERE flujo = 'entrada' AND tipomov = 'devolucion' LIMIT 1),
        CURDATE(),
        _cantidad,
        _saldorestante
      );
    END LOOP;
    CLOSE cur;
  COMMIT;
END$$

-- PROCEDIMIENTO PARA ELIMINAR COTIZACIONES
DROP PROCEDURE IF EXISTS spuDeleteCotizacion;
DELIMITER $$
CREATE PROCEDURE spuDeleteCotizacion (
  IN _idcotizacion INT,
  IN _justificacion VARCHAR(255)
)
BEGIN
  DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
    ROLLBACK;
    RESIGNAL;
  END;

  START TRANSACTION;

    -- Solo marcamos como anulada (no afecta stock)
    UPDATE cotizaciones
    SET estado = FALSE,
        justificacion = _justificacion
    WHERE idcotizacion = _idcotizacion;

  COMMIT;
END$$
DELIMITER ;*/

-- 13) PROCEDIMIENTO PARA DATOS DE VENTA (DIA, SEMANA Y MES)
DROP PROCEDURE IF EXISTS spListVentasPorPeriodo;
DELIMITER $$
CREATE PROCEDURE spListVentasPorPeriodo(
  IN _modo   ENUM('semana','mes','dia'),
  IN _fecha  DATE
)
BEGIN
  DECLARE start_date DATE;
  DECLARE end_date   DATE;
  IF _modo = 'semana' THEN
    SET start_date = DATE_SUB(_fecha, INTERVAL WEEKDAY(_fecha) DAY);
    SET end_date   = DATE_ADD(start_date, INTERVAL 6 DAY);
  ELSEIF _modo = 'mes' THEN
    SET start_date = DATE_FORMAT(_fecha, '%Y-%m-01');
    SET end_date   = LAST_DAY(_fecha);
  ELSE
    SET start_date = _fecha;
    SET end_date   = _fecha;  
  END IF;
  SELECT
    v.idventa    AS id,
    COALESCE(CONCAT(p.apellidos,' ',p.nombres), e.nomcomercial) AS cliente,
    v.tipocom,
    v.numcom,
    vt.total_pendiente,
    CASE WHEN vt.total_pendiente = 0 THEN 'pagado' ELSE 'pendiente' END AS estado_pago
  FROM ventas v
    JOIN clientes c      ON v.idcliente  = c.idcliente
    LEFT JOIN personas p ON c.idpersona  = p.idpersona
    LEFT JOIN empresas e ON c.idempresa  = e.idempresa
    JOIN vista_saldos_por_venta vt ON v.idventa = vt.idventa
  WHERE DATE(v.fechahora) BETWEEN start_date AND end_date
    AND v.estado = TRUE
  ORDER BY v.fechahora;
END$$

-- 14) PROCEDIMIENTO PARA DATOS DE COMPRA (DIA, SEMANA Y MES)
DROP PROCEDURE IF EXISTS spListComprasPorPeriodo;
DELIMITER $$
CREATE PROCEDURE spListComprasPorPeriodo(
  IN _modo  ENUM('semana','mes','dia'),
  IN _fecha DATE
)
BEGIN
  DECLARE start_date DATE;
  DECLARE end_date   DATE;
  -- Calcular rango según modo
  IF _modo = 'semana' THEN
    SET start_date = DATE_SUB(_fecha, INTERVAL WEEKDAY(_fecha) DAY);
    SET end_date   = DATE_ADD(start_date, INTERVAL 6 DAY);
  ELSEIF _modo = 'mes' THEN
    SET start_date = DATE_FORMAT(_fecha, '%Y-%m-01');
    SET end_date   = LAST_DAY(_fecha);
  ELSE
    SET start_date = _fecha;
    SET end_date   = _fecha;
  END IF;
  -- Listar compras en el rango con info de pago
  SELECT 
    C.idcompra AS id,
    E.nomcomercial AS proveedor,
    C.tipocom, 
    C.numcom,
    vspc.total_pendiente,
    CASE 
      WHEN vspc.total_pendiente = 0 THEN 'pagado'
      ELSE 'pendiente'
    END AS estado_pago
  FROM compras C
  JOIN proveedores P ON C.idproveedor = P.idproveedor
  JOIN empresas E ON P.idempresa = E.idempresa
  LEFT JOIN vista_saldos_por_compra vspc ON C.idcompra = vspc.idcompra
  WHERE DATE(C.fechacompra) BETWEEN start_date AND end_date
    AND C.estado = TRUE
  ORDER BY C.fechacompra;
END$$

-- 15) PROCEDIMIENTO PARA DATOS DE COTIZACIÓN (DIA, SEMANA Y MES)
DROP PROCEDURE IF EXISTS spListCotizacionesPorPeriodo;
DELIMITER $$
CREATE PROCEDURE spListCotizacionesPorPeriodo(
  IN _modo   ENUM('dia','semana','mes'),
  IN _fecha  DATE
)
BEGIN
  DECLARE start_date, end_date DATE;
  IF _modo='semana' THEN
    SET start_date=DATE_SUB(_fecha,INTERVAL WEEKDAY(_fecha) DAY),
        end_date=DATE_ADD(start_date,INTERVAL 6 DAY);
  ELSEIF _modo='mes' THEN
    SET start_date=DATE_FORMAT(_fecha,'%Y-%m-01'),
        end_date=LAST_DAY(_fecha);
  ELSE
    SET start_date=_fecha, end_date=_fecha;
  END IF;

  SELECT
    v.idcotizacion AS id,
    v.cliente,
    SUM(v.precio) AS total,
    v.vigencia,
    CASE
      WHEN DATE_ADD(DATE(v.fechahora),INTERVAL v.vigencia DAY)>=CURRENT_DATE()
        THEN 'vigente'
      ELSE 'expirada'
    END AS estado_vigencia,
    DATE(v.fechahora) AS fecha
  FROM vs_cotizaciones v
  WHERE DATE(v.fechahora) BETWEEN start_date AND end_date
  GROUP BY v.idcotizacion, v.cliente, v.vigencia, DATE(v.fechahora)
  ORDER BY DATE(v.fechahora);
END$$
DELIMITER ;

-- 15) PROCEDIMIENTO PARA BUSCAR CLIENTES
/*
DROP PROCEDURE IF EXISTS buscar_cliente;
DELIMITER $$
CREATE PROCEDURE buscar_cliente(IN termino_busqueda VARCHAR(255))
BEGIN
    SELECT 
        C.idcliente,
        CASE
            WHEN C.idempresa IS NOT NULL AND E.nomcomercial IS NOT NULL THEN E.nomcomercial
            WHEN C.idpersona IS NOT NULL AND P.nombres IS NOT NULL THEN CONCAT(P.nombres, ' ', P.apellidos)
        END AS cliente,
        C.idempresa,
        C.idpersona
    FROM clientes C
    LEFT JOIN empresas E ON C.idempresa = E.idempresa
    LEFT JOIN personas P ON C.idpersona = P.idpersona
    WHERE 
        (E.nomcomercial LIKE CONCAT('%', termino_busqueda, '%') AND E.nomcomercial IS NOT NULL)
        OR 
        ((P.nombres LIKE CONCAT('%', termino_busqueda, '%') OR P.apellidos LIKE CONCAT('%', termino_busqueda, '%')) 
         AND P.nombres IS NOT NULL AND P.apellidos IS NOT NULL)
    LIMIT 10;
END$$
*/

