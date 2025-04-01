
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



SELECT * FROM clientes WHERE idcliente = 4;







SELECT * FROM clientes WHERE idcliente = 1;

SELECT * FROM ventas WHERE idventa = 1;
SELECT * FROM detalleventa WHERE idventa = 1;
SELECT * FROM vs_ventas;