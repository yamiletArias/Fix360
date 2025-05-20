
-- ************************* VISTA DE AMORTIZACION *************************

-- 1) VISTA PARA VER EL TOTAL DE LAS VENTAS (ID)
DROP VIEW IF EXISTS vista_total_por_venta;
CREATE VIEW vista_total_por_venta AS
SELECT
  dv.idventa,
  -- Suma para cada línea: (precio unitario - descuento unitario) × cantidad
  ROUND(SUM((dv.precioventa - dv.descuento) * dv.cantidad), 2) AS total
FROM detalleventa AS dv
GROUP BY dv.idventa;

-- 1) VISTA PARA VER EL TOTAL DE LAS COMPRAS (ID)
DROP VIEW IF EXISTS vista_total_por_compra;
CREATE VIEW vista_total_por_compra AS
SELECT
  dc.idcompra,
  ROUND(SUM((dc.preciocompra - dc.descuento) * dc.cantidad), 2) AS total
FROM detallecompra AS dc
GROUP BY dc.idcompra;

-- 2) VISTA PARA VER LAS AMORTIZACIONES DE CADA VENTA (ID)
DROP VIEW IF EXISTS vista_amortizaciones_por_venta;
CREATE VIEW vista_amortizaciones_por_venta AS
SELECT
  idventa,
  SUM(amortizacion) AS total_amortizado
FROM amortizaciones
GROUP BY idventa;

-- 2) VISTA PARA VER LAS AMORTIZACIONES DE CADA COMPRA (ID)
DROP VIEW IF EXISTS vista_amortizaciones_por_compra;
CREATE VIEW vista_amortizaciones_por_compra AS
SELECT
idcompra,
SUM(amortizacion) AS total_amortizado
FROM amortizaciones
WHERE idcompra IS NOT NULL
GROUP BY idcompra;

-- 3) VISTA PARA TRAER: (TOTAL VENTA) - (TOTAL PAGADO) - (PENDIENTE)
DROP VIEW IF EXISTS vista_saldos_por_venta;
CREATE VIEW vista_saldos_por_venta AS
SELECT
  v.idventa,
  COALESCE(CONCAT(p.nombres, ' ', p.apellidos), e.nomcomercial) AS cliente,
  vt.total AS total_original,
  COALESCE(a.total_amortizado, 0) AS total_pagado,
  vt.total - COALESCE(a.total_amortizado, 0) AS total_pendiente -- <-- este es el nuevo "total"
FROM ventas AS v
JOIN clientes AS c ON v.idcliente = c.idcliente
LEFT JOIN personas AS p ON c.idpersona = p.idpersona
LEFT JOIN empresas AS e ON c.idempresa = e.idempresa
JOIN vista_total_por_venta AS vt ON v.idventa = vt.idventa
LEFT JOIN vista_amortizaciones_por_venta AS a ON v.idventa = a.idventa;

-- 3) VISTA PARA TRAER: (TOTAL COMPRA) - (TOTAL PAGADO) - (PENDIENTE)
DROP VIEW IF EXISTS vista_saldos_por_compra;
CREATE VIEW vista_saldos_por_compra AS
SELECT
  c.idcompra,
  e.nomcomercial AS proveedor,
  tc.total AS total_original,
  COALESCE(a.total_amortizado, 0) AS total_pagado,
  tc.total - COALESCE(a.total_amortizado, 0) AS total_pendiente
FROM compras AS c
JOIN proveedores AS pr ON c.idproveedor = pr.idproveedor
JOIN empresas AS e ON pr.idempresa = e.idempresa
JOIN vista_total_por_compra AS tc ON c.idcompra = tc.idcompra
LEFT JOIN vista_amortizaciones_por_compra AS a ON c.idcompra = a.idcompra;

-- 4) VISTA PARA OBTENER FORMA DE PAGO (idventa - idcompra)
DROP VIEW IF EXISTS vista_amortizaciones_con_formapago;
CREATE VIEW vista_amortizaciones_con_formapago AS
SELECT
a.idamortizacion,
a.idventa,
a.idcompra,
a.numtransaccion,
a.amortizacion,
a.saldo,
a.creado,
f.formapago
FROM amortizaciones AS a
LEFT JOIN formapagos AS f ON a.idformapago = f.idformapago;

-- ************************* VISTA DE VENTAS *************************

-- 1) VISTA DE VENTAS PARA LISTAR-VENTAS
DROP VIEW IF EXISTS vs_ventas;
CREATE VIEW vs_ventas AS
SELECT
  v.idventa   AS id,
  CASE
    WHEN c.idempresa IS NOT NULL THEN e.nomcomercial
    WHEN c.idpersona IS NOT NULL THEN CONCAT(p.nombres, ' ', p.apellidos)
  END AS cliente,
  v.tipocom,
  v.numcom,
  vt.total_pendiente,
  CASE
    WHEN vt.total_pendiente = 0 THEN 'pagado'
    ELSE 'pendiente'
  END AS estado_pago
FROM ventas v
INNER JOIN clientes c      ON v.idcliente = c.idcliente
LEFT  JOIN empresas e      ON c.idempresa  = e.idempresa
LEFT  JOIN personas p      ON c.idpersona  = p.idpersona
JOIN  vista_saldos_por_venta vt ON v.idventa   = vt.idventa
WHERE v.estado = TRUE;

-- 2) VISTA PARA EL DETALLE DE VENTA PARA EL MODAL DE CADA IDVENTA 
DROP VIEW IF EXISTS vista_detalle_venta;
CREATE VIEW vista_detalle_venta AS
SELECT 
  v.idventa,
  v.fechahora,
  COALESCE(CONCAT(p.apellidos, ' ', p.nombres), e.nomcomercial) AS cliente,
  v.kilometraje,
  -- Vehículo completo
  CONCAT(tv.tipov, ' ', ma.nombre, ' ', vh.color, ' (', vh.placa, ')') AS vehiculo,
  -- Producto
  CONCAT(s.subcategoria, ' ', pr.descripcion) AS producto,
  dv.cantidad,
  dv.precioventa AS precio,
  dv.descuento,
  -- Total a pagar por línea: (precio - descuento) * cantidad
  ROUND((dv.precioventa - dv.descuento) * dv.cantidad, 2) AS total_producto
FROM ventas v
JOIN clientes c        ON v.idcliente      = c.idcliente
LEFT JOIN personas p   ON c.idpersona      = p.idpersona
LEFT JOIN empresas e   ON c.idempresa      = e.idempresa
LEFT JOIN vehiculos vh ON v.idvehiculo     = vh.idvehiculo
LEFT JOIN modelos m    ON vh.idmodelo      = m.idmodelo
LEFT JOIN tipovehiculos tv ON m.idtipov    = tv.idtipov
LEFT JOIN marcas ma    ON m.idmarca        = ma.idmarca
JOIN detalleventa dv   ON v.idventa        = dv.idventa
JOIN productos pr      ON dv.idproducto    = pr.idproducto
JOIN subcategorias s   ON pr.idsubcategoria = s.idsubcategoria
WHERE v.estado = TRUE;

-- VISTA DE VENTAS CON ORDEN DE SERVICIO
DROP VIEW IF EXISTS vista_detalle_venta;
CREATE VIEW vista_detalle_venta AS

-- A) líneas de PRODUCTOS
SELECT
  v.idventa,
  v.fechahora,
  COALESCE(CONCAT(p.apellidos,' ',p.nombres), e.nomcomercial) AS cliente,
  v.kilometraje,
  CONCAT(tv.tipov,' ',ma.nombre,' ',vh.color,' (',vh.placa,')') AS vehiculo,
  'PRODUCTO'               AS tipo_linea,
  CONCAT(s.subcategoria,' ',pr.descripcion) AS detalle,
  dv.cantidad,
  dv.precioventa           AS precio_unitario,
  dv.descuento,
  ROUND((dv.precioventa-dv.descuento)*dv.cantidad,2) AS total_linea
FROM ventas v
JOIN clientes c            ON v.idcliente      = c.idcliente
LEFT JOIN personas p       ON c.idpersona      = p.idpersona
LEFT JOIN empresas e       ON c.idempresa      = e.idempresa
LEFT JOIN vehiculos vh     ON v.idvehiculo     = vh.idvehiculo
LEFT JOIN modelos m        ON vh.idmodelo      = m.idmodelo
LEFT JOIN tipovehiculos tv ON m.idtipov        = tv.idtipov
LEFT JOIN marcas ma        ON m.idmarca        = ma.idmarca
JOIN detalleventa dv       ON v.idventa        = dv.idventa
JOIN productos pr          ON dv.idproducto    = pr.idproducto
JOIN subcategorias s       ON pr.idsubcategoria = s.idsubcategoria
WHERE v.estado = TRUE

UNION ALL

-- B) líneas de SERVICIOS
SELECT
  v.idventa,
  v.fechahora,
  COALESCE(CONCAT(p.apellidos,' ',p.nombres), e.nomcomercial) AS cliente,
  v.kilometraje,
  CONCAT(tv.tipov,' ',ma.nombre,' ',vh.color,' (',vh.placa,')') AS vehiculo,
  'SERVICIO'               AS tipo_linea,
  se.servicio              AS detalle,
  1                        AS cantidad,
  dos.precio               AS precio_unitario,
  0                        AS descuento,
  ROUND(dos.precio*1,2)    AS total_linea
FROM ventas v
-- emparejamos con la orden según cliente, propietario y fechaingreso = fechahora
JOIN ordenservicios os 
  ON os.idcliente    = v.idcliente
 AND os.idpropietario = v.idpropietario
 AND os.fechaingreso  = v.fechahora
JOIN detalleordenservicios dos 
  ON os.idorden      = dos.idorden
JOIN servicios se     ON dos.idservicio  = se.idservicio
JOIN clientes c       ON v.idcliente      = c.idcliente
LEFT JOIN personas p  ON c.idpersona      = p.idpersona
LEFT JOIN empresas e  ON c.idempresa      = e.idempresa
LEFT JOIN vehiculos vh ON v.idvehiculo    = vh.idvehiculo
LEFT JOIN modelos m    ON vh.idmodelo     = m.idmodelo
LEFT JOIN tipovehiculos tv ON m.idtipov    = tv.idtipov
LEFT JOIN marcas ma    ON m.idmarca        = ma.idmarca
WHERE v.estado = TRUE;

-- ************************* VISTA DE COMPRAS *************************

-- 3) VISTA DE COMPRAS PARA LISTAR-COMPRAS
DROP VIEW IF EXISTS vs_compras;
CREATE VIEW vs_compras AS
SELECT 
    C.idcompra AS id,
    C.tipocom,
    C.numcom,
    E.nomcomercial AS proveedores,
    VSPC.total_pendiente,
    CASE
        WHEN VSPC.total_pendiente = 0 THEN 'pagado'
        ELSE 'pendiente'
    END AS estado_pago
FROM compras C
JOIN proveedores P ON C.idproveedor = P.idproveedor
JOIN empresas E ON P.idempresa = E.idempresa
LEFT JOIN vista_saldos_por_compra VSPC ON C.idcompra = VSPC.idcompra
WHERE C.estado = TRUE;

-- 4) VISTA PARA EL DETALLE DE COMPRA PARA EL MODAL POR CADA IDCOMPRA
DROP VIEW IF EXISTS vista_detalle_compra;
CREATE VIEW vista_detalle_compra AS
SELECT 
  c.idcompra,
  c.fechacompra,
  e.nomcomercial AS proveedor,
  CONCAT(s.subcategoria, ' ', pr.descripcion) AS producto,
  dc.preciocompra AS precio,
  dc.descuento,
  dc.cantidad,
  ROUND((dc.preciocompra - dc.descuento) * dc.cantidad, 2) AS total_producto
FROM compras c
JOIN proveedores prov ON c.idproveedor = prov.idproveedor
JOIN empresas e ON prov.idempresa = e.idempresa
JOIN detallecompra dc ON c.idcompra = dc.idcompra
JOIN productos pr ON dc.idproducto = pr.idproducto
JOIN subcategorias s ON pr.idsubcategoria = s.idsubcategoria
WHERE c.estado = TRUE;

-- ************************* VISTA COTIZACION *************************

-- 5) VISTA DE COTIZACIONES PARA LISTAR-COTIZACION
DROP VIEW IF EXISTS vs_cotizaciones;
CREATE VIEW vs_cotizaciones AS
SELECT 
  c.idcotizacion,
  CASE
    WHEN cli.idempresa IS NOT NULL THEN e.nomcomercial
    WHEN cli.idpersona IS NOT NULL THEN CONCAT(p.nombres, ' ', p.apellidos)
  END AS cliente,
  dc.precio,
  c.vigenciadias AS vigencia,
  c.fechahora    AS fechahora
FROM cotizaciones c
  LEFT JOIN clientes cli ON c.idcliente = cli.idcliente
  LEFT JOIN empresas e ON cli.idempresa = e.idempresa
  LEFT JOIN personas p ON cli.idpersona = p.idpersona
  JOIN detallecotizacion dc ON c.idcotizacion = dc.idcotizacion
WHERE c.estado = TRUE;


-- 6) VISTA PARA EL DETALLE DE COTIZACION PARA EL MODAL POR CADA IDCOTIZACION
DROP VIEW IF EXISTS vista_detalle_cotizacion;
CREATE VIEW vista_detalle_cotizacion AS
SELECT 
  c.idcotizacion,
  c.idcliente,                                -- <---- lo agregamos
  COALESCE(CONCAT(p.nombres, ' ', p.apellidos), e.nomcomercial) AS cliente,
  CONCAT(S.subcategoria, ' ', pr.descripcion) AS producto,
  dc.precio,
  dc.cantidad,
  dc.descuento
FROM cotizaciones c
  JOIN clientes cli ON c.idcliente = cli.idcliente
  LEFT JOIN personas p ON cli.idpersona = p.idpersona
  LEFT JOIN empresas e ON cli.idempresa = e.idempresa
  JOIN detallecotizacion dc ON c.idcotizacion = dc.idcotizacion
  JOIN productos pr ON dc.idproducto = pr.idproducto
  INNER JOIN subcategorias S ON pr.idsubcategoria = S.idsubcategoria
WHERE c.estado = TRUE;
/* 
-- sin cantidad
DROP VIEW IF EXISTS vista_detalle_cotizacion;
CREATE VIEW vista_detalle_cotizacion AS
SELECT 
  c.idcotizacion,
  COALESCE(CONCAT(p.nombres, ' ', p.apellidos), e.nomcomercial) AS cliente,
  CONCAT(S.subcategoria, ' ', pr.descripcion) AS producto,
  dc.precio,
  dc.descuento
FROM cotizaciones c
JOIN clientes cli ON c.idcliente = cli.idcliente
LEFT JOIN personas p ON cli.idpersona = p.idpersona
LEFT JOIN empresas e ON cli.idempresa = e.idempresa
JOIN detallecotizacion dc ON c.idcotizacion = dc.idcotizacion
JOIN productos pr ON dc.idproducto = pr.idproducto
INNER JOIN subcategorias S ON pr.idsubcategoria = S.idsubcategoria;
*/
/*
-- real
DROP VIEW IF EXISTS vista_detalle_cotizacion;
CREATE VIEW vista_detalle_cotizacion AS
SELECT 
  c.idcotizacion,
  COALESCE(CONCAT(p.nombres, ' ', p.apellidos), e.nomcomercial) AS cliente,
  CONCAT(S.subcategoria, ' ', pr.descripcion) AS producto,
  dc.precio,
  dc.cantidad,        -- ← lo agregamos
  dc.descuento
FROM cotizaciones c
JOIN clientes cli ON c.idcliente = cli.idcliente
LEFT JOIN personas p ON cli.idpersona = p.idpersona
LEFT JOIN empresas e ON cli.idempresa = e.idempresa
JOIN detallecotizacion dc ON c.idcotizacion = dc.idcotizacion
JOIN productos pr ON dc.idproducto = pr.idproducto
INNER JOIN subcategorias S ON pr.idsubcategoria = S.idsubcategoria;*/

-- ************************* VISTA PARA LOS ESTADO FALSE *************************

-- 1) VISTA DE VENTAS ELIMINADAS
DROP VIEW IF EXISTS vs_ventas_eliminadas;
CREATE VIEW vs_ventas_eliminadas AS
SELECT 
    V.idventa AS id,
    CASE
        WHEN C.idempresa IS NOT NULL THEN E.nomcomercial
        WHEN C.idpersona IS NOT NULL THEN CONCAT(P.nombres, ' ', P.apellidos)
    END AS cliente,
    V.tipocom,
    V.numcom
FROM ventas V
INNER JOIN clientes C ON V.idcliente = C.idcliente
LEFT JOIN empresas E ON C.idempresa = E.idempresa
LEFT JOIN personas P ON C.idpersona = P.idpersona
WHERE V.estado = FALSE;

-- 2) VISTA DE LOS DETALLES DE VENTA QUE HAN SIDO ELIMINADOS
DROP VIEW IF EXISTS vista_detalle_venta_eliminada;
CREATE VIEW vista_detalle_venta_eliminada AS
SELECT 
  v.idventa,
  v.fechahora,
  COALESCE(CONCAT(p.apellidos, ' ', p.nombres), e.nomcomercial) AS cliente,
  v.kilometraje,
  CONCAT(tv.tipov, ' ', ma.nombre, ' ', vh.color, ' (', vh.placa, ')') AS vehiculo,
  CONCAT(s.subcategoria, ' ', pr.descripcion) AS producto,
  dv.cantidad,
  dv.precioventa AS precio,
  dv.descuento,
  -- Total a pagar por línea: (precio - descuento) * cantidad
  ROUND((dv.precioventa - dv.descuento) * dv.cantidad, 2) AS total_producto
FROM ventas v
JOIN clientes c        ON v.idcliente      = c.idcliente
LEFT JOIN personas p   ON c.idpersona      = p.idpersona
LEFT JOIN empresas e   ON c.idempresa      = e.idempresa
LEFT JOIN vehiculos vh ON v.idvehiculo     = vh.idvehiculo
LEFT JOIN modelos m    ON vh.idmodelo      = m.idmodelo
LEFT JOIN tipovehiculos tv ON m.idtipov    = tv.idtipov
LEFT JOIN marcas ma    ON m.idmarca        = ma.idmarca
JOIN detalleventa dv   ON v.idventa        = dv.idventa
JOIN productos pr      ON dv.idproducto    = pr.idproducto
JOIN subcategorias s   ON pr.idsubcategoria = s.idsubcategoria
WHERE v.estado = FALSE;

-- 3) VISTA PARA VER LA JUSTIFICACION POR IDVENTA
DROP VIEW IF EXISTS vista_justificacion_venta;
CREATE VIEW vista_justificacion_venta AS
SELECT 
    idventa,
    justificacion
FROM ventas
WHERE estado = FALSE;

-- 4) VISTA DE COMPRAS ELIMINADAS
DROP VIEW IF EXISTS vs_compras_eliminadas;
CREATE VIEW vs_compras_eliminadas AS
SELECT 
    C.idcompra AS id,
    E.nomcomercial AS proveedor,
    C.tipocom,
    C.numcom
FROM compras C
JOIN proveedores P ON C.idproveedor = P.idproveedor
JOIN empresas E ON P.idempresa = E.idempresa
WHERE C.estado = FALSE;

-- 5) VISTA PARA VER LA JUSTIFICACION POR IDCOMPRA
DROP VIEW IF EXISTS vista_justificacion_compra;
CREATE VIEW vista_justificacion_compra AS
SELECT 
    idcompra,
    justificacion
FROM compras
WHERE estado = FALSE;

-- 6) VISTA PARA EL DETALLE DE COMPRA
DROP VIEW IF EXISTS vista_detalle_compra_eliminada;
CREATE VIEW vista_detalle_compra_eliminada AS
SELECT 
  c.idcompra,
  e.nomcomercial AS proveedor,
  c.fechacompra,
  CONCAT(s.subcategoria, ' ', pr.descripcion) AS producto,
  dc.cantidad,
  dc.preciocompra AS precio,
  dc.descuento,
  ROUND((dc.preciocompra - dc.descuento) * dc.cantidad, 2) AS total_producto
FROM compras c
JOIN proveedores prov ON c.idproveedor = prov.idproveedor
JOIN empresas e ON prov.idempresa = e.idempresa
JOIN detallecompra dc ON c.idcompra = dc.idcompra
JOIN productos pr ON dc.idproducto = pr.idproducto
JOIN subcategorias s ON pr.idsubcategoria = s.idsubcategoria
WHERE c.estado = FALSE;

-- 7) COTIZACIONES
DROP VIEW IF EXISTS vs_cotizaciones_eliminadas;
CREATE VIEW vs_cotizaciones_eliminadas AS
SELECT 
  c.idcotizacion,
  CASE
    WHEN cli.idempresa IS NOT NULL THEN e.nomcomercial
    WHEN cli.idpersona IS NOT NULL THEN CONCAT(p.nombres, ' ', p.apellidos)
  END AS cliente,
  dc.precio,
  c.vigenciadias AS vigencia,
  c.fechahora
FROM cotizaciones c
  LEFT JOIN clientes cli ON c.idcliente = cli.idcliente
  LEFT JOIN empresas e ON cli.idempresa = e.idempresa
  LEFT JOIN personas p ON cli.idpersona = p.idpersona
  JOIN detallecotizacion dc ON c.idcotizacion = dc.idcotizacion
WHERE c.estado = FALSE;

DROP VIEW IF EXISTS vista_detalle_cotizacion_eliminada;
CREATE VIEW vista_detalle_cotizacion_eliminada AS
SELECT 
  c.idcotizacion,
  COALESCE(CONCAT(p.nombres, ' ', p.apellidos), e.nomcomercial) AS cliente,
  CONCAT(s.subcategoria, ' ', pr.descripcion) AS producto,
  dc.precio,
  dc.descuento
FROM cotizaciones c
JOIN clientes cli ON c.idcliente = cli.idcliente
LEFT JOIN personas p ON cli.idpersona = p.idpersona
LEFT JOIN empresas e ON cli.idempresa = e.idempresa
JOIN detallecotizacion dc ON c.idcotizacion = dc.idcotizacion
JOIN productos pr ON dc.idproducto = pr.idproducto
JOIN subcategorias s ON pr.idsubcategoria = s.idsubcategoria
WHERE c.estado = FALSE;

DROP VIEW IF EXISTS vista_justificacion_cotizacion;
CREATE VIEW vista_justificacion_cotizacion AS
SELECT 
  idcotizacion,
  justificacion
FROM cotizaciones
WHERE estado = FALSE;

-- ************************* VISTA DE ARQUEO DE CAJA *************************

-- VISTA PARA VER LOS INGRESOS
DROP VIEW IF EXISTS vista_formapagos;
CREATE VIEW vista_formapagos AS
SELECT
  idformapago,
  formapago
FROM formapagos;

-- VISTA PARA VER LOS EGRESOS

CREATE OR REPLACE VIEW vista_conceptos_egresos AS
SELECT 'almuerzo' AS concepto
UNION ALL SELECT 'combustible'
UNION ALL SELECT 'compra de insumos'
UNION ALL SELECT 'otros conceptos'
UNION ALL SELECT 'pasajes'
UNION ALL SELECT 'servicios varios';

-- VISTA PARA EL RESUMEN Y SALDO RESTANTE
DROP VIEW IF EXISTS vista_resumen_arqueo;
CREATE VIEW vista_resumen_arqueo AS
SELECT
  f.fecha,

  -- 1) Saldo anterior: ingresos acumulados – egresos (sólo conceptos válidos) acumulados antes de la fecha
  COALESCE(
    (SELECT SUM(a.amortizacion)
     FROM amortizaciones a
     WHERE a.idventa IS NOT NULL
       AND DATE(a.creado) < f.fecha)
    , 0)
  -
  COALESCE(
    (SELECT SUM(e.monto)
     FROM egresos e
     JOIN vista_conceptos_egresos c ON e.concepto = c.concepto
     WHERE DATE(e.creado) < f.fecha)
    , 0)
  AS saldo_anterior,

  -- 2) Ingreso efectivo del día: todas las amortizaciones del día
  COALESCE(
    (SELECT SUM(a.amortizacion)
     FROM amortizaciones a
     WHERE a.idventa IS NOT NULL
       AND DATE(a.creado) = f.fecha)
  , 0) AS ingreso_efectivo,

  -- 3) Egresos del día: sólo los conceptos de la vista
  COALESCE(
    (SELECT SUM(e.monto)
     FROM egresos e
     JOIN vista_conceptos_egresos c ON e.concepto = c.concepto
     WHERE DATE(e.creado) = f.fecha)
  , 0) AS total_egresos,

  -- 4) Total efectivo (saldo anterior + ingreso del día)
  ( (    
        COALESCE(
          (SELECT SUM(a.amortizacion)
           FROM amortizaciones a
           WHERE a.idventa IS NOT NULL
             AND DATE(a.creado) < f.fecha)
        , 0)
      -
        COALESCE(
          (SELECT SUM(e.monto)
           FROM egresos e
           JOIN vista_conceptos_egresos c ON e.concepto = c.concepto
           WHERE DATE(e.creado) < f.fecha)
        , 0)
    )
    +
    COALESCE(
      (SELECT SUM(a.amortizacion)
       FROM amortizaciones a
       WHERE a.idventa IS NOT NULL
         AND DATE(a.creado) = f.fecha)
    , 0)
  ) AS total_efectivo,

  -- 5) Total en caja (total efectivo – egresos del día)
  GREATEST(
    (
      ( 
        COALESCE(
          (SELECT SUM(a.amortizacion)
           FROM amortizaciones a
           WHERE a.idventa IS NOT NULL
             AND DATE(a.creado) < f.fecha)
        , 0)
      -
        COALESCE(
          (SELECT SUM(e.monto)
           FROM egresos e
           JOIN vista_conceptos_egresos c ON e.concepto = c.concepto
           WHERE DATE(e.creado) < f.fecha)
        , 0)
      )
      +
      COALESCE(
        (SELECT SUM(a.amortizacion)
         FROM amortizaciones a
         WHERE a.idventa IS NOT NULL
           AND DATE(a.creado) = f.fecha)
      , 0)
    )
    -
    COALESCE(
      (SELECT SUM(e.monto)
       FROM egresos e
       JOIN vista_conceptos_egresos c ON e.concepto = c.concepto
       WHERE DATE(e.creado) = f.fecha)
    , 0)
  , 0) AS total_caja

FROM (
  -- Todas las fechas con movimiento (ingresos u egresos)
  SELECT DATE(creado) AS fecha FROM amortizaciones
  UNION
  SELECT DATE(creado) AS fecha FROM egresos
) AS f
ORDER BY f.fecha;


-- PRUEBA DE EGRESOS
/*
DROP VIEW IF EXISTS vista_egresos_por_fecha_y_concepto;
CREATE VIEW vista_egresos_por_fecha_y_concepto AS
SELECT
  d.fecha,
  c.concepto,
  COALESCE(agg.total_egresos, 0) AS total_egresos
FROM
  -- 1) todas las fechas en que hay registro de egresos
  ( SELECT DISTINCT DATE(creado) AS fecha
    FROM egresos
  ) AS d
CROSS JOIN
  -- 2) lista fija de conceptos que quieres mostrar siempre
  ( SELECT 'almuerzo'           AS concepto
    UNION ALL SELECT 'combustible'
    UNION ALL SELECT 'compra de insumos'
    UNION ALL SELECT 'otros conceptos'
    UNION ALL SELECT 'pasajes'
    UNION ALL SELECT 'servicios varios'
  ) AS c
LEFT JOIN
  -- 3) sumas reales por fecha y concepto
  ( SELECT
      DATE(creado)        AS fecha,
      concepto,
      ROUND(SUM(monto),2) AS total_egresos
    FROM egresos
    GROUP BY DATE(creado), concepto
  ) AS agg
  ON agg.fecha    = d.fecha
 AND agg.concepto = c.concepto
ORDER BY d.fecha, c.concepto;
*/
-- PRUEBA DE INGRESOS
/*
DROP VIEW IF EXISTS vista_ingresos_por_formapago;
CREATE VIEW vista_ingresos_por_formapago AS
SELECT
  f.idformapago,
  f.formapago,
  d.fecha,
  COALESCE(agg.total_ingresos, 0) AS total_ingresos
FROM formapagos AS f
CROSS JOIN (
  -- todas las fechas en que hubo cobros de venta
  SELECT DISTINCT DATE(creado) AS fecha
  FROM amortizaciones
  WHERE idventa IS NOT NULL
) AS d
LEFT JOIN (
  -- suma de amortizaciones por forma y fecha
  SELECT
    idformapago,
    DATE(creado) AS fecha,
    ROUND(SUM(amortizacion), 2) AS total_ingresos
  FROM amortizaciones
  WHERE idventa IS NOT NULL
  GROUP BY idformapago, DATE(creado)
) AS agg
  ON agg.idformapago = f.idformapago
 AND agg.fecha       = d.fecha
ORDER BY d.fecha, f.idformapago;
*/

-- PRUEBAS PARA VER EL STOCK
/*
CREATE OR REPLACE VIEW vista_stock_actual AS
SELECT 
  k.idproducto,
  p.descripcion,
  COALESCE(SUM(
    CASE 
      WHEN tm.flujo = 'entrada' THEN m.cantidad
      WHEN tm.flujo = 'salida'  THEN -m.cantidad
      ELSE 0
    END
  ), 0) AS stock_actual
FROM kardex k
JOIN productos p ON k.idproducto = p.idproducto
LEFT JOIN movimientos m ON m.idkardex = k.idkardex
LEFT JOIN tipomovimientos tm ON m.idtipomov = tm.idtipomov
GROUP BY k.idproducto, p.descripcion;
*/

-- VISTA PARA VER LAS ENTRADAS DE COMPRA
/*
SELECT
  c.idcompra,
  c.fechacompra,
  c.tipocom,
  c.numserie,
  c.numcom,
  c.moneda,
  p.idproducto,
  p.descripcion AS producto,
  dc.cantidad,
  dc.preciocompra,
  dc.descuento,
  k.idkardex,
  lm.idmovimiento,
  lm.fecha,
  lm.cantidad,
  lm.saldorestante,
  tm.flujo,
  tm.tipomov
FROM compras c
INNER JOIN detallecompra dc ON c.idcompra = dc.idcompra
INNER JOIN productos p ON dc.idproducto = p.idproducto
LEFT JOIN kardex k ON p.idproducto = k.idproducto
-- Traemos el último movimiento que corresponde a la compra y producto exactos
LEFT JOIN movimientos lm
  ON lm.idkardex = k.idkardex
  AND lm.cantidad = dc.cantidad
  AND lm.fecha <= c.fechacompra
  AND lm.idtipomov IN (
    SELECT idtipomov FROM tipomovimientos WHERE flujo = 'entrada' AND tipomov = 'compra'
  )
LEFT JOIN tipomovimientos tm ON lm.idtipomov = tm.idtipomov
WHERE c.estado = TRUE
ORDER BY c.fechacompra DESC, c.idcompra, p.descripcion; 
*/

-- VISTA PARA VER LAS SALIDA DE VENTAS
/*
SELECT
  v.idventa,
  v.fechahora,
  v.tipocom,
  v.numserie,
  v.numcom,
  v.moneda,
  v.kilometraje,
  COALESCE(CONCAT(p.nombres, ' ', p.apellidos), e.nomcomercial) AS cliente,
  CONCAT(cp.nombres, ' ', cp.apellidos) AS colaborador,
  CONCAT(tv.tipov, ' ', ma.nombre, ' ', vh.color, ' (', vh.placa, ')') AS vehiculo,
  dv.idproducto,
  CONCAT(s.subcategoria, ' ', pr.descripcion) AS producto,
  dv.cantidad,
  dv.precioventa AS precio_unitario,
  dv.descuento AS descuento_unitario,
  ROUND((dv.precioventa - dv.descuento) * dv.cantidad, 2) AS total_linea,
  dv.numserie AS numserie_detalle,
  k.idkardex,
  lm.idmovimiento,
  lm.fecha,
  lm.cantidad,
  lm.saldorestante,
  tm.flujo,
  tm.tipomov
FROM ventas v
INNER JOIN detalleventa dv ON v.idventa = dv.idventa
LEFT JOIN clientes c ON v.idcliente = c.idcliente
LEFT JOIN personas p ON c.idpersona = p.idpersona
LEFT JOIN empresas e ON c.idempresa = e.idempresa
LEFT JOIN colaboradores col ON v.idcolaborador = col.idcolaborador
LEFT JOIN contratos ct ON col.idcontrato = ct.idcontrato
LEFT JOIN personas cp ON ct.idpersona = cp.idpersona
LEFT JOIN vehiculos vh ON v.idvehiculo = vh.idvehiculo
LEFT JOIN modelos m ON vh.idmodelo = m.idmodelo
LEFT JOIN tipovehiculos tv ON m.idtipov = tv.idtipov
LEFT JOIN marcas ma ON m.idmarca = ma.idmarca
INNER JOIN productos pr ON dv.idproducto = pr.idproducto
INNER JOIN subcategorias s ON pr.idsubcategoria = s.idsubcategoria
LEFT JOIN kardex k ON pr.idproducto = k.idproducto
LEFT JOIN movimientos lm 
  ON lm.idkardex = k.idkardex 
  AND lm.cantidad = dv.cantidad
  AND lm.fecha <= v.fechahora
  AND lm.idtipomov IN (
    SELECT idtipomov FROM tipomovimientos WHERE flujo = 'salida' AND tipomov = 'venta'
  )
LEFT JOIN tipomovimientos tm ON lm.idtipomov = tm.idtipomov
WHERE v.estado = TRUE;
*/

-- VISTAR PARA LA DEVOLUCION:
/*
SELECT
  v.idventa,
  v.fechahora,
  v.numcom,
  v.justificacion,
  COALESCE(CONCAT(p.nombres, ' ', p.apellidos), e.nomcomercial) AS cliente,
  dv.idproducto,
  pr.descripcion AS producto,
  dv.cantidad AS cantidad_vendida,
  m.fecha AS fecha_devolucion,
  m.cantidad AS cantidad_devuelta,
  m.saldorestante
FROM ventas v
INNER JOIN detalleventa dv ON v.idventa = dv.idventa
INNER JOIN productos pr ON dv.idproducto = pr.idproducto
LEFT JOIN clientes c ON v.idcliente = c.idcliente
LEFT JOIN personas p ON c.idpersona = p.idpersona
LEFT JOIN empresas e ON c.idempresa = e.idempresa
LEFT JOIN kardex k ON pr.idproducto = k.idproducto
INNER JOIN movimientos m ON m.idkardex = k.idkardex
INNER JOIN tipomovimientos tm ON m.idtipomov = tm.idtipomov
    AND tm.flujo = 'entrada'
    AND tm.tipomov = 'devolucion'
WHERE v.estado = 0;
*/

/*
DROP VIEW IF EXISTS vista_para_convertir_cotizacion;
CREATE VIEW vista_para_convertir_cotizacion AS
SELECT
  c.idcotizacion,
  c.fechahora,
  c.moneda,
  COALESCE(CONCAT(p.nombres, ' ', p.apellidos), e.nomcomercial) AS cliente,
  dc.idproducto,
  CONCAT(s.subcategoria, ' ', pr.descripcion) AS producto,
  dc.precio,
  dc.cantidad,
  dc.descuento
FROM cotizaciones AS c
JOIN detallecotizacion AS dc     ON c.idcotizacion = dc.idcotizacion
LEFT JOIN clientes AS cli        ON c.idcliente    = cli.idcliente
LEFT JOIN personas AS p          ON cli.idpersona  = p.idpersona
LEFT JOIN empresas AS e          ON cli.idempresa  = e.idempresa
JOIN productos AS pr             ON dc.idproducto  = pr.idproducto
JOIN subcategorias AS s          ON pr.idsubcategoria = s.idsubcategoria
WHERE c.idcotizacion IS NOT NULL;
*/