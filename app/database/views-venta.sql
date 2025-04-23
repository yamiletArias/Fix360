-- vista de las ventas
CREATE VIEW vs_ventas AS
SELECT 
    V.idventa AS id,
    CASE
        WHEN C.idempresa IS NOT NULL THEN E.nomcomercial
        WHEN C.idpersona IS NOT NULL THEN P.nombres
    END AS cliente,
    V.tipocom,
    V.fechahora,
    V.numcom
	FROM ventas V
	INNER JOIN clientes C ON V.idcliente = C.idcliente
	LEFT JOIN empresas E ON C.idempresa = E.idempresa
	LEFT JOIN personas P ON C.idpersona = P.idpersona;
    
-- PRUEBA real DE COMPRA CON EL ESTADO
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
-- FIN PRUEBA DE COMPRA CON ESTADO

-- vista cotizacion
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

-- detalle de venta en el modal real
CREATE VIEW vista_detalle_venta AS
SELECT 
  v.idventa,
  COALESCE(CONCAT(p.nombres, ' ', p.apellidos), e.nomcomercial) AS cliente,
  CONCAT(S.subcategoria, ' ', pr.descripcion) AS producto,
  dv.precioventa AS precio,
  dv.descuento
FROM ventas v
JOIN clientes c ON v.idcliente = c.idcliente
LEFT JOIN personas p ON c.idpersona = p.idpersona
LEFT JOIN empresas e ON c.idempresa = e.idempresa
JOIN detalleventa dv ON v.idventa = dv.idventa
JOIN productos pr ON dv.idproducto = pr.idproducto
INNER JOIN subcategorias S ON pr.idsubcategoria = S.idsubcategoria;
-- fin detalle de venta modal

-- detalle de compra modal
-- vista detalle de compra (si solo hay empresas como proveedores)
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
-- fin detalle de compra modal

-- detalle de cotizacion modal
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
-- fin de detalle cotizacion modal


SELECT producto, precio, descuento 
FROM vista_detalle_compra 
WHERE idcompra= 10;
SELECT * FROM vista_detalle_venta WHERE idventa = 2;
SELECT producto, precio, descuento 
FROM vista_detalle_venta 
WHERE idventa = 1;

-- PRUEBAS Y VISTAS ******************