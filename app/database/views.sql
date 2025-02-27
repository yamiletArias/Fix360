DROP VIEW IF EXISTS vwCategoriasSubcategorias;

CREATE VIEW vwCategoriasSubcategorias AS
SELECT 
    c.nombre AS categoria,
    s.nombre AS subcategoria
FROM subcategorias s
INNER JOIN categorias c ON s.idcategoria = c.idcategoria;


-- SELECT * FROM vwCategoriasSubcategorias; */

-- VISTA PARA LA INFORMACIO NDE VENTAS */
CREATE OR REPLACE VIEW vw_ventas_detalle AS
SELECT v.idventa, v.fechahora, v.tipocom, v.numserie, v.numcom, 
       c.razonsocial AS cliente, col.razonsocial AS vendedor, 
       dv.idproducto, p.nombre AS producto, dv.cantidad, dv.precioventa, dv.descuento
FROM ventas v
JOIN clientes c ON v.idcliente = c.idcliente
JOIN colaboradores col ON v.idcolaborador = col.idcolaborador
JOIN detalleventa dv ON v.idventa = dv.idventa
JOIN productos p ON dv.idproducto = p.idproducto;


-- VISTA DE COMPRAS CON DETALLES DE LOS PRODUCTOS */
CREATE OR REPLACE VIEW vw_compras_detalle AS
SELECT cp.idcompra, cp.fechacompra, cp.tipocom, cp.numserie, cp.numcom, 
       pr.razonsocial AS proveedor, col.razonsocial AS comprador, 
       dc.idproducto, p.nombre AS producto, dc.cantidad, dc.preciocompra, dc.descuento
FROM compras cp
JOIN proveedores pr ON cp.idproveedor = pr.idproveedor
JOIN colaboradores col ON cp.idcolaborador = col.idcolaborador
JOIN detallecompra dc ON cp.idcompra = dc.idcompra
JOIN productos p ON dc.idproducto = p.idproducto;


-- MUESTRA EL STOCK DE LOS PRODUCTOS */

CREATE OR REPLACE VIEW vw_stock_productos AS
SELECT p.idproducto, p.nombre AS producto, p.presentacion, p.undmedida, 
       k.stockmin, k.stockmax
FROM productos p
LEFT JOIN kardex k ON p.idproducto = k.idproducto;

-- VISTA DE HISTORIAL DE MOVIMIENTOS DE PRODUCTOS EN EL INVENTARIO */

CREATE OR REPLACE VIEW vw_movimientos_kardex AS
SELECT m.idmovimiento, m.fecha, p.nombre AS producto, 
       tm.flujo, tm.tipomov, m.cantidad, m.saldorestante
FROM movimientos m
JOIN kardex k ON m.idkardex = k.idkardex
JOIN productos p ON k.idproducto = p.idproducto
JOIN tipomovimientos tm ON m.idtipomov = tm.idtipomov;

-- 

