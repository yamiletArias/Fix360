-- PRUEBA
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

-- REAL
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
END$$