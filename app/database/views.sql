DROP VIEW IF EXISTS vwCategoriasSubcategorias;

CREATE VIEW vwCategoriasSubcategorias AS
SELECT
  c.categoria AS categoria,
  s.subcategoria AS subcategoria
FROM
  subcategorias s
  INNER JOIN categorias c
    ON s.idcategoria = c.idcategoria;


-- select * from propietarios;


CREATE OR REPLACE VIEW vwClientesPersona AS
SELECT
c.idcliente,
p.idpersona,
p.nombres,
p.apellidos,
p.tipodoc,
p.numdoc,
p.numruc,
p.direccion,
p.correo,
p.telprincipal,
p.telalternativo
FROM
clientes c
LEFT JOIN personas p
ON c.idpersona = p.idpersona
WHERE c.idempresa IS NULL;

-- select nombres, apellidos,tipodoc,numdoc from vwClientesPersona;

CREATE OR REPLACE VIEW vwClientesEmpresa AS
SELECT 
c.idcliente,
e.idempresa,
e.nomcomercial,
e.razonsocial,
e.telefono,
e.correo,
e.ruc
FROM
clientes c
LEFT JOIN empresas e
ON c.idempresa = e.idempresa
WHERE c.idpersona IS NULL;


CREATE OR REPLACE VIEW vwVehiculos AS
SELECT
CASE
WHEN c.idpersona IS NULL THEN em.nomcomercial
ELSE pe.nombres
END AS propietario,
v.idvehiculo,
t.tipov,
ma.nombre,
v.placa,
v.color,
m.modelo,
v.anio,
v.numserie,
v.tipocombustible
FROM
propietarios p
LEFT JOIN vehiculos v
ON p.idvehiculo = v.idvehiculo
LEFT JOIN clientes c
ON p.idcliente = c.idcliente
LEFT JOIN modelos m
ON v.idmodelo = m.idmodelo
LEFT JOIN tipovehiculos t
ON m.idtipov = t.idtipov
LEFT JOIN marcas ma
ON m.idmarca = ma.idmarca
LEFT JOIN personas pe
ON c.idpersona = pe.idpersona
LEFT JOIN empresas em
ON c.idempresa = em.idempresa;


CREATE OR REPLACE VIEW vwProductos AS
SELECT 
m.nombre AS marca,
s.subcategoria,
p.descripcion,
p.presentacion,
p.cantidad,
p.undmedida AS medida,
p.precio,
p.img 
FROM 
productos p
LEFT JOIN marcas m
ON p.idmarca = m.idmarca
LEFT JOIN subcategorias s
ON p.idsubcategoria = s.idsubcategoria;

-- select * from vwVehiculos;
-- select * from vehiculos;
-- select * from vwProductos;
-- select * from marcas where tipo != "vehiculo" order by tipo ASC;