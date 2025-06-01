/* SOLO PRUEBA
DROP VIEW IF EXISTS vista_detalle_venta;
CREATE VIEW vista_detalle_venta AS
SELECT
  v.idventa,
  v.fechahora,
  COALESCE(
    CASE 
      WHEN propc.idempresa IS NOT NULL THEN epc.nomcomercial
      WHEN propc.idpersona IS NOT NULL THEN CONCAT(ppc.nombres,' ',ppc.apellidos)
    END,
    'Sin propietario'
  ) AS propietario,
  COALESCE(CONCAT(p.apellidos,' ',p.nombres), e.nomcomercial) AS cliente,
  v.kilometraje,
  CONCAT(tv.tipov,' ',ma.nombre,' ',vh.color,' (',vh.placa,')') AS vehiculo,
  CONCAT(su.subcategoria,' ', pr.descripcion) AS producto,
  dv.cantidad,
  dv.precioventa         AS precio,
  dv.descuento,
  ROUND((dv.precioventa - dv.descuento) * dv.cantidad, 2) AS total_producto,
  NULL                   AS tiposervicio,
  NULL                   AS nombreservicio,
  NULL                   AS mecanico,
  NULL                   AS precio_servicio,
  'producto'             AS registro_tipo
FROM ventas v
  -- Propietario
  LEFT JOIN propietarios prop     ON v.idpropietario = prop.idpropietario
  LEFT JOIN clientes propc       ON prop.idcliente   = propc.idcliente
  LEFT JOIN empresas epc         ON propc.idempresa  = epc.idempresa
  LEFT JOIN personas ppc         ON propc.idpersona  = ppc.idpersona
  -- Cliente
  LEFT JOIN clientes cte         ON v.idcliente      = cte.idcliente
  LEFT JOIN personas p           ON cte.idpersona    = p.idpersona
  LEFT JOIN empresas e           ON cte.idempresa    = e.idempresa
  -- Vehículo
  LEFT JOIN vehiculos vh         ON v.idvehiculo     = vh.idvehiculo
  LEFT JOIN modelos m            ON vh.idmodelo      = m.idmodelo
  LEFT JOIN tipovehiculos tv     ON m.idtipov        = tv.idtipov
  LEFT JOIN marcas ma            ON m.idmarca        = ma.idmarca
  -- Detalle de productos
  JOIN detalleventa dv           ON v.idventa        = dv.idventa
  JOIN productos pr              ON dv.idproducto    = pr.idproducto
  JOIN subcategorias su          ON pr.idsubcategoria = su.idsubcategoria
WHERE v.estado = TRUE

UNION ALL

-- === BLOQUE de SERVICIOS ===
SELECT
  v.idventa,
  v.fechahora,
  COALESCE(
    CASE 
      WHEN propc.idempresa IS NOT NULL THEN epc.nomcomercial
      WHEN propc.idpersona IS NOT NULL THEN CONCAT(ppc.nombres,' ',ppc.apellidos)
    END,
    'Sin propietario'
  ) AS propietario,
  COALESCE(CONCAT(p.apellidos,' ',p.nombres), e.nomcomercial) AS cliente,
  v.kilometraje,
  CONCAT(tv.tipov,' ',ma.nombre,' ',vh.color,' (',vh.placa,')') AS vehiculo,
  NULL                   AS producto,
  NULL                   AS cantidad,
  NULL                   AS precio,
  NULL                   AS descuento,
  NULL                   AS total_producto,
  sc.subcategoria        AS tiposervicio,
  se.servicio            AS nombreservicio,
  col.namuser            AS mecanico,
  dos.precio             AS precio_servicio,
  'servicio'             AS registro_tipo
FROM ventas v
  -- Propietario
  LEFT JOIN propietarios prop     ON v.idpropietario = prop.idpropietario
  LEFT JOIN clientes propc       ON prop.idcliente   = propc.idcliente
  LEFT JOIN empresas epc         ON propc.idempresa  = epc.idempresa
  LEFT JOIN personas ppc         ON propc.idpersona  = ppc.idpersona
  -- Cliente
  LEFT JOIN clientes cte         ON v.idcliente      = cte.idcliente
  LEFT JOIN personas p           ON cte.idpersona    = p.idpersona
  LEFT JOIN empresas e           ON cte.idempresa    = e.idempresa
  -- Vehículo
  LEFT JOIN vehiculos vh         ON v.idvehiculo     = vh.idvehiculo
  LEFT JOIN modelos m            ON vh.idmodelo      = m.idmodelo
  LEFT JOIN tipovehiculos tv     ON m.idtipov        = tv.idtipov
  LEFT JOIN marcas ma            ON m.idmarca        = ma.idmarca
  -- Unir directamente a la OT asociada a la venta
  INNER JOIN ordenservicios os      ON v.idexpediente_ot = os.idorden
  INNER JOIN detalleordenservicios dos ON os.idorden    = dos.idorden
  INNER JOIN servicios se           ON dos.idservicio    = se.idservicio
  INNER JOIN subcategorias sc       ON se.idsubcategoria = sc.idsubcategoria
  INNER JOIN colaboradores col      ON dos.idmecanico    = col.idcolaborador
WHERE v.estado = TRUE
  AND v.idexpediente_ot IS NOT NULL;-- filtrar para que no aparezca fila vacía de servicio
*/

-- PRUEBA

DROP PROCEDURE IF EXISTS spRegisterVentaConOrden;
DELIMITER $$
CREATE PROCEDURE spRegisterVentaConOrden (
  IN _conOrden        BOOLEAN,                -- ¿Crear o no OT?
  IN _idadmin         INT,                    -- id del colaborador que administra la OT
  IN _idpropietario   INT,                    -- idpropietario (propietario del vehículo)
  IN _idcliente       INT,                    -- idcliente final (puede ser 0 o NULL)
  IN _idvehiculo      INT,                    -- idvehículo (puede ser 0 o NULL)
  IN _kilometraje     DECIMAL(10,2),          -- lectura de km
  IN _observaciones   VARCHAR(255),           -- texto libre de observaciones
  IN _ingresogrua     BOOLEAN,                -- si ingresó con grúa o no
  IN _fechaingreso    DATETIME,               -- fecha/hora en que ingresa la OT
  IN _tipocom         ENUM('boleta','factura','orden de trabajo'),
  IN _fechahora       DATETIME,
  IN _numserie        VARCHAR(10),
  IN _numcom          VARCHAR(10),
  IN _moneda          VARCHAR(20),
  IN _idcolaborador   INT                     -- id del mecánico que hace el trabajo
)
BEGIN
  DECLARE v_idorden INT            DEFAULT NULL;
  DECLARE v_idventa INT            DEFAULT NULL;
  DECLARE v_fechaing DATETIME;
  DECLARE v_idexpediente_ot INT    DEFAULT NULL;

  -- Si pasaste fechaingreso = NULL, toma la hora de la venta
  SET v_fechaing = COALESCE(_fechaingreso, _fechahora);

  -- 1) Si es 'orden de trabajo', crear expediente_ot
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

  -- 2) Si pediste crear una OT (_conOrden = TRUE), insertar en ordenservicios
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
    -- Aquí: guardamos el id de la OT en v_idorden y en v_idexpediente_ot
    SET v_idorden = LAST_INSERT_ID();
    SET v_idexpediente_ot = v_idorden;  
    -- (De esta forma, incluso si tipocom = 'boleta', la venta apuntará a esta OT)
  END IF;

  -- 3) Insertar en ventas, guardando idexpediente_ot (si lo creamos arriba)
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
    v_idexpediente_ot,       -- ← aquí va la OT que creamos (o NULL si no hubo OT)
    NULLIF(_kilometraje, 0),
    NULL,
    TRUE
  );
  SET v_idventa = LAST_INSERT_ID();

  -- 4) Devolver los IDs creados
  SELECT v_idventa AS idventa,
         v_idorden AS idorden;
END $$
DELIMITER ;


/*
DROP PROCEDURE IF EXISTS spListOTPorPeriodo;
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
    v.idventa             AS id,
    v.idpropietario       AS idpropietario,
    COALESCE(CONCAT(p.apellidos,' ',p.nombres), e.nomcomercial, 'Cliente anónimo') AS cliente,
    v.tipocom,
    v.numserie,
    v.numcom,
    DATE_FORMAT(v.fechahora, '%Y-%m-%d %H:%i') AS fechahora,
    vt.total_pendiente,
    CASE
      WHEN vt.total_pendiente = 0 THEN 'pagado'
      ELSE 'pendiente'
    END AS estado_pago,
    COALESCE(CONCAT(po_p.apellidos,' ',po_p.nombres), po_e.nomcomercial, 'Sin propietario') AS propietario
  FROM ventas v

  -- Obtener solo quienes tengan tipocom = 'orden de trabajo'
  INNER JOIN (
    SELECT idexpediente_ot
      FROM ventas
     WHERE tipocom = 'orden de trabajo'
  ) AS only_ot
    ON v.idexpediente_ot = only_ot.idexpediente_ot

  LEFT JOIN clientes c       ON v.idcliente      = c.idcliente
  LEFT JOIN personas p       ON c.idpersona      = p.idpersona
  LEFT JOIN empresas e       ON c.idempresa      = e.idempresa
  LEFT JOIN vista_saldos_por_venta vt ON v.idventa = vt.idventa

  LEFT JOIN propietarios pr
    ON v.idpropietario = pr.idpropietario
  LEFT JOIN clientes po_c ON pr.idcliente = po_c.idcliente
  LEFT JOIN personas po_p ON po_c.idpersona = po_p.idpersona
  LEFT JOIN empresas po_e ON po_c.idempresa = po_e.idempresa

  WHERE DATE(v.fechahora) BETWEEN start_date AND end_date
    AND v.estado = TRUE
    AND v.tipocom = 'orden de trabajo'
  ORDER BY v.fechahora;
END $$
DELIMITER ;
*/

/*
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
      NULLIF(_idcliente,0),
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
DELIMITER ;*/
/*select * from expediente_ot;*/
-- 1) Tabla de Expedientes OT
CREATE TABLE expediente_ot (
  idexpediente_ot   INT           PRIMARY KEY AUTO_INCREMENT,
  idcliente         INT           NULL,
  idvehiculo        INT           NULL,
  idcotizacion      INT           NULL,       -- opcional: enlazar cotización previa
  fecha_apertura    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  estado            ENUM('ABIERTA','CERRADA') NOT NULL DEFAULT 'ABIERTA',
  total_estimado    DECIMAL(10,2) NULL,       -- presupuesto inicial
  creado            TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  modificado        TIMESTAMP     NOT NULL 
                     DEFAULT CURRENT_TIMESTAMP 
                     ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_exp_ot_cliente   FOREIGN KEY (idcliente)  REFERENCES clientes(idcliente),
  CONSTRAINT fk_exp_ot_vehiculo  FOREIGN KEY (idvehiculo) REFERENCES vehiculos(idvehiculo),
  CONSTRAINT fk_exp_ot_cotizacion FOREIGN KEY (idcotizacion) REFERENCES cotizaciones(idcotizacion)
) ENGINE=INNODB;

ALTER TABLE ventas
  ADD COLUMN idexpediente_ot INT NULL AFTER numcom,
  ADD CONSTRAINT fk_venta_expediente_ot
    FOREIGN KEY (idexpediente_ot) REFERENCES expediente_ot(idexpediente_ot),
  -- 3) Validar coherencia entre tipocom y la presencia de expediente_ot
  ADD CONSTRAINT chk_venta_ot
    CHECK (
      (tipocom = 'orden de trabajo' AND idexpediente_ot IS NOT NULL)
      OR
      (tipocom <> 'orden de trabajo' AND idexpediente_ot IS NULL)
    );
/*
-- PRUEBA real (ya esta en spuVentas)
DROP PROCEDURE IF EXISTS spListOTPorPeriodo;
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

  LEFT JOIN propietarios pr
    ON pr.idvehiculo = v.idvehiculo
    AND pr.fechainicio <= DATE(v.fechahora)
    AND (pr.fechafinal IS NULL OR pr.fechafinal >= DATE(v.fechahora))
  LEFT JOIN clientes po_c  ON pr.idcliente = po_c.idcliente
  LEFT JOIN personas po_p  ON po_c.idpersona = po_p.idpersona
  LEFT JOIN empresas po_e  ON po_c.idempresa = po_e.idempresa

  WHERE DATE(v.fechahora) BETWEEN start_date AND end_date
    AND v.estado  = TRUE
    AND v.tipocom = 'orden de trabajo'
  ORDER BY v.fechahora;
END$$
DELIMITER ;
*/
/*
DROP PROCEDURE IF EXISTS spListOTPorPeriodo;
DELIMITER $$
CREATE PROCEDURE spListOTPorPeriodo(
  IN _modo   ENUM('semana','mes','dia'),
  IN _fecha  DATE
)
BEGIN
  DECLARE start_date DATE;
  DECLARE end_date   DATE;

  -- 1) Calcular rango según modo
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

  -- 2) Lista de OT con cliente, saldo y propietario
  SELECT
    v.idventa    AS id,
    -- Cliente que contrató la OT
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
    CASE WHEN vt.total_pendiente = 0 THEN 'pagado' ELSE 'pendiente' END AS estado_pago,
    -- Nuevo: propietario vigente del vehículo al momento de la OT
    COALESCE(
      CONCAT(po_p.apellidos,' ',po_p.nombres),
      po_e.nomcomercial,
      'Sin propietario'
    ) AS propietario
  FROM ventas v
  -- datos de cliente pagador
  LEFT JOIN clientes c      ON v.idcliente      = c.idcliente
  LEFT JOIN personas p      ON c.idpersona      = p.idpersona
  LEFT JOIN empresas e      ON c.idempresa      = e.idempresa

  -- información de saldo
  LEFT JOIN vista_saldos_por_venta vt 
    ON v.idventa = vt.idventa

  -- JOIN para traer al propietario vigente
  LEFT JOIN propietarios pr
    ON pr.idvehiculo = v.idvehiculo
    AND pr.fechainicio <= DATE(v.fechahora)
    AND (pr.fechafinal IS NULL OR pr.fechafinal >= DATE(v.fechahora))
  LEFT JOIN clientes po_c
    ON pr.idcliente = po_c.idcliente
  LEFT JOIN personas po_p
    ON po_c.idpersona = po_p.idpersona
  LEFT JOIN empresas po_e
    ON po_c.idempresa = po_e.idempresa

  WHERE DATE(v.fechahora) BETWEEN start_date AND end_date
    AND v.estado  = TRUE
    AND v.tipocom = 'orden de trabajo'
  ORDER BY v.fechahora;
END$$
DELIMITER ;*/

/*
DROP PROCEDURE IF EXISTS spListOTPorPeriodo;
DELIMITER $$
CREATE PROCEDURE spListOTPorPeriodo(
  IN _modo   ENUM('semana','mes','dia'),
  IN _fecha  DATE
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

  SELECT
    v.idventa    AS id,
    COALESCE(CONCAT(p.apellidos,' ',p.nombres), e.nomcomercial) AS cliente,
    v.tipocom,
    v.numserie,
    v.numcom,
    DATE_FORMAT(v.fechahora, '%Y-%m-%d %H:%i') AS fechahora,
    vt.total_pendiente,
    CASE WHEN vt.total_pendiente = 0 
         THEN 'pagado' 
         ELSE 'pendiente' 
    END AS estado_pago
  FROM ventas v
  LEFT JOIN clientes c      ON v.idcliente      = c.idcliente
  LEFT JOIN personas p      ON c.idpersona      = p.idpersona
  LEFT JOIN empresas e      ON c.idempresa      = e.idempresa
  LEFT JOIN vista_saldos_por_venta vt
                           ON v.idventa        = vt.idventa
  WHERE DATE(v.fechahora) BETWEEN start_date AND end_date
    AND v.estado     = TRUE
    AND v.tipocom    = 'orden de trabajo'
  ORDER BY v.fechahora;
END$$
DELIMITER ;
*/
-- PRUEBA
/*
DROP PROCEDURE IF EXISTS spListVentasPorPeriodo;
DELIMITER $$
CREATE PROCEDURE spListVentasPorPeriodo(
  IN _modo   ENUM('semana','mes','dia'),
  IN _fecha  DATE
)
BEGIN
  DECLARE start_date DATE;
  DECLARE end_date   DATE;

  -- 1) Calcular rango según el modo
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

  -- 2) Consulta UNION ALL: productos + servicios
  SELECT
    v.idventa,
    v.tipocom,
    v.numcom,
    -- propietario
    COALESCE(
      CASE 
        WHEN propc.idempresa IS NOT NULL THEN epc.nomcomercial
        WHEN propc.idpersona IS NOT NULL THEN CONCAT(ppc.nombres,' ',ppc.apellidos)
      END,
      'Sin propietario'
    ) AS propietario,
    -- cliente
    COALESCE(
      CASE
        WHEN cte.idpersona IS NOT NULL THEN CONCAT(p.apellidos,' ',p.nombres)
        WHEN cte.idempresa IS NOT NULL THEN e.nomcomercial
      END,
      'Cliente anónimo'
    ) AS cliente,
    v.fechahora AS fecha,
    v.kilometraje,
    -- vehículo
    CONCAT(tv.tipov,' ',ma.nombre,' ',vh.color,' (',vh.placa,')') AS vehiculo,
    -- PRODUCTOS
    CONCAT(su.subcategoria,' ',pr.descripcion) AS producto,
    dv.cantidad,
    dv.precioventa AS precio,
    dv.descuento,
    ROUND((dv.precioventa - dv.descuento) * dv.cantidad, 2) AS total_producto,
    -- CAMPOS VACÍOS PARA SERVICIOS
    NULL AS tiposervicio,
    NULL AS nombreservicio,
    NULL AS mecanico,
    NULL AS precio_servicio,
    'producto' AS registro_tipo
  FROM ventas v
  LEFT JOIN propietarios prop       ON v.idpropietario = prop.idpropietario
  LEFT JOIN clientes propc          ON prop.idcliente   = propc.idcliente
  LEFT JOIN empresas epc            ON propc.idempresa  = epc.idempresa
  LEFT JOIN personas ppc            ON propc.idpersona  = ppc.idpersona

  LEFT JOIN clientes cte            ON v.idcliente      = cte.idcliente
  LEFT JOIN personas p              ON cte.idpersona    = p.idpersona
  LEFT JOIN empresas e              ON cte.idempresa    = e.idempresa

  LEFT JOIN vehiculos vh            ON v.idvehiculo     = vh.idvehiculo
  LEFT JOIN modelos m               ON vh.idmodelo      = m.idmodelo
  LEFT JOIN tipovehiculos tv        ON m.idtipov        = tv.idtipov
  LEFT JOIN marcas ma               ON m.idmarca        = ma.idmarca

  JOIN detalleventa dv              ON dv.idventa       = v.idventa
  JOIN productos pr                 ON pr.idproducto    = dv.idproducto
  JOIN subcategorias su             ON su.idsubcategoria= pr.idsubcategoria

  WHERE v.estado = TRUE
    AND v.tipocom IN ('boleta','factura')
    AND DATE(v.fechahora) BETWEEN start_date AND end_date

  UNION ALL

  SELECT
    v.idventa,
    v.tipocom,
    v.numcom    AS numcomp,
    -- propietario (mismo join)
    COALESCE(
      CASE 
        WHEN propc.idempresa IS NOT NULL THEN epc.nomcomercial
        WHEN propc.idpersona IS NOT NULL THEN CONCAT(ppc.nombres,' ',ppc.apellidos)
      END,
      'Sin propietario'
    ) AS propietario,
    -- cliente
    COALESCE(
      CASE
        WHEN cte.idpersona IS NOT NULL THEN CONCAT(p.apellidos,' ',p.nombres)
        WHEN cte.idempresa IS NOT NULL THEN e.nomcomercial
      END,
      'Cliente anónimo'
    ) AS cliente,
    v.fechahora AS fecha,
    v.kilometraje,
    -- vehículo
    CONCAT(tv.tipov,' ',ma.nombre,' ',vh.color,' (',vh.placa,')') AS vehiculo,
    -- CAMPOS VACÍOS PARA PRODUCTOS
    NULL AS producto,
    NULL AS cantidad,
    NULL AS precio,
    NULL AS descuento,
    NULL AS total_producto,
    -- SERVICIOS
    sc.subcategoria      AS tiposervicio,
    se.servicio          AS nombreservicio,
    col.namuser          AS mecanico,
    dos.precio           AS precio_servicio,
    'servicio'           AS registro_tipo
  FROM ventas v
  LEFT JOIN propietarios prop       ON v.idpropietario = prop.idpropietario
  LEFT JOIN clientes propc          ON prop.idcliente   = propc.idcliente
  LEFT JOIN empresas epc            ON propc.idempresa  = epc.idempresa
  LEFT JOIN personas ppc            ON propc.idpersona  = ppc.idpersona

  LEFT JOIN clientes cte            ON v.idcliente      = cte.idcliente
  LEFT JOIN personas p              ON cte.idpersona    = p.idpersona
  LEFT JOIN empresas e              ON cte.idempresa    = e.idempresa

  LEFT JOIN vehiculos vh            ON v.idvehiculo     = vh.idvehiculo
  LEFT JOIN modelos m               ON vh.idmodelo      = m.idmodelo
  LEFT JOIN tipovehiculos tv        ON m.idtipov        = tv.idtipov
  LEFT JOIN marcas ma               ON m.idmarca        = ma.idmarca

  -- unimos orden y detalle de servicios por fecha y cliente
  LEFT JOIN ordenservicios os  
    ON v.idcliente = os.idcliente
   AND DATE(v.fechahora) = DATE(os.fechaingreso)
  LEFT JOIN detalleordenservicios dos ON dos.idorden     = os.idorden
  LEFT JOIN servicios se            ON se.idservicio    = dos.idservicio
  LEFT JOIN subcategorias sc        ON sc.idsubcategoria= se.idsubcategoria
  LEFT JOIN colaboradores col       ON col.idcolaborador= dos.idmecanico

  WHERE v.estado = TRUE
    AND v.tipocom IN ('boleta','factura')
    AND DATE(v.fechahora) BETWEEN start_date AND end_date

  ORDER BY fecha, idventa, registro_tipo;
END$$
DELIMITER ;
*/

-- PRIMERO 
/*
DROP PROCEDURE IF EXISTS spListVentasPorPeriodo;
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
END$$*/