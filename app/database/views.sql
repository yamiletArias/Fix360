DROP VIEW IF EXISTS vwCategoriasSubcategorias;
SELECT * FROM personas;
CREATE VIEW vwCategoriasSubcategorias AS
SELECT
  c.categoria AS categoria,
  s.subcategoria AS subcategoria
FROM
  subcategorias s
  INNER JOIN categorias c
    ON s.idcategoria = c.idcategoria;

-- SELECT * FROM vwCategoriasSubcategorias; */
 -- VISTA PARA LA INFORMACION DE VENTAS */


-- VISTA DE COMPRAS CON DETALLES DE LOS PRODUCTOS */


-- MUESTRA EL STOCK DE LOS PRODUCTOS */
 






-- create view vwClientePrincipal
 -- SELECT * FROM vwModelosConTipoYMarca;


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
p.modificado,
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
e.ruc,
e.modificado
FROM
clientes c
LEFT JOIN empresas e
ON c.idempresa = e.idempresa
WHERE c.idpersona IS NULL;


CREATE OR REPLACE VIEW vwVehiculos AS
SELECT
CASE
WHEN c.idpersona IS NULL THEN em.nomcomercial
ELSE CONCAT(pe.nombres, ' ',pe.apellidos)
END AS propietario,
v.idvehiculo,
t.tipov,
ma.nombre,
m.modelo,
v.anio,
v.numserie,
v.tipocombustible,
v.placa,
v.color
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


CREATE OR REPLACE VIEW vwproductos AS 
SELECT
p.idproducto,
  m.nombre      AS marca,
  s.subcategoria AS subcategoria,
  p.descripcion AS descripcion,
  p.presentacion AS presentacion,
  p.cantidad     AS cantidad,
  p.undmedida    AS medida,
  p.precio       AS precio,
  p.img          AS img
FROM productos p
    LEFT JOIN marcas m
      ON p.idmarca = m.idmarca
   LEFT JOIN subcategorias s
     ON p.idsubcategoria = s.idsubcategoria;




-- insert into agendas(idpropietario, fchproxvisita, comentario, estado)
-- values (2,curdate(),"Cambio de aceite", 1);

-- select * from agendas;

CREATE OR REPLACE VIEW vwSubcategoriaServicio AS
SELECT
s.*
FROM subcategorias s
INNER JOIN categorias c
ON s.idcategoria = c.idcategoria
WHERE categoria = 'servicio';


CREATE OR REPLACE VIEW vwMecanicos AS
SELECT
c.idcolaborador,
CONCAT(p.nombres,' ',p.apellidos) AS nombres 
FROM colaboradores c
LEFT JOIN contratos co
ON c.idcontrato = co.idcontrato
LEFT JOIN roles r
ON co.idrol = r.idrol
LEFT JOIN personas p
ON co.idpersona = p.idpersona
WHERE r.rol = 'mecanico';

CREATE OR REPLACE VIEW vwComponentes AS
SELECT * FROM componentes;



-- select * from vwMecanicos ORDER BY nombres;

-- select * from vwSubcategoriaServicio;

-- select * from productos;
-- select * from vehiculos;
-- select * from clientes;
-- select * from propietarios;