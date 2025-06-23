USE dbfix360;
DELIMITER $$

-- TRAER EL KILOMETRAJE
DROP PROCEDURE IF EXISTS spGetUltimoKilometraje $$
CREATE PROCEDURE spGetUltimoKilometraje(
  IN  p_idvehiculo INT
)
BEGIN
  -- Obtenemos de forma unificada todos los registros de kilometraje
  -- (de ordenservicios y de ventas), luego tomamos el más reciente.
  SELECT
    k.kilometraje AS ultimo_kilometraje,
    k.fecha_registro AS fecha
  FROM (
    -- Kilometraje registrado en orden de servicio
    SELECT
      kilometraje,
      fechaingreso AS fecha_registro
    FROM ordenservicios
    WHERE idvehiculo = p_idvehiculo

    UNION ALL

    -- Kilometraje registrado en venta
    SELECT
      kilometraje,
      fechahora AS fecha_registro
    FROM ventas
    WHERE idvehiculo = p_idvehiculo
  ) AS k

  ORDER BY k.fecha_registro DESC
  LIMIT 1;
END$$

-- 1) PROCEDIMIENTO PARA REGISTRAR EMPRESA
DROP PROCEDURE IF EXISTS spRegisterEmpresaProveedor $$
CREATE PROCEDURE spRegisterEmpresaProveedor (
  IN _nomcomercial VARCHAR(80),
  IN _razonsocial  VARCHAR(80),
  IN _telefono     VARCHAR(20),
  IN _correo       VARCHAR(100),
  IN _ruc          CHAR(11)
)
BEGIN
  DECLARE new_idempresa INT;
  DECLARE new_idproveedor INT;

  -- 1) Insertamos primero en la tabla empresas
  INSERT INTO empresas (nomcomercial, razonsocial, telefono, correo, ruc)
    VALUES (_nomcomercial, _razonsocial, _telefono, _correo, _ruc);

  SET new_idempresa = LAST_INSERT_ID();

  -- 2) A continuación insertamos en proveedores usando el id que acabamos de generar
  INSERT INTO proveedores (idempresa)
    VALUES (new_idempresa);

  SET new_idproveedor = LAST_INSERT_ID();

  -- 3) Finalmente devolvemos ambos IDs en una sola fila
  SELECT new_idempresa   AS idempresa,
         new_idproveedor AS idproveedor;
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
      _idvehiculo
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
      _idvehiculo,
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
  IN _descuento          DECIMAL(5,2),
  IN _registrarMvto      BOOLEAN  -- <--- nuevo flag
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
  -- 2) Actualizar precio en productos
  UPDATE productos SET preciov = _precioventa
    WHERE idproducto = _idproducto;

	IF _registrarMvto THEN
    -- 3) Obtener idkardex y tipo de movimiento
    SELECT idkardex INTO _idkardex FROM kardex WHERE idproducto = _idproducto LIMIT 1;
    SELECT idtipomov INTO _idtipomov
      FROM tipomovimientos
      WHERE flujo = 'salida' AND tipomov = 'venta'
      LIMIT 1;

    -- 4.1) Validar cantidad válida (> 0)
    IF _cantidad <= 0 THEN
      SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'Error: La cantidad del movimiento debe ser mayor que cero.';
    END IF;

    -- 4.2) Calcular saldo restante
    SET _saldoNuevo = calcularSaldoRestante(_idkardex, _cantidad);

    -- 4.3) Validar stock suficiente
    IF _saldoNuevo < 0 THEN
      SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'Error: Stock insuficiente para registrar la venta.';
    END IF;

    -- 4.4) Insertar movimiento
    INSERT INTO movimientos (
      idkardex, idtipomov, fecha,
      cantidad, preciounit, saldorestante
    ) VALUES (
      _idkardex, _idtipomov, CURDATE(),
      _cantidad, _precioventa, _saldoNuevo
    );
  END IF;
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
DELIMITER $$
CREATE PROCEDURE buscar_producto(
  IN termino_busqueda VARCHAR(255),
  IN modo ENUM('venta','compra')
)
BEGIN
  SELECT
    P.idproducto,
    CONCAT(S.subcategoria, ' ', P.descripcion) AS subcategoria_producto,
    CASE
      WHEN modo = 'venta'  THEN P.preciov
      WHEN modo = 'compra' THEN P.precioc
      ELSE NULL
    END AS precio,
    IFNULL((
  SELECT m2.saldorestante
	  FROM movimientos m2
	  WHERE m2.idkardex = k.idkardex
	  ORDER BY m2.idmovimiento DESC
	  LIMIT 1
	), 0) AS stock
  FROM productos P
  JOIN subcategorias S ON P.idsubcategoria = S.idsubcategoria
  LEFT JOIN kardex k    ON P.idproducto = k.idproducto
  WHERE S.subcategoria LIKE CONCAT('%', termino_busqueda, '%')
     OR P.descripcion   LIKE CONCAT('%', termino_busqueda, '%')
     OR P.codigobarra   LIKE CONCAT('%', termino_busqueda, '%')
  LIMIT 10;
END $$
-- CALL buscar_producto('prueba', 'venta');

DROP PROCEDURE IF EXISTS buscar_producto_cot $$
DELIMITER $$
CREATE PROCEDURE buscar_producto_cot(
  IN termino_busqueda VARCHAR(255)
)
BEGIN
  SELECT
    P.idproducto,
    CONCAT(S.subcategoria, ' ', P.descripcion) AS subcategoria_producto,
    P.preciov,
    IFNULL((
      SELECT m2.saldorestante
      FROM movimientos m2
      WHERE m2.idkardex = k.idkardex
      ORDER BY m2.idmovimiento DESC
      LIMIT 1
    ), 0) AS stock
  FROM productos P
  JOIN subcategorias S ON P.idsubcategoria = S.idsubcategoria
  LEFT JOIN kardex k   ON P.idproducto = k.idproducto
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
DELIMITER $$
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

  -- 1. Insertar detalle de la compra
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

  -- 2. Actualizar el precio de compra del producto
  UPDATE productos
    SET precioc = _preciocompra
  WHERE idproducto = _idproducto;

  -- 3. Verificar si existe el kardex para ese producto
  SELECT idkardex
    INTO _idkardex
  FROM kardex
  WHERE idproducto = _idproducto
  LIMIT 1;

  -- 4. Si no existe, crearlo
  IF _idkardex IS NULL THEN
    INSERT INTO kardex (idproducto) VALUES (_idproducto);
    SET _idkardex = LAST_INSERT_ID();
  END IF;

  -- 5. Obtener saldo restante anterior
  SELECT saldorestante
    INTO _saldorestante
  FROM movimientos
  WHERE idkardex = _idkardex
  ORDER BY idmovimiento DESC
  LIMIT 1;

  -- Si no hay movimientos previos, el saldo comienza en 0
  SET _saldorestante = IFNULL(_saldorestante, 0) + _cantidad;

  -- 6. Registrar movimiento de entrada (compra)
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
     IN _idservicio   INT,
     IN _cantidad     INT,
     IN _precio       DECIMAL(7,2),
     IN _descuento    DECIMAL(5,2)
)
BEGIN
   INSERT INTO detallecotizacion (
     idcotizacion,
     idproducto,
     idservicio,
     cantidad,
     precio,
     descuento
   ) VALUES (
     _idcotizacion,
     _idproducto,
     _idservicio,
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

    -- Obtener fecha/hora de la venta
    SELECT fechahora INTO v_fechahora
    FROM ventas
    WHERE idventa = _idventa;

    -- Anular la venta
    UPDATE ventas
    SET estado = FALSE,
        justificacion = _justificacion
    WHERE idventa = _idventa;

    -- Anular orden de servicio
    UPDATE ordenservicios
    SET estado = 'I'
    WHERE idcliente = (SELECT idcliente FROM ventas WHERE idventa = _idventa)
      AND DATE(fechaingreso) = DATE(v_fechahora);

    -- Eliminar amortización asociada
    DELETE FROM amortizaciones
    WHERE idventa = _idventa;

    -- Devolver productos al stock
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
	C.numserie,
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

DROP PROCEDURE IF EXISTS ev_MarcarCotizacionesVencidas $$
CREATE EVENT IF NOT EXISTS ev_MarcarCotizacionesVencidas
  ON SCHEDULE EVERY 1 DAY
  STARTS CONCAT(CURDATE(), ' 00:00:00')
  DO
BEGIN
  UPDATE cotizaciones
  SET vencida = TRUE
  WHERE vencida = FALSE
    AND estado = TRUE
    AND fecha_expiracion <= CURRENT_DATE();
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
    d.idcotizacion   AS id,
    d.cliente,
    -- Sumamos el total de productos y servicios
    SUM(
      COALESCE(d.total_linea,    0)
      + COALESCE(d.precio_servicio,0)
    )                AS total,
    d.vigenciadias  AS vigencia,
    CASE
      WHEN DATE_ADD(DATE(d.fecha), INTERVAL d.vigenciadias DAY) >= CURRENT_DATE()
        THEN 'vigente'
      ELSE 'expirada'
    END             AS estado_vigencia,
    DATE(d.fecha)   AS fecha
  FROM vista_detalle_cotizacion_pdf d
  WHERE DATE(d.fecha) BETWEEN start_date AND end_date
  GROUP BY
    d.idcotizacion,
    d.cliente,
    d.vigenciadias,
    DATE(d.fecha)
  ORDER BY DATE(d.fecha);
END $$

DELIMITER ;
