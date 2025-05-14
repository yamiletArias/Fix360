
-- ************************* VISTA DE AMORTIZACION *************************

-- 1) VISTA PARA VER EL TOTAL DE LAS VENTAS (ID)
DROP VIEW IF EXISTS vista_total_por_venta;
CREATE VIEW vista_total_por_venta AS
SELECT
  idventa,
  SUM(precioventa * (1 - descuento/100)) AS total
FROM detalleventa
GROUP BY idventa;

-- 1) VISTA PARA VER EL TOTAL DE LAS COMPRAS (ID)
DROP VIEW IF EXISTS vista_total_por_compra;
CREATE VIEW vista_total_por_compra AS
SELECT
	idcompra,
	SUM(preciocompra * (1 - descuento / 100)) AS total
FROM detallecompra
GROUP BY idcompra;

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

-- ************************* VISTA DE VENTAS

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
  COALESCE(CONCAT(p.apellidos, ' ' , p.nombres), e.nomcomercial) AS cliente,
  v.kilometraje,
  -- Vehículo: muestra NULL si no hay vehículo
  CONCAT(tv.tipov, ' ', ma.nombre, ' ', vh.color, ' (', vh.placa, ')') AS vehiculo,
  CONCAT(s.subcategoria,' ',pr.descripcion) AS producto,
  dv.precioventa AS precio,
  dv.descuento
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
  e.nomcomercial AS proveedor,
  CONCAT(s.subcategoria, ' ', pr.descripcion) AS producto,
  dc.preciocompra AS precio,
  dc.descuento
FROM compras c
JOIN proveedores prov ON c.idproveedor = prov.idproveedor
JOIN empresas e ON prov.idempresa = e.idempresa
JOIN detallecompra dc ON c.idcompra = dc.idcompra
JOIN productos pr ON dc.idproducto = pr.idproducto
JOIN subcategorias s ON pr.idsubcategoria = s.idsubcategoria;

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
  c.vigenciadias AS vigencia
FROM cotizaciones c
LEFT JOIN clientes cli ON c.idcliente = cli.idcliente
LEFT JOIN empresas e ON cli.idempresa = e.idempresa
LEFT JOIN personas p ON cli.idpersona = p.idpersona
JOIN detallecotizacion dc ON c.idcotizacion = dc.idcotizacion;

-- 6) VISTA PARA EL DETALLE DE COTIZACION PARA EL MODAL POR CADA IDCOTIZACION
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
  dv.precioventa AS precio,
  dv.descuento
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
  CONCAT(s.subcategoria, ' ', pr.descripcion) AS producto,
  dc.preciocompra AS precio,
  dc.descuento
FROM compras c
JOIN proveedores prov ON c.idproveedor = prov.idproveedor
JOIN empresas e ON prov.idempresa = e.idempresa
JOIN detallecompra dc ON c.idcompra = dc.idcompra
JOIN productos pr ON dc.idproducto = pr.idproducto
JOIN subcategorias s ON pr.idsubcategoria = s.idsubcategoria
WHERE c.estado = FALSE;

-- ************************* VISTA DE ARQUEO DE CAJA *************************


-- prueba 
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
