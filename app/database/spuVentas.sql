USE dbfix360;
DELIMITER $$

-- 1) PROCEDIMIENTO PARA REGISTRAR EMPRESA
DROP PROCEDURE IF EXISTS spRegisterEmpresa $$
CREATE PROCEDURE spRegisterEmpresa (
  IN _nomcomercial VARCHAR(80),
  IN _razonsocial   VARCHAR(80),
  IN _telefono      VARCHAR(20),
  IN _correo        VARCHAR(100),
  IN _ruc           CHAR(11)
)
BEGIN
  INSERT INTO empresas (nomcomercial, razonsocial, telefono, correo, ruc)
  VALUES (_nomcomercial, _razonsocial, _telefono, _correo, _ruc);
  SELECT LAST_INSERT_ID() AS idempresa;
END $$
  
-- 2) PROCEDIMIENTO PARA REGISTRAR VENTA CON ORDEN
DROP PROCEDURE IF EXISTS spRegisterVentaConOrden $$
CREATE PROCEDURE spRegisterVentaConOrden (
  IN _conOrden        BOOLEAN,
  IN _idadmin         INT,
  IN _idpropietario   INT,
  IN _idcliente       INT,
  IN _idvehiculo      INT,
  IN _kilometraje     DECIMAL(10,2),
  IN _observaciones   VARCHAR(255),
  IN _ingresogrua     BOOLEAN,
  IN _fechaingreso    DATETIME,
  IN _tipocom         ENUM('boleta','factura','orden de trabajo'),
  IN _fechahora       DATETIME,
  IN _numserie        VARCHAR(10),
  IN _numcom          VARCHAR(10),
  IN _moneda          VARCHAR(20),
  IN _idcolaborador   INT
)
BEGIN
  DECLARE v_idorden INT            DEFAULT NULL;
  DECLARE v_idventa INT            DEFAULT NULL;
  DECLARE v_fechaing DATETIME;
  DECLARE v_idexpediente_ot INT    DEFAULT NULL;

  SET v_fechaing = COALESCE(_fechaingreso, _fechahora);

  IF _tipocom = 'orden de trabajo' THEN
    INSERT INTO expediente_ot (
      idcliente,
      idvehiculo
    ) VALUES (
      NULLIF(_idcliente, 0),
      NULLIF(_idvehiculo, 0)
    );
    SET v_idexpediente_ot = LAST_INSERT_ID();
  END IF;

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
      NULLIF(_idcliente, 0),
      NULLIF(_idvehiculo, 0),
      _kilometraje,
      _observaciones,
      _ingresogrua,
      v_fechaing,
      NULL,
      'A'
    );
    SET v_idorden = LAST_INSERT_ID();
    SET v_idexpediente_ot = v_idorden;
  END IF;

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
    idexpediente_ot,
    kilometraje,
    justificacion,
    estado
  ) VALUES (
    NULLIF(_idcliente, 0),
    _idpropietario,
    _idcolaborador,
    NULLIF(_idvehiculo, 0),
    _tipocom,
    _fechahora,
    _numserie,
    _numcom,
    _moneda,
    v_idexpediente_ot,
    NULLIF(_kilometraje, 0),
    NULL,
    TRUE
  );
  SET v_idventa = LAST_INSERT_ID();

  SELECT v_idventa AS idventa,
         v_idorden AS idorden;
END $$

-- 3) FUNCIÓN PARA CALCULAR SALDO RESTANTE
DROP FUNCTION IF EXISTS calcularSaldoRestante $$
CREATE FUNCTION calcularSaldoRestante(
  _idkardex INT,
  _cantidad INT
) RETURNS INT
BEGIN
  DECLARE _saldoactual INT;

  SELECT m.saldorestante
  INTO _saldoactual
  FROM movimientos m
  WHERE m.idkardex = _idkardex
  ORDER BY m.idmovimiento DESC
  LIMIT 1;

  IF _saldoactual IS NULL THEN
    SELECT k.stockmax
    INTO _saldoactual
    FROM kardex k
    WHERE k.idkardex = _idkardex;
  END IF;

  RETURN _saldoactual - _cantidad;
END $$

-- 4) PROCEDIMIENTO DE REGISTRO DETALLE DE VENTA
DROP PROCEDURE IF EXISTS spuInsertDetalleVenta $$
CREATE PROCEDURE spuInsertDetalleVenta (
  IN _idventa            INT,
  IN _idproducto         INT,
  IN _cantidad           INT,
  IN _numserie_detalle   VARCHAR(50),
  IN _precioventa        DECIMAL(7,2),
  IN _descuento          DECIMAL(5,2)
)
BEGIN
  DECLARE _idkardex   INT;
  DECLARE _idtipomov  INT;
  DECLARE _saldoNuevo INT;

  INSERT INTO detalleventa (
    idproducto,
    idventa,
    cantidad,
    numserie,
    precioventa,
    descuento
  ) VALUES (
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

  UPDATE productos
    SET precio = _precioventa
  WHERE idproducto = _idproducto;

  SELECT idkardex
    INTO _idkardex
  FROM kardex
  WHERE idproducto = _idproducto
  LIMIT 1;

  SELECT idtipomov
    INTO _idtipomov
  FROM tipomovimientos
  WHERE flujo = 'salida'
    AND tipomov = 'venta'
  LIMIT 1;

  SET _saldoNuevo = calcularSaldoRestante(_idkardex, _cantidad);

  INSERT INTO movimientos (
    idkardex,
    idtipomov,
    fecha,
    cantidad,
    preciounit,
    saldorestante
  ) VALUES (
    _idkardex,
    _idtipomov,
    CURDATE(),
    _cantidad,
    _precioventa,
    _saldoNuevo
  );
END $$

-- 5) PROCEDIMIENTO PARA OBTENER MONEDAS DE VENTAS
DROP PROCEDURE IF EXISTS spuGetMonedasVentas $$
CREATE PROCEDURE spuGetMonedasVentas()
BEGIN
  SELECT 'Soles' AS moneda
  UNION
  SELECT 'Dólares' AS moneda
  UNION
  SELECT DISTINCT
    CASE
      WHEN UPPER(TRIM(moneda)) = 'SOLES'   THEN 'Soles'
      WHEN UPPER(TRIM(moneda)) = 'DOLARES' THEN 'Dólares'
      ELSE NULL
    END AS moneda
  FROM ventas
  WHERE moneda IN ('SOLES', 'DOLARES');
END $$

-- 6) PROCEDIMIENTO PARA BUSCAR PRODUCTO (producto, stock, precio)
DROP PROCEDURE IF EXISTS buscar_producto $$
CREATE PROCEDURE buscar_producto(
  IN termino_busqueda VARCHAR(255)
)
BEGIN
  SELECT
    P.idproducto,
    CONCAT(S.subcategoria, ' ', P.descripcion) AS subcategoria_producto,
    P.precio,
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
     OR P.codigobarra   LIKE CONCAT('%', termino_busqueda, '%')
  LIMIT 10;
END $$

-- 7) PROCEDIMIENTO PARA MOSTRAR PROVEEDORES
DROP PROCEDURE IF EXISTS spuGetProveedores $$
CREATE PROCEDURE spuGetProveedores()
BEGIN
  SELECT DISTINCT 
    p.idproveedor,
    e.nomcomercial AS nombre_empresa
  FROM proveedores p
  INNER JOIN empresas e ON p.idempresa = e.idempresa
  LEFT JOIN compras c ON c.idproveedor = p.idproveedor;
END $$

-- 8) PROCEDIMIENTO PARA REGISTRAR COMPRA
DROP PROCEDURE IF EXISTS spuRegisterCompra $$
CREATE PROCEDURE spuRegisterCompra (
  IN _fechacompra     DATE,
  IN _tipocom         VARCHAR(50),
  IN _numserie        VARCHAR(10),
  IN _numcom          VARCHAR(10),
  IN _moneda          VARCHAR(20),
  IN _idproveedor     INT,
  IN _idcolaborador   INT
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
  ) VALUES (
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

-- 9) PROCEDIMIENTO PARA REGISTRAR DETALLE DE COMPRA
DROP PROCEDURE IF EXISTS spuInsertDetalleCompra $$
CREATE PROCEDURE spuInsertDetalleCompra (
  IN _idcompra     INT,
  IN _idproducto   INT,
  IN _cantidad     INT,
  IN _preciocompra DECIMAL(7,2),
  IN _descuento    DECIMAL(5,2)
)
BEGIN
  DECLARE _idkardex      INT;
  DECLARE _saldorestante INT;

  INSERT INTO detallecompra (
    idproducto,
    idcompra,
    cantidad,
    preciocompra,
    descuento
  ) VALUES (
    _idproducto,
    _idcompra,
    _cantidad,
    _preciocompra,
    _descuento
  );

  UPDATE productos
    SET precio = _preciocompra
  WHERE idproducto = _idproducto;

  SELECT idkardex
    INTO _idkardex
  FROM kardex
  WHERE idproducto = _idproducto
  LIMIT 1;

  SELECT saldorestante
    INTO _saldorestante
  FROM movimientos
  WHERE idkardex = _idkardex
  ORDER BY idmovimiento DESC
  LIMIT 1;
  SET _saldorestante = IFNULL(_saldorestante, 0) + _cantidad;

  INSERT INTO movimientos (
    idkardex,
    idtipomov,
    fecha,
    cantidad,
    preciounit,
    saldorestante
  ) VALUES (
    _idkardex,
    (SELECT idtipomov 
       FROM tipomovimientos 
      WHERE flujo = 'entrada' 
        AND tipomov = 'compra'
      LIMIT 1),
    CURDATE(),
    _cantidad,
    _preciocompra,
    _saldorestante
  );
END $$

-- 10) PROCEDIMIENTO PARA REGISTRAR COTIZACIÓN
DROP PROCEDURE IF EXISTS spuRegisterCotizaciones $$
CREATE PROCEDURE spuRegisterCotizaciones (
  IN _fechahora   TIMESTAMP,
  IN _vigenciadias INT,
  IN _moneda       VARCHAR(20),
  IN _idcolaborador INT,
  IN _idcliente     INT
)
BEGIN
  INSERT INTO cotizaciones (
    idcliente,
    idcolaborador,
    fechahora,
    vigenciadias,
    moneda
  ) VALUES (
    _idcliente,
    _idcolaborador,
    _fechahora,
    _vigenciadias,
    _moneda
  );
  SELECT LAST_INSERT_ID() AS idcotizacion;
END $$

-- 11) PROCEDIMIENTO PARA REGISTRAR DETALLE DE COTIZACIÓN
DROP PROCEDURE IF EXISTS spuInsertDetalleCotizacion $$
CREATE PROCEDURE spuInsertDetalleCotizacion (
  IN _idcotizacion INT,
  IN _idproducto   INT,
  IN _cantidad     INT,
  IN _precio       DECIMAL(7,2),
  IN _descuento    DECIMAL(5,2)
)
BEGIN
  INSERT INTO detallecotizacion (
    idproducto,
    idcotizacion,
    cantidad,
    precio,
    descuento
  ) VALUES (
    _idproducto,
    _idcotizacion,
    _cantidad,
    _precio,
    _descuento
  );
END $$

-- 12) PROCEDIMIENTO PARA ANULAR COMPRA (DEVOLUCIÓN DE STOCK)
DROP PROCEDURE IF EXISTS spuDeleteCompra $$
CREATE PROCEDURE spuDeleteCompra (
  IN _idcompra      INT,
  IN _justificacion VARCHAR(255)
)
BEGIN
  DECLARE _idproducto INT;
  DECLARE _cantidad    INT;
  DECLARE _idkardex    INT;
  DECLARE _saldorestante INT;
  DECLARE _done         INT DEFAULT FALSE;

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

    UPDATE compras
    SET estado = FALSE,
        justificacion = _justificacion
    WHERE idcompra = _idcompra;

    OPEN cur;
    read_loop: LOOP
      FETCH cur INTO _idproducto, _cantidad;
      IF _done THEN
        LEAVE read_loop;
      END IF;

      SELECT k.idkardex INTO _idkardex
      FROM kardex k
      WHERE k.idproducto = _idproducto
      LIMIT 1;

      SELECT saldorestante INTO _saldorestante
      FROM movimientos
      WHERE idkardex = _idkardex
      ORDER BY idmovimiento DESC
      LIMIT 1;

      SET _saldorestante = IFNULL(_saldorestante, 0) - _cantidad;

      INSERT INTO movimientos (
        idkardex,
        idtipomov,
        fecha,
        cantidad,
        saldorestante
      ) VALUES (
        _idkardex,
        (SELECT idtipomov 
           FROM tipomovimientos 
          WHERE flujo = 'salida' 
            AND tipomov = 'devolucion' 
          LIMIT 1),
        CURDATE(),
        _cantidad,
        _saldorestante
      );
    END LOOP;
    CLOSE cur;

  COMMIT;
END $$

-- 13) PROCEDIMIENTO PARA ANULAR VENTA (DEVOLUCIÓN DE STOCK)
DROP PROCEDURE IF EXISTS spuDeleteVenta $$
CREATE PROCEDURE spuDeleteVenta (
  IN _idventa       INT,
  IN _justificacion VARCHAR(255)
)
BEGIN
  DECLARE _idproducto INT;
  DECLARE _cantidad    INT;
  DECLARE _idkardex    INT;
  DECLARE _saldorestante INT;
  DECLARE _done         INT DEFAULT FALSE;
  DECLARE v_fechahora   DATETIME;

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

    SELECT fechahora INTO v_fechahora
    FROM ventas
    WHERE idventa = _idventa;

    UPDATE ventas
    SET estado = FALSE,
        justificacion = _justificacion
    WHERE idventa = _idventa;

    UPDATE ordenservicios
    SET estado = 'I'
    WHERE idcliente = (SELECT idcliente FROM ventas WHERE idventa = _idventa)
      AND DATE(fechaingreso) = DATE(v_fechahora);

    OPEN cur;
    read_loop: LOOP
      FETCH cur INTO _idproducto, _cantidad;
      IF _done THEN
        LEAVE read_loop;
      END IF;

      SELECT k.idkardex INTO _idkardex
      FROM kardex k
      WHERE k.idproducto = _idproducto
      LIMIT 1;

      SELECT saldorestante INTO _saldorestante
      FROM movimientos
      WHERE idkardex = _idkardex
      ORDER BY idmovimiento DESC
      LIMIT 1;

      SET _saldorestante = IFNULL(_saldorestante, 0) + _cantidad;

      INSERT INTO movimientos (
        idkardex,
        idtipomov,
        fecha,
        cantidad,
        saldorestante
      ) VALUES (
        _idkardex,
        (SELECT idtipomov 
           FROM tipomovimientos 
          WHERE flujo = 'entrada' 
            AND tipomov = 'devolucion' 
          LIMIT 1),
        CURDATE(),
        _cantidad,
        _saldorestante
      );
    END LOOP;
    CLOSE cur;

  COMMIT;
END $$

-- 14) PROCEDIMIENTO PARA ELIMINAR COTIZACIÓN
DROP PROCEDURE IF EXISTS spuDeleteCotizacion $$
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
    UPDATE cotizaciones
    SET estado = FALSE,
        justificacion = _justificacion
    WHERE idcotizacion = _idcotizacion;
  COMMIT;
END $$

-- 15) PROCEDIMIENTO PARA LISTAR ORDENES DE TRABAJO POR PERIODO
DROP PROCEDURE IF EXISTS spListOTPorPeriodo $$
CREATE PROCEDURE spListOTPorPeriodo(
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
    v.idventa             AS id,
    v.idpropietario       AS idpropietario,
    COALESCE(
      CONCAT(po_p.apellidos,' ',po_p.nombres),
      po_e.nomcomercial,
      'Sin propietario'
    ) AS propietario,
    COALESCE(
      CONCAT(p.apellidos,' ',p.nombres),
      e.nomcomercial,
      'Cliente anónimo'
    ) AS cliente,
    v.tipocom,
    v.numserie,
    v.numcom,
    DATE_FORMAT(v.fechahora, '%Y-%m-%d %H:%i') AS fechahora,
    vt.total_pendiente,
    CASE
      WHEN vt.total_pendiente = 0 THEN 'pagado'
      ELSE 'pendiente'
    END AS estado_pago
  FROM ventas v
  LEFT JOIN vista_saldos_por_venta vt ON v.idventa = vt.idventa
  LEFT JOIN clientes c       ON v.idcliente  = c.idcliente
  LEFT JOIN personas p       ON c.idpersona  = p.idpersona
  LEFT JOIN empresas e       ON c.idempresa  = e.idempresa
  LEFT JOIN clientes po_c    ON v.idpropietario = po_c.idcliente
  LEFT JOIN personas po_p    ON po_c.idpersona   = po_p.idpersona
  LEFT JOIN empresas po_e    ON po_c.idempresa   = po_e.idempresa
  WHERE DATE(v.fechahora) BETWEEN start_date AND end_date
    AND v.estado  = TRUE
    AND v.tipocom = 'orden de trabajo'
  ORDER BY v.fechahora;
END $$

-- 16) PROCEDIMIENTO PARA LISTAR VENTAS POR PERIODO
DROP PROCEDURE IF EXISTS spListVentasPorPeriodo $$
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
    v.idventa AS id,
    COALESCE(
      CASE
        WHEN pc.idempresa IS NOT NULL THEN ep.nomcomercial
        WHEN pc.idpersona IS NOT NULL THEN CONCAT(pp.nombres, ' ', pp.apellidos)
      END,
      'Sin propietario'
    ) AS propietario,
    COALESCE(
      CONCAT(p.apellidos, ' ', p.nombres),
      e.nomcomercial,
      'Cliente anónimo'
    ) AS cliente,
    v.tipocom,
    v.numcom,
    vt.total_pendiente,
    CASE 
      WHEN vt.total_pendiente = 0 THEN 'pagado' 
      ELSE 'pendiente' 
    END AS estado_pago
  FROM ventas v
  LEFT JOIN clientes       pc  ON v.idpropietario = pc.idcliente
  LEFT JOIN personas       pp  ON pc.idpersona     = pp.idpersona
  LEFT JOIN empresas       ep  ON pc.idempresa     = ep.idempresa
  LEFT JOIN clientes       c   ON v.idcliente      = c.idcliente
  LEFT JOIN personas       p   ON c.idpersona      = p.idpersona
  LEFT JOIN empresas       e   ON c.idempresa      = e.idempresa
  LEFT JOIN vista_saldos_por_venta vt ON v.idventa       = vt.idventa
  WHERE DATE(v.fechahora) BETWEEN start_date AND end_date
    AND v.estado = TRUE
    AND v.tipocom IN ('boleta','factura')
  ORDER BY v.fechahora;
END $$

-- 17) PROCEDIMIENTO PARA LISTAR COMPRAS POR PERIODO
DROP PROCEDURE IF EXISTS spListComprasPorPeriodo $$
CREATE PROCEDURE spListComprasPorPeriodo(
  IN _modo  ENUM('semana','mes','dia'),
  IN _fecha DATE
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
END $$

-- 18) PROCEDIMIENTO PARA LISTAR COTIZACIONES POR PERIODO
DROP PROCEDURE IF EXISTS spListCotizacionesPorPeriodo $$
CREATE PROCEDURE spListCotizacionesPorPeriodo(
  IN _modo   ENUM('dia','semana','mes'),
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
    v.idcotizacion AS id,
    v.cliente,
    SUM(v.precio) AS total,
    v.vigencia,
    CASE
      WHEN DATE_ADD(DATE(v.fechahora), INTERVAL v.vigencia DAY) >= CURRENT_DATE()
        THEN 'vigente'
      ELSE 'expirada'
    END AS estado_vigencia,
    DATE(v.fechahora) AS fecha
  FROM vs_cotizaciones v
  WHERE DATE(v.fechahora) BETWEEN start_date AND end_date
  GROUP BY v.idcotizacion, v.cliente, v.vigencia, DATE(v.fechahora)
  ORDER BY DATE(v.fechahora);
END $$

DELIMITER ;

/* PRIMER SPU
DROP PROCEDURE IF EXISTS spRegisterVentaConOrden;
DELIMITER $$

CREATE PROCEDURE spRegisterVentaConOrden (
  IN _conOrden        BOOLEAN,
  IN _idadmin         INT,
  IN _idpropietario   INT,
  IN _idcliente       INT,
  IN _idvehiculo      INT,
  IN _kilometraje     DECIMAL(10,2),
  IN _observaciones   VARCHAR(255),
  IN _ingresogrua     BOOLEAN,
  IN _fechaingreso    DATETIME,
  IN _tipocom         ENUM('boleta','factura','orden de trabajo'),
  IN _fechahora       DATETIME,
  IN _numserie        VARCHAR(10),
  IN _numcom          VARCHAR(10),
  IN _moneda          VARCHAR(20),
  IN _idcolaborador   INT
)
BEGIN
  DECLARE v_idorden INT DEFAULT NULL;
  DECLARE v_idventa INT DEFAULT NULL;
  DECLARE v_fechaing DATETIME;
  DECLARE v_idexpediente_ot INT DEFAULT NULL;

  SET v_fechaing = COALESCE(_fechaingreso, _fechahora);

  -- 1) Si es orden de trabajo, crear expediente_ot
  IF _tipocom = 'orden de trabajo' THEN
    INSERT INTO expediente_ot (
      idcliente,
      idvehiculo
    ) VALUES (
      NULLIF(_idcliente,0),
      NULLIF(_idvehiculo,0)
    );
    SET v_idexpediente_ot = LAST_INSERT_ID();
  END IF;

  -- 2) Inserta orden de servicio si corresponde
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
      NULLIF(_idcliente, 0),
      NULLIF(_idvehiculo, 0),
      _kilometraje,
      _observaciones,
      _ingresogrua,
      v_fechaing,
      NULL,
      'A'
    );
    SET v_idorden = LAST_INSERT_ID();
  END IF;

  -- 3) Inserta venta incluyendo idexpediente_ot si lo creamos
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
    idexpediente_ot,
    kilometraje,
    justificacion,
    estado
  ) VALUES (
    NULLIF(_idcliente,0),
    _idpropietario,
    _idcolaborador,
    NULLIF(_idvehiculo,0),
    _tipocom,
    _fechahora,
    _numserie,
    _numcom,
    _moneda,
    v_idexpediente_ot,
    NULLIF(_kilometraje,0),
    NULL,
    TRUE
  );
  SET v_idventa = LAST_INSERT_ID();
  -- 4) Devuelve IDs
  SELECT v_idventa AS idventa,
         v_idorden AS idorden;
END$$
DELIMITER ;
*/
-- SIN ACTUALIZAR EL PRECIO DEL PRODUCTO
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

/*
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
DELIMITER ;*/

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
END$$*/

/*DROP PROCEDURE IF EXISTS spListOTPorPeriodo;
DELIMITER $$
CREATE PROCEDURE spListOTPorPeriodo(
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
    v.idventa           AS id,             -- Mantén el id de la venta
    v.idpropietario     AS idpropietario,
    COALESCE(CONCAT(p.apellidos,' ',p.nombres), e.nomcomercial, 'Cliente anónimo') AS cliente,
    v.tipocom,
    v.numserie,
    v.numcom,
    DATE_FORMAT(v.fechahora, '%Y-%m-%d %H:%i') AS fechahora,
    vt.total_pendiente,
    CASE WHEN vt.total_pendiente = 0 THEN 'pagado' ELSE 'pendiente' END AS estado_pago,
    COALESCE(CONCAT(po_p.apellidos,' ',po_p.nombres), po_e.nomcomercial, 'Sin propietario') AS propietario
  FROM ventas v

  LEFT JOIN clientes c      ON v.idcliente      = c.idcliente
  LEFT JOIN personas p      ON c.idpersona      = p.idpersona
  LEFT JOIN empresas e      ON c.idempresa      = e.idempresa
  LEFT JOIN vista_saldos_por_venta vt ON v.idventa = vt.idventa

  LEFT JOIN propietarios pr ON v.idpropietario = pr.idpropietario
  LEFT JOIN clientes po_c  ON pr.idcliente = po_c.idcliente
  LEFT JOIN personas po_p  ON po_c.idpersona = po_p.idpersona
  LEFT JOIN empresas po_e  ON po_c.idempresa = po_e.idempresa

  WHERE DATE(v.fechahora) BETWEEN start_date AND end_date
    AND v.estado  = TRUE
    AND v.tipocom = 'orden de trabajo'
  ORDER BY v.fechahora;
END$$
DELIMITER ;*/


/*DROP PROCEDURE IF EXISTS spListVentasPorPeriodo;
DELIMITER $$
CREATE PROCEDURE spListVentasPorPeriodo(
  IN _modo   ENUM('semana','mes','dia'),
  IN _fecha  DATE
)
BEGIN
  DECLARE start_date DATE;
  DECLARE end_date   DATE;
  -- Calcular rango segun modo
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
    CASE 
      WHEN vt.total_pendiente = 0 THEN 'pagado' 
      ELSE 'pendiente' 
    END AS estado_pago
  FROM ventas v
  LEFT JOIN clientes c 
    ON v.idcliente = c.idcliente
  LEFT JOIN personas p 
    ON c.idpersona = p.idpersona
  LEFT JOIN empresas e 
    ON c.idempresa = e.idempresa
  LEFT JOIN vista_saldos_por_venta vt 
    ON v.idventa = vt.idventa
  WHERE DATE(v.fechahora) BETWEEN start_date AND end_date
    AND v.estado = TRUE
    -- excluyo OT, solo boleta y factura:
    AND v.tipocom IN ('boleta','factura')
  ORDER BY v.fechahora;
END$$
DELIMITER ;*/


/*DROP PROCEDURE IF EXISTS spListVentasPorPeriodo;
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
LEFT JOIN clientes c ON v.idcliente = c.idcliente
    LEFT JOIN personas p ON c.idpersona  = p.idpersona
    LEFT JOIN empresas e ON c.idempresa  = e.idempresa
LEFT JOIN vista_saldos_por_venta vt ON v.idventa = vt.idventa
  WHERE DATE(v.fechahora) BETWEEN start_date AND end_date
    AND v.estado = TRUE
  ORDER BY v.fechahora;
END$$*/

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

-- PRIMER INTENTO
/*DROP PROCEDURE IF EXISTS spRegisterVentaConOrden;
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
  IN _tipocom ENUM('boleta','factura','orden de trabajo'),
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
	NULLIF(_idcliente,0),       -- convierte 0 en NULL
	_idpropietario,   -- convierte 0 en NULL
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
*/

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
