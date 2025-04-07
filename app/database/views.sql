DROP VIEW IF EXISTS vwCategoriasSubcategorias;

CREATE VIEW vwCategoriasSubcategorias AS
SELECT
  c.nombre AS categoria,
  s.nombre AS subcategoria
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
 CREATE OR REPLACE VIEW vw_compras_detalle AS
SELECT
  cp.idcompra,
  cp.fechacompra,
  cp.tipocom,
  cp.numserie,
  cp.numcom,
  pr.razonsocial AS proveedor,
  col.razonsocial AS comprador,
  dc.idproducto,
  p.nombre AS producto,
  dc.cantidad,
  dc.preciocompra,
  dc.descuento
FROM
  compras cp
  JOIN proveedores pr
    ON cp.idproveedor = pr.idproveedor
  JOIN colaboradores col
    ON cp.idcolaborador = col.idcolaborador
  JOIN detallecompra dc
    ON cp.idcompra = dc.idcompra
  JOIN productos p
    ON dc.idproducto = p.idproducto;

-- MUESTRA EL STOCK DE LOS PRODUCTOS */
 CREATE OR REPLACE VIEW vw_stock_productos AS
SELECT
  p.idproducto,
  p.nombre AS producto,
  p.presentacion,
  p.undmedida,
  k.stockmin,
  k.stockmax
FROM
  productos p
  LEFT JOIN kardex k
    ON p.idproducto = k.idproducto;

-- VISTA DE HISTORIAL DE MOVIMIENTOS DE PRODUCTOS EN EL INVENTARIO */
 CREATE OR REPLACE VIEW vw_movimientos_kardex AS
SELECT
  m.idmovimiento,
  m.fecha,
  p.nombres AS producto,
  tm.flujo,
  tm.tipomov,
  m.cantidad,
  m.saldorestante
FROM
  movimientos m
  JOIN kardex k
    ON m.idkardex = k.idkardex
  JOIN productos p
    ON k.idproducto = p.idproducto
  JOIN tipomovimientos tm
    ON m.idtipomov = tm.idtipomov;

-- VISTA DE DATOS DE CLIENTE A SI SEA PERSONA O EMPRESA
 CREATE OR REPLACE VIEW vw_clientes AS
SELECT
  c.idcliente,
  p.nombres,
  p.apellidos,
  e.nomcomercial AS empresa,
  ct.contactabilidad
FROM
  clientes c
  LEFT JOIN personas p
    ON c.idpersona = p.idpersona
  LEFT JOIN empresas e
    ON c.idempresa = e.idempresa
  JOIN contactabilidad ct
    ON c.idcontactabilidad = ct.idcontactabilidad;

-- VISTA DETALLADA DE LOS COLABORADORES
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
FROM
  colaboradores col
  JOIN contratos c
    ON col.idcontrato = c.idcontrato
  JOIN roles r
    ON c.idrol = r.idrol
  JOIN personas p
    ON c.idpersona = p.idpersona;

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

-- create view vwClientePrincipal
 SELECT
  *
FROM
  vwModelosConTipoYMarca;

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

-- select * from vwVehiculos;
-- select * from vehiculos;