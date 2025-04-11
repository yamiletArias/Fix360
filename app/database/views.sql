DROP VIEW IF EXISTS vwCategoriasSubcategorias;
select * from personas;
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
CREATE OR REPLACE VIEW vw_ventas_detalle AS
SELECT
  v.idventa,
  v.fechahora,
  v.tipocom,
  v.numserie,
  v.numcom,
  c.razonsocial AS cliente,
  col.razonsocial AS vendedor,
  dv.idproducto,
  p.nombre AS producto,
  dv.cantidad,
  dv.precioventa,
  dv.descuento
FROM
  ventas v
  JOIN clientes c
    ON v.idcliente = c.idcliente
  JOIN colaboradores col
    ON v.idcolaborador = col.idcolaborador
  JOIN detalleventa dv
    ON v.idventa = dv.idventa
  JOIN productos p
    ON dv.idproducto = p.idproducto;

-- VISTA DE COMPRAS CON DETALLES DE LOS PRODUCTOS */


-- VISTA DEL KARDEX CON DETALLE DEL PRODUCTO
 CREATE OR REPLACE VIEW vwKardex AS
SELECT
  k.*,
  p.descripcion,
  p.precio
FROM
  kardex k
  INNER JOIN productos p
    ON k.idproducto = p.idproducto;



CREATE OR REPLACE VIEW vw_clientes AS
SELECT
  c.idcliente,
  p.idpersona,
  p.nombres,
  p.apellidos,
  p.numdoc AS documento,
  e.idempresa,
  e.nomcomercial,
  e.razonsocial,
  e.ruc
FROM
  clientes c
  LEFT JOIN personas p
    ON c.idpersona = p.idpersona
  LEFT JOIN empresas e
    ON c.idempresa = e.idempresa;

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

create or replace view vwAgendasDelDia as
select
a.*,
CASE
WHEN c.idpersona IS NULL THEN e.nomcomercial
ELSE p.nombres
END AS propietario
from agendas a
inner join propietarios pr
on  a.idpropietario = pr.idpropietario
inner join clientes c
on pr.idcliente = c.idcliente
inner join personas p
on c.idpersona = p.idpersona
inner join empresas e
on c.idempresa = e.idempresa
where a.fchproxvisita = curdate();


insert into agendas(idpropietario, fchproxvisita, comentario, estado)
values (2,curdate(),"Cambio de aceite", 1);

select * from agendas;

create or replace view vwSubcategoriaServicio as
select
s.*
from subcategorias s
inner join categorias c
on s.idcategoria = c.idcategoria
where categoria = 'servicio';

create or replace view vwMecanicos as
select
c.idcolaborador,
p.nombres 
from colaboradores c
left join contratos co
on c.idcontrato = co.idcontrato
left join roles r
on co.idrol = r.idrol
left join personas p
on co.idpersona = p.idpersona
where r.rol = 'mecanico';


-- select * from vwSubcategoriaServicio;
-- select * from productos;
-- select * from vehiculos;