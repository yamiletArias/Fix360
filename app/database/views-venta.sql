
-- 1) VISTA DE VENTAS PARA LISTAR-VENTAS
DROP VIEW IF EXISTS vs_ventas;
CREATE VIEW vs_ventas AS
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
WHERE V.estado = TRUE;
    
-- 2) VISTA DE COMPRAS PARA LISTAR-COMPRAS (se lista solo los de estado TRUE)
DROP VIEW IF EXISTS vs_compras;
CREATE VIEW vs_compras AS
SELECT 
    C.idcompra AS id,
    C.tipocom,
    C.numcom,
    E.nomcomercial AS proveedores,
    C.fechacompra,
    SUM(DC.preciocompra) AS preciocompra
FROM compras C
JOIN proveedores P ON C.idproveedor = P.idproveedor
JOIN empresas E ON P.idempresa = E.idempresa
LEFT JOIN detallecompra DC ON C.idcompra = DC.idcompra
WHERE C.estado = TRUE
GROUP BY C.idcompra, C.tipocom, C.numcom, E.nomcomercial, C.fechacompra;

-- 3) VISTA DE COTIZACIONES PARA LISTAR-COTIZACION
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

-- 4) VISTA PARA EL DETALLE DE VENTA PARA EL MODAL DE CADA VENTA 
DROP VIEW IF EXISTS vista_detalle_venta;
CREATE VIEW vista_detalle_venta AS
SELECT 
  v.idventa,
  v.fechahora,
  COALESCE(CONCAT(p.apellidos, ' ' , p.nombres), e.nomcomercial) AS cliente,
  v.kilometraje,
  CONCAT(tv.tipov, ' ', ma.nombre, ' ', vh.color, ' (', vh.placa, ')') AS vehiculo,
  CONCAT(s.subcategoria,' ',pr.descripcion) AS producto,
  dv.precioventa AS precio,
  dv.descuento
FROM ventas v
JOIN clientes c    ON v.idcliente   = c.idcliente
LEFT JOIN personas p ON c.idpersona   = p.idpersona
LEFT JOIN empresas e ON c.idempresa   = e.idempresa
-- Joins para obtener datos del vehículo
JOIN vehiculos vh     ON v.idvehiculo  = vh.idvehiculo
JOIN modelos m        ON vh.idmodelo   = m.idmodelo
JOIN tipovehiculos tv ON m.idtipov     = tv.idtipov
JOIN marcas ma        ON m.idmarca     = ma.idmarca
-- Joins para detalle de venta
JOIN detalleventa dv  ON v.idventa      = dv.idventa
JOIN productos pr     ON dv.idproducto  = pr.idproducto
JOIN subcategorias s  ON pr.idsubcategoria = s.idsubcategoria
WHERE v.estado = TRUE;

-- 5) VISTA PARA EL DETALLE DE COMPRA PARA EL MODAL POR CADA IDCOMPRA
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

-- 6) VISTA PARA EL DETALLE DE COTIZACION PARA EL MODAL POR CADA IDCOTIZACION
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

-- PRUEBA PARA VER LAS VENTAS REGISTRADAS:
DROP VIEW IF EXISTS vs_ventas_cabecera;
CREATE VIEW vs_ventas_cabecera AS
SELECT
  vt.idventa,
  -- Cliente: empresa o persona
  CASE
    WHEN cl.idempresa IS NOT NULL THEN em.nomcomercial
    WHEN cl.idpersona IS NOT NULL THEN CONCAT(pe.nombres, ' ', pe.apellidos)
    ELSE 'Sin cliente'
  END AS cliente,
  vt.tipocom,
  vt.fechahora,
  vt.numserie,
  vt.numcom,
  vt.moneda,
  vt.kilometraje,
  -- Vehículo concatenado: tipo, marca, color y placa
  CONCAT(tv.tipov, ' ', ma.nombre, ' ', vh.color, ' (', vh.placa, ')') AS vehiculo
FROM ventas       AS vt
JOIN clientes     AS cl  ON vt.idcliente   = cl.idcliente
LEFT JOIN empresas AS em ON cl.idempresa   = em.idempresa
LEFT JOIN personas AS pe ON cl.idpersona   = pe.idpersona
JOIN vehiculos    AS vh  ON vt.idvehiculo  = vh.idvehiculo
JOIN modelos      AS m   ON vh.idmodelo    = m.idmodelo
JOIN tipovehiculos AS tv ON m.idtipov      = tv.idtipov
JOIN marcas       AS ma  ON m.idmarca      = ma.idmarca;


SELECT * 
FROM vs_ventas_detalle_all;

SELECT producto, precio, descuento 
FROM vista_detalle_compra 
WHERE idcompra= 10;
SELECT * FROM vista_detalle_venta WHERE idventa = 2;
SELECT producto, precio, descuento 
FROM vista_detalle_venta 
WHERE idventa = 1;

-- PRUEBAS Y VISTAS ******************