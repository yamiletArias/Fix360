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
    
-- vista de las compras
CREATE VIEW vs_compras AS
SELECT 
    C.idcompra AS id,
    C.tipocom,
    C.numcom,
    E.nomcomercial AS proveedores,
    C.fechacompra,
    DC.preciocompra
FROM compras C
JOIN proveedores P ON C.idproveedor = P.idproveedor
JOIN empresas E ON P.idempresa = E.idempresa
LEFT JOIN detallecompra DC ON C.idcompra = DC.idcompra;

-- vista cotizacion
CREATE OR REPLACE VIEW vs_cotizaciones AS
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
	


-- registro de venta
CREATE VIEW vs_registro_venta AS
SELECT 
    CASE
        WHEN C.idempresa IS NOT NULL THEN E.nomcomercial
        WHEN C.idpersona IS NOT NULL THEN P.nombres
    END AS clientes,
    CONCAT(S.subcategoria, ' ', P2.descripcion) AS subcategoria_producto,
    V.tipocom,
    V.numserie,
    V.numcom,
    V.fechahora,
    V.moneda,
    DV.precioventa,
    DV.cantidad,
    DV.descuento
FROM ventas V
INNER JOIN detalleventa DV ON V.idventa = DV.idventa
INNER JOIN clientes C ON V.idcliente = C.idcliente
LEFT JOIN empresas E ON C.idempresa = E.idempresa
LEFT JOIN personas P ON C.idpersona = P.idpersona
INNER JOIN productos P2 ON DV.idproducto = P2.idproducto
INNER JOIN subcategorias S ON P2.idsubcategoria = S.idsubcategoria;

-- vista cliente
CREATE VIEW vs_clientes AS
SELECT 
    C.idcliente,
    CASE
        WHEN C.idempresa IS NOT NULL THEN E.nomcomercial
        WHEN C.idpersona IS NOT NULL THEN P.nombres
    END AS cliente,
    C.idempresa,
    C.idpersona
FROM clientes C
LEFT JOIN empresas E ON C.idempresa = E.idempresa
LEFT JOIN personas P ON C.idpersona = P.idpersona;

-- producto
CREATE VIEW vs_productos_subcategoria_producto AS
SELECT 
    P.idproducto,
    CONCAT(S.subcategoria, ' ', P.descripcion) AS subcategoria_producto,
    P.precio,
    P.presentacion,
    P.undmedida,
    P.cantidad
FROM productos P
INNER JOIN subcategorias S ON P.idsubcategoria = S.idsubcategoria;

-- detalle venta
CREATE VIEW v_detalle_venta AS
    SELECT d.iddetventa,
           d.idproducto,
           d.cantidad,
           d.numserie,
           d.precioventa,
           d.descuento,
           CONCAT(s.subcategoria, ' ', p.descripcion) AS producto
    FROM detalleventa d
    LEFT JOIN productos p ON d.idproducto = p.idproducto
    LEFT JOIN subcategorias s ON p.idsubcategoria = s.idsubcategoria;

SELECT * FROM v_detalle_venta WHERE idventa = 5;
-- fin detalle venta

-- registro de venta con idproducto
CREATE VIEW vs_registro_venta AS
SELECT 
    CASE
        WHEN C.idempresa IS NOT NULL THEN E.nomcomercial
        WHEN C.idpersona IS NOT NULL THEN P.nombres
    END AS clientes,
    P2.idproducto, 
    CONCAT(S.subcategoria, ' ', P2.descripcion) AS subcategoria_producto,
    V.tipocom,
    V.numserie,
    V.numcom,
    V.fechahora,
    V.moneda,
    DV.precioventa,
    DV.cantidad,
    DV.descuento
FROM ventas V
INNER JOIN detalleventa DV ON V.idventa = DV.idventa
INNER JOIN clientes C ON V.idcliente = C.idcliente
LEFT JOIN empresas E ON C.idempresa = E.idempresa
LEFT JOIN personas P ON C.idpersona = P.idpersona
INNER JOIN productos P2 ON DV.idproducto = P2.idproducto
INNER JOIN subcategorias S ON P2.idsubcategoria = S.idsubcategoria;


-- vista producto por separado
CREATE VIEW vs_productos_categoria_subcategoria AS
SELECT 
    C.categoria, 
    S.subcategoria, 
    P.descripcion AS producto,
    P.precio,
    P.presentacion,
    P.undmedida,
    P.cantidad
FROM productos P
INNER JOIN subcategorias S ON P.idsubcategoria = S.idsubcategoria
INNER JOIN categorias C ON S.idcategoria = C.idcategoria;
-- prueba
CREATE VIEW vs_registro_venta AS
SELECT 
    CASE
        WHEN C.idempresa IS NOT NULL THEN E.nomcomercial
        WHEN C.idpersona IS NOT NULL THEN P.nombres
    END AS clientes,
    DV.subcategoria_producto, -- Ya está listo para mostrar el campo combinado
    V.tipocom,
    V.numserie,
    V.numcom,
    V.fechahora,
    V.moneda,
    DV.precioventa,
    DV.cantidad,
    DV.descuento
FROM ventas V
INNER JOIN detalleventa DV ON V.idventa = DV.idventa
INNER JOIN clientes C ON V.idcliente = C.idcliente
LEFT JOIN empresas E ON C.idempresa = E.idempresa
LEFT JOIN personas P ON C.idpersona = P.idpersona;


-- prueba de venta
CREATE VIEW vs_registro_venta AS
SELECT 
    CASE
        WHEN C.idempresa IS NOT NULL THEN E.nomcomercial
        WHEN C.idpersona IS NOT NULL THEN P.nombres
    END AS clientes,
    P2.descripcion AS producto,
    V.tipocom,
    V.numserie,
    V.numcom,
    V.fechahora,
    V.moneda,
    DV.precioventa,
    DV.cantidad,
    DV.descuento
FROM ventas V
INNER JOIN detalleventa DV ON V.idventa = DV.idventa
INNER JOIN clientes C ON V.idcliente = C.idcliente
LEFT JOIN empresas E ON C.idempresa = E.idempresa
LEFT JOIN personas P ON C.idpersona = P.idpersona
INNER JOIN productos P2 ON DV.idproducto = P2.idproducto;

-- prueba de compra
CREATE VIEW vs_registro_compra AS
SELECT 
    E.nomcomercial AS proveedor,        -- Nombre comercial de la empresa proveedora
    P2.descripcion AS producto,         -- Descripción del producto
    C.tipocom,                          -- Tipo de compra (boleta, factura)
    C.numserie,                         -- Número de serie del documento
    C.numcom,                           -- Número del documento
    C.fechacompra,                      -- Fecha de la compra
    C.moneda,                           -- Moneda de la compra
    DC.preciocompra,                    -- Precio de compra del producto
    DC.cantidad,                        -- Cantidad de productos comprados
    DC.descuento                        -- Descuento aplicado en el producto
FROM compras C
INNER JOIN detallecompra DC ON C.idcompra = DC.idcompra
INNER JOIN proveedores P ON C.idproveedor = P.idproveedor
INNER JOIN empresas E ON P.idempresa = E.idempresa
INNER JOIN productos P2 ON DC.idproducto = P2.idproducto;


SELECT * FROM vs_clientes;

SELECT * FROM clientes WHERE idcliente = 4;

SELECT * FROM clientes WHERE idcliente = 1;

SELECT * FROM ventas WHERE idventa = 1;
SELECT * FROM detalleventa WHERE idventa = 1;
SELECT * FROM vs_ventas;