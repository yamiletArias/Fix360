
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

SELECT * FROM vs_ventas;