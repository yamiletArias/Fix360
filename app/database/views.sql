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
-- select * from vwvehiculos;
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
  p.precioc
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
  ma.nombre     AS marca,
  m.modelo,
  v.anio,
  v.numserie,
  tc.tcombustible,
  v.placa,
  v.color,
  v.vin,
  v.numchasis,
  v.modificado
FROM vehiculos v
  -- Unimos sólo el registro “activo” de propietarios (fechafinal IS NULL)
  LEFT JOIN propietarios p
    ON p.idvehiculo = v.idvehiculo
   AND p.fechafinal IS NULL

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

-- select * from vwproductos;

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
  p.preciov       AS precio,
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
WHERE r.rol = 'mecanico' OR r.rol = 'Jefe mecanico';

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
-- --
DROP VIEW IF EXISTS vwColaboradoresActivosVigentes;
CREATE VIEW vwColaboradoresActivosVigentes AS
SELECT
  col.idcolaborador,
  CONCAT(p.nombres, ' ', p.apellidos) AS nombre_completo
FROM colaboradores col
  JOIN contratos con ON col.idcontrato = con.idcontrato
  JOIN personas  p   ON con.idpersona    = p.idpersona
WHERE
  col.estado = TRUE
  AND con.fechainicio <= CURDATE()
  AND (con.fechafin IS NULL OR con.fechafin >= CURDATE())
ORDER BY nombre_completo;

DROP VIEW IF EXISTS vwFormaPagos;
CREATE VIEW vwFormapagos AS
SELECT * FROM formapagos;
SET GLOBAL event_scheduler = ON;
-- Evento para cancelar automaticamente los recordatorios si tiene el estado de 'P' o 'R'
CREATE EVENT IF NOT EXISTS evCancelarRecordatoriosVencidos
  ON SCHEDULE
    EVERY 1 DAY
    STARTS DATE_ADD(CURDATE(), INTERVAL 1 DAY) + INTERVAL 0 HOUR
  DO
    UPDATE agendas
       SET estado = 'C'
     WHERE estado IN ('P','R')
       AND fchproxvisita < CURDATE();
       
DROP VIEW IF EXISTS vwRoles;
CREATE VIEW vwRoles AS 
SELECT * FROM roles;


DROP VIEW IF EXISTS vwColaboradoresDetalle;
CREATE VIEW vwColaboradoresDetalle AS
SELECT
  c.idcolaborador,
  c.namuser          AS username,
  c.estado           AS usuario_activo,
  -- Datos de la persona vinculada a su contrato
  p.idpersona,
  p.nombres,
  p.apellidos,
  p.tipodoc,
  p.numdoc,
  p.numruc,
  p.direccion,
  p.correo,
  p.telprincipal,
  p.telalternativo,
  -- Fechas de su contrato
  ct.fechainicio,
  ct.fechafin,
  -- Nombre del rol
  r.idrol,
  r.rol              AS nombre_rol
FROM colaboradores c
JOIN contratos ct
  ON c.idcontrato = ct.idcontrato
JOIN personas p
  ON ct.idpersona  = p.idpersona
JOIN roles r
  ON ct.idrol      = r.idrol;
  
  DROP VIEW IF EXISTS vista_total_ordenes_hoy;
CREATE VIEW vista_total_ordenes_hoy AS
SELECT
    COUNT(*) AS total_ordenes_hoy
FROM
    ordenservicios o
WHERE
    DATE(o.fechaingreso) = CURDATE()
    AND o.estado = 'A';


CREATE OR REPLACE VIEW vw_servicios_mensuales AS
SELECT
  DATE_FORMAT(o.fechaingreso, '%Y-%m') AS mes,
  s.servicio                           AS servicio,
  COUNT(*)                             AS veces_realizado
FROM detalleordenservicios dos
JOIN ordenservicios o
  ON dos.idorden = o.idorden
JOIN servicios s
  ON dos.idservicio = s.idservicio
WHERE dos.estado = 'A'
GROUP BY
  mes,
  s.servicio
ORDER BY
  mes,
  veces_realizado DESC;
  
  CREATE OR REPLACE VIEW v_total_ordenes_activas AS
SELECT
  COUNT(*) AS total_ordenes_activas
FROM ordenservicios
WHERE fechasalida IS NULL
  AND estado = 'A';
  

