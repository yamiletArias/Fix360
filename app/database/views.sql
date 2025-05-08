USE dbfix360;

-- 1) Categorías y subcategorías
DROP VIEW IF EXISTS vwCategoriasSubcategorias;
CREATE VIEW vwCategoriasSubcategorias AS
SELECT
  c.categoria AS categoria,
  s.subcategoria AS subcategoria
FROM subcategorias s
INNER JOIN categorias c
  ON s.idcategoria = c.idcategoria;

-- 2) Información de cliente (persona o empresa, con contactabilidad)
DROP VIEW IF EXISTS vw_clientes;
CREATE OR REPLACE VIEW vw_clientes AS
SELECT
  c.idcliente,
  p.nombres,
  p.apellidos,
  e.nomcomercial AS empresa,
  ct.contactabilidad
FROM clientes c
LEFT JOIN personas p
  ON c.idpersona = p.idpersona
LEFT JOIN empresas e
  ON c.idempresa = e.idempresa
JOIN contactabilidad ct
  ON c.idcontactabilidad = ct.idcontactabilidad;

-- 3) Colaboradores con contrato y rol
DROP VIEW IF EXISTS vw_colaboradores;
CREATE OR REPLACE VIEW vw_colaboradores AS
SELECT
  col.idcolaborador,
  col.namuser,
  col.estado,
  r.rol,
  p.nombres,
  p.apellidos,
  c.fechainicio,
  c.fechafin
FROM colaboradores col
JOIN contratos c
  ON col.idcontrato = c.idcontrato
JOIN roles r
  ON c.idrol = r.idrol
JOIN personas p
  ON c.idpersona = p.idpersona;

-- 4) Kardex con detalle de producto
DROP VIEW IF EXISTS vwKardex;
CREATE OR REPLACE VIEW vwKardex AS
SELECT
  k.*,
  p.descripcion,
  p.precio
FROM kardex k
INNER JOIN productos p
  ON k.idproducto = p.idproducto;

-- 5) Información cliente extendida (versión 2)
DROP VIEW IF EXISTS vw_clientes;
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
FROM clientes c
LEFT JOIN personas p
  ON c.idpersona = p.idpersona
LEFT JOIN empresas e
  ON c.idempresa = e.idempresa;

-- 6) Solo clientes persona
DROP VIEW IF EXISTS vwClientesPersona;
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
FROM clientes c
LEFT JOIN personas p
  ON c.idpersona = p.idpersona
WHERE c.idempresa IS NULL;

-- 7) Solo clientes empresa
DROP VIEW IF EXISTS vwClientesEmpresa;
CREATE OR REPLACE VIEW vwClientesEmpresa AS
SELECT 
  c.idcliente,
  e.idempresa,
  e.nomcomercial,
  e.razonsocial,
  e.telefono,
  e.correo,
  e.modificado,
  e.ruc
FROM clientes c
LEFT JOIN empresas e
  ON c.idempresa = e.idempresa
WHERE c.idpersona IS NULL;

-- 8) Vehículos con datos de propietario
-- select * from vwVehiculos;
DROP VIEW IF EXISTS vwVehiculos;
CREATE OR REPLACE VIEW vwVehiculos AS
SELECT
  CASE
    WHEN c.idpersona IS NULL THEN em.nomcomercial
    ELSE CONCAT(pe.nombres, ' ', pe.apellidos)
  END AS propietario,
  v.idvehiculo,
  t.tipov,
  ma.nombre,
  m.modelo,
  v.anio,
  v.numserie,
  tc.tcombustible,
  v.placa,
  v.color,
  v.vin,
  v.numchasis
FROM propietarios p
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
  ON c.idempresa = em.idempresa
  LEFT JOIN tipocombustibles tc
  ON v.idtcombustible = tc.idtcombustible;
  
-- select * from vwtcombustible;  
  
  DROP VIEW IF EXISTS vwtcombustible;
  CREATE OR REPLACE VIEW vwtcombustible AS
  SELECT
  idtcombustible,
  tcombustible
  FROM tipocombustibles;

-- 9) Productos con marca y subcategoría
DROP VIEW IF EXISTS vwproductos;
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

-- 10) Subcategorías cuyo categoría es 'servicio'
DROP VIEW IF EXISTS vwSubcategoriaServicio;
CREATE OR REPLACE VIEW vwSubcategoriaServicio AS
SELECT
  s.*
FROM subcategorias s
INNER JOIN categorias c
  ON s.idcategoria = c.idcategoria
WHERE c.categoria = 'servicio';

-- 11) Colaboradores con rol 'mecanico'
DROP VIEW IF EXISTS vwMecanicos;
CREATE OR REPLACE VIEW vwMecanicos AS
SELECT
  c.idcolaborador,
  CONCAT(p.nombres, ' ', p.apellidos) AS nombres 
FROM colaboradores c
LEFT JOIN contratos co
  ON c.idcontrato = co.idcontrato
LEFT JOIN roles r
  ON co.idrol = r.idrol
LEFT JOIN personas p
  ON co.idpersona = p.idpersona
WHERE r.rol = 'mecanico';

-- 12) Componentes (lista completa)
DROP VIEW IF EXISTS vwComponentes;
CREATE OR REPLACE VIEW vwComponentes AS
SELECT idcomponente,componente FROM componentes;

-- select * from vwcomponentes;
DROP VIEW IF EXISTS vwRecordatoriosHoy;
CREATE OR REPLACE VIEW vwRecordatoriosHoy AS
SELECT
  a.idagenda,
  a.idpropietario,
  a.fchproxvisita,
  a.comentario,
  a.estado,
   CONCAT(p.nombres, ' ', p.apellidos) AS nomcliente ,
  p.telprincipal,
  p.telalternativo,
  p.correo
FROM agendas AS a
JOIN clientes AS c
  ON a.idpropietario = c.idcliente
JOIN personas AS p
  ON c.idpersona = p.idpersona
WHERE DATE(a.fchproxvisita) = CURDATE() AND a.estado IN ('P','R'); 


