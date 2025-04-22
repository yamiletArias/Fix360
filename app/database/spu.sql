/* Registrar un nuevo cliente*/
-- insert into contactabilidad (contactabilidad) values ('redes sociales');
 -- select * from empresas;
 -- select * from productos;
 -- select * from personas;
 -- select * from propietarios;
 -- select * from clientes;
 -- select * from vehiculos;
-- drop procedure spRegisterPersona;
 -- call spRegisterCliente('empresa',null, null,null,null,null,'correoempresa@gmail.com',null,null,'nueva salud','empresa SAC','912498430','12345678948',1);
 
 DELIMITER $$

CREATE PROCEDURE spRegisterClientePersona (
  IN _nombres VARCHAR (50),
  IN _apellidos VARCHAR (50),
  IN _tipodoc VARCHAR (30),
  IN _numdoc CHAR(20),
  IN _numruc CHAR(11),
  IN _direccion VARCHAR (70),
  IN _correo VARCHAR (100),
  IN _telprincipal VARCHAR (20),
  IN _telalternativo VARCHAR (20),
  IN _idcontactabilidad INT
)
BEGIN
  DECLARE _idpersona INT;
  -- Insertar en la tabla personas
   INSERT INTO personas (
    nombres,
    apellidos,
    tipodoc,
    numdoc,
    numruc,
    direccion,
    correo,
    telprincipal,
    telalternativo
  )
  VALUES
    (
      _nombres,
      _apellidos,
      _tipodoc,
      _numdoc,
      _numruc,
      _direccion,
      _correo,
      _telprincipal,
      _telalternativo
    );
  -- Obtener el ID de la persona insertada
   SET _idpersona = LAST_INSERT_ID();
  -- Insertar en la tabla clientes a la persona
   INSERT INTO clientes (idpersona, idcontactabilidad)
  VALUES
    (_idpersona, _idcontactabilidad);
END $$

DELIMITER $$

DELIMITER $$
CREATE PROCEDURE spRegisterClienteEmpresa (
  IN _ruc CHAR(11),
  IN _nomcomercial VARCHAR (80),
  IN _razonsocial VARCHAR (80),
  IN _telefono VARCHAR (20),
  IN _correo VARCHAR (100),
  IN _idcontactabilidad INT
)
BEGIN
  DECLARE _idempresa INT;
  -- Insertar en la tabla empresas
   INSERT INTO empresas (
    ruc,
    nomcomercial,
razonsocial,
    telefono,
    correo
  )
  VALUES
    (
      _ruc,
      _nomcomercial,
      _razonsocial,
      _telefono,
      _correo
    );
  -- Obtener el ID de la empresa insertada
   SET _idempresa = LAST_INSERT_ID();
  -- Insertar en la tabla clientes vinculando la empresa
   INSERT INTO clientes (idempresa, idcontactabilidad)
  VALUES
    (_idempresa, _idcontactabilidad);
END $$

-- drop procedure spRegisterVehiculo;

DELIMITER $$
 CREATE PROCEDURE spRegisterVehiculo(
IN _idmodelo INT,
IN _placa CHAR(6),
IN _anio CHAR(4),
IN _numserie VARCHAR(50),
IN _color VARCHAR(20),
IN _tipocombustible VARCHAR(20),
IN _idcliente INT
)
BEGIN
DECLARE _idvehiculo INT;

INSERT INTO vehiculos (idmodelo,placa,anio,numserie,color,tipocombustible)
VALUES (_idmodelo,_placa,_anio,_numserie,_color,_tipocombustible);
SET _idvehiculo = LAST_INSERT_ID();
INSERT INTO propietarios (idcliente, idvehiculo) VALUES
(_idcliente, _idvehiculo);

END $$
DELIMITER $$
-- select * from vwClientesPersona;
CREATE PROCEDURE spGetAllContactabilidad ()
BEGIN
  SELECT
    *
  FROM
    contactabilidad
  ORDER BY contactabilidad ASC;
END $$

DELIMITER $$

DELIMITER $$
CREATE PROCEDURE spGetSubcategoriaByCategoria (
IN _idcategoria INT
)
BEGIN
SELECT 
s.idsubcategoria,
s.subcategoria
FROM
subcategorias s
WHERE 
s.idcategoria = _idcategoria;
END $$

DELIMITER $$

-- call spGetSubcategoriaByCategoria(1); 
-- select * from subcategorias order by idsubcategoria asc;
DELIMITER $$

CREATE PROCEDURE spGetModelosByTipoMarca (IN p_idtipov INT, IN p_idmarca INT)
BEGIN
  SELECT
    m.idmodelo,
    m.modelo
  FROM
    Modelos m
  WHERE m.idtipov = p_idtipov
    AND m.idmarca = p_idmarca;
END $$

DELIMITER $$

DELIMITER $$
CREATE PROCEDURE spGetAllMarcaProducto()
BEGIN
SELECT
* 
FROM 
marcas 
WHERE tipo != 'vehiculo';
END $$
 DELIMITER $$
 
 DELIMITER $$
 CREATE PROCEDURE spGetAllCategoria()
 BEGIN
 SELECT 
 *
 FROM
 categorias;
 END $$
 DELIMITER $$
 
 DELIMITER $$
CREATE PROCEDURE spGetAllMarcaVehiculo ()
BEGIN
  SELECT
    *
  FROM
    marcas
  WHERE tipo = 'vehiculo'
  ORDER BY nombre ASC;
END $$

DELIMITER $$

DELIMITER $$

CREATE PROCEDURE spGetAllTipoVehiculo ()
BEGIN
  SELECT
    idtipov,
    tipov
  FROM
    tipovehiculos
  ORDER BY tipov ASC;
END $$

DELIMITER $$

CREATE PROCEDURE spBuscarPersona (
  IN _tipoBusqueda VARCHAR (20),
  IN _criterio VARCHAR (100)
)
BEGIN
  IF _tipoBusqueda = 'DNI'
  THEN
  SELECT
    *
  FROM
    personas
  WHERE numdoc LIKE CONCAT('%', _criterio, '%');
  ELSEIF _tipoBusqueda = 'NOMBRE'
  THEN
  SELECT
    *
  FROM
    personas
  WHERE CONCAT(nombres, ' ', apellidos) LIKE CONCAT('%', _criterio, '%');
  END IF;
END $$

DELIMITER $$

-- CALL spBuscarPersona ('dni', '761');
-- CALL spBuscarPersona ('nombre', 'herna') -- select * from personas;
 DROP PROCEDURE IF EXISTS spBuscarEmpresa;
DELIMITER $$

CREATE PROCEDURE spBuscarEmpresa (
  IN _tipoBusqueda VARCHAR (20),
  IN _criterio VARCHAR (100)
)
BEGIN
  IF _tipoBusqueda = 'RUC'
  THEN
  SELECT
    *
  FROM
    empresas
  WHERE ruc LIKE CONCAT('%', _criterio, '%');
  ELSEIF _tipoBusqueda = 'RAZONSOCIAL'
  THEN
  SELECT
    *
  FROM
    empresas
  WHERE razonsocial LIKE CONCAT('%', _criterio, '%');
  ELSEIF _tipoBusqueda = 'NOMBRECOMERCIAL'
  THEN
  SELECT
    *
  FROM
    empresas
  WHERE nomcomercial LIKE CONCAT('%', _criterio, '%');
  END IF;
END $$

DELIMITER $$

-- para la interfaz de productos (modal otro)
DELIMITER $$
CREATE PROCEDURE spRegisterProducto(
IN _idsubcategoria INT,
IN _idmarca INT,
IN _descripcion VARCHAR(50),
IN _precio DECIMAL(7,2),
IN _presentacion VARCHAR(40),
IN _undmedida VARCHAR(40),
IN _cantidad DECIMAL(10,2),
IN _img 		VARCHAR(255)
)
BEGIN
	INSERT INTO productos (idsubcategoria, idmarca, descripcion, precio, presentacion, undmedida,cantidad,img) 
					VALUES (_idsubcategoria,_idmarca,_descripcion,_precio,_presentacion,_undmedida,_cantidad,_img);
END$$
DELIMITER $$

CREATE OR REPLACE VIEW vista_productos_completa AS
SELECT 
  p.idproducto,
  CONCAT(s.subcategoria, ' ', p.descripcion) AS producto,
  p.precio,
  p.presentacion,
  p.undmedida,
  p.cantidad,
  p.img,
  s.idsubcategoria,
  s.subcategoria,
  c.idcategoria,
  c.categoria,
  m.idmarca,
  m.nombre AS marca
FROM productos p
INNER JOIN subcategorias s ON p.idsubcategoria = s.idsubcategoria
INNER JOIN categorias c ON s.idcategoria = c.idcategoria
INNER JOIN marcas m ON p.idmarca = m.idmarca;



-- DIN PRODUCTO

CREATE PROCEDURE spGetPersonaById(
IN _idpersona INT
)
BEGIN
SELECT * FROM personas WHERE idpersona = _idpersona;
END $$

DELIMITER $$


DELIMITER $$

CREATE PROCEDURE spGetEmpresaById(
IN _idempresa INT
)
BEGIN
SELECT * FROM empresas WHERE idempresa = _idempresa;
END $$
DELIMITER $$


CREATE PROCEDURE spUpdatePersona(
)
-- call spGetEmpresaById(6)

DELIMITER $$
 CREATE PROCEDURE spGetServicioBySubcategoria(
 IN _idsubcategoria INT
 )
 BEGIN
 SELECT s.idservicio, s.idsubcategoria, s.servicio FROM servicios s WHERE idsubcategoria = _idsubcategoria;
 END $$

-- drop procedure spGetServicioBySubcategoria;
-- call spGetServicioBySubcategoria(50)
-- select * from servicios;
DROP PROCEDURE IF EXISTS spGetVehiculoByCliente
DELIMITER $$
CREATE PROCEDURE spGetVehiculoByCliente(
IN _idcliente INT
)
BEGIN
SELECT 
v.idvehiculo,
v.placa
FROM vehiculos v
LEFT JOIN propietarios p 
ON v.idvehiculo = p.idvehiculo
LEFT JOIN clientes c
ON c.idcliente = p.idcliente
WHERE p.idcliente = _idcliente;
END $$

-- experimento:
DROP PROCEDURE IF EXISTS spGetVehiculoByCliente
DELIMITER $$
CREATE PROCEDURE spGetVehiculoByCliente(
IN _idcliente INT
)
BEGIN
SELECT 
  CONCAT(tv.tipov, ' ', ma.nombre, ' ', v.color, ' (', v.placa, ')') AS vehiculo
FROM vehiculos v
LEFT JOIN modelos m ON v.idmodelo = m.idmodelo
LEFT JOIN tipovehiculos tv ON m.idtipov = tv.idtipov
LEFT JOIN marcas ma ON m.idmarca = ma.idmarca
WHERE p.idcliente = _idcliente;
END $$

SELECT 
  CONCAT(tv.tipov, ' ', ma.nombre, ' ', v.color, ' (', v.placa, ')') AS vehiculo
FROM vehiculos v
LEFT JOIN modelos m ON v.idmodelo = m.idmodelo
LEFT JOIN tipovehiculos tv ON m.idtipov = tv.idtipov
LEFT JOIN marcas ma ON m.idmarca = ma.idmarca;



-- call spGetVehiculoByCliente(7)
-- select * from propietarios;
-- select * from empresas;
-- select * from clientes;
-- select * from vehiculos;
-- select 
-- CREATE PROCEDURE spUpdatePersona()

-- call spGetEmpresaById(6)               
-- call spBuscarEmpresa ('nombrecomercial', 'SAC');
-- select * from categorias;
-- call spRegistrarVehiculoYPropietario(1,'345345','2025','987987987','rojo','Allinol','20','dni',1);
-- call spGetClienteByDni('40');
-- select * from propietarios;
-- select * from clientes;
-- select * from personas;