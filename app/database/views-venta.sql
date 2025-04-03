
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

SELECT idcliente, cliente
FROM vs_clientes
WHERE cliente LIKE CONCAT('%', ?, '%')
LIMIT 10;


-- producto
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

CREATE VIEW vs_productos_subcategoria_producto AS
SELECT 
    CONCAT(S.subcategoria, ' - ', P.descripcion) AS subcategoria_producto,
    P.precio,
    P.presentacion,
    P.undmedida,
    P.cantidad,
    P.img
FROM productos P
INNER JOIN subcategorias S ON P.idsubcategoria = S.idsubcategoria;

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



SELECT * FROM vs_clientes;

SELECT * FROM clientes WHERE idcliente = 4;

SELECT * FROM clientes WHERE idcliente = 1;

SELECT * FROM ventas WHERE idventa = 1;
SELECT * FROM detalleventa WHERE idventa = 1;
SELECT * FROM vs_ventas;