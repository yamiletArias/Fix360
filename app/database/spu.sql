
 DELIMITER $$
DROP PROCEDURE IF EXISTS spRegisterClientePersona$$
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
  INSERT INTO personas (
    nombres, apellidos, tipodoc, numdoc, numruc,
    direccion, correo, telprincipal, telalternativo
  )
  VALUES (
    _nombres, _apellidos, _tipodoc, _numdoc, _numruc,
    _direccion, _correo, _telprincipal, _telalternativo
  );
  SET _idpersona = LAST_INSERT_ID();
  INSERT INTO clientes (idpersona, idcontactabilidad)
  VALUES (_idpersona, _idcontactabilidad);
END$$

DROP PROCEDURE IF EXISTS spRegisterClienteEmpresa$$
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
  INSERT INTO empresas (
    ruc, nomcomercial, razonsocial, telefono, correo
  )
  VALUES (
    _ruc, _nomcomercial, _razonsocial, _telefono, _correo
  );
  SET _idempresa = LAST_INSERT_ID();
  INSERT INTO clientes (idempresa, idcontactabilidad)
  VALUES (_idempresa, _idcontactabilidad);
END$$

DROP PROCEDURE IF EXISTS spRegisterVehiculo$$
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
  INSERT INTO vehiculos (
    idmodelo, placa, anio, numserie, color, tipocombustible
  )
  VALUES (
    _idmodelo, _placa, _anio, _numserie, _color, _tipocombustible
  );
  SET _idvehiculo = LAST_INSERT_ID();
  INSERT INTO propietarios (idcliente, idvehiculo)
  VALUES (_idcliente, _idvehiculo);
END$$

DROP PROCEDURE IF EXISTS spRegisterProducto$$
CREATE PROCEDURE spRegisterProducto(
  IN _idsubcategoria INT,
  IN _idmarca INT,
  IN _descripcion VARCHAR(50),
  IN _precio DECIMAL(7,2),
  IN _presentacion VARCHAR(40),
  IN _undmedida VARCHAR(40),
  IN _cantidad DECIMAL(10,2),
  IN _img VARCHAR(255)
)
BEGIN
  INSERT INTO productos (
    idsubcategoria, idmarca, descripcion, precio,
    presentacion, undmedida, cantidad, img
  )
  VALUES (
    _idsubcategoria, _idmarca, _descripcion, _precio,
    _presentacion, _undmedida, _cantidad, _img
  );
END$$

-- 5) Registrar servicio
DROP PROCEDURE IF EXISTS spRegisterServicio$$
CREATE PROCEDURE spRegisterServicio(
  IN _idsubcategoria INT,
  IN _servicio VARCHAR(255)
)
BEGIN
  INSERT INTO servicios (idsubcategoria, servicio)
  VALUES (_idsubcategoria, _servicio);
END$$

-- 6) Obtener todas las contactabilidades
DROP PROCEDURE IF EXISTS spGetAllContactabilidad$$
CREATE PROCEDURE spGetAllContactabilidad()
BEGIN
  SELECT * FROM contactabilidad
  ORDER BY contactabilidad ASC;
END$$

-- 7) Obtener todas las categorías
DROP PROCEDURE IF EXISTS spGetAllCategoria$$
CREATE PROCEDURE spGetAllCategoria()
BEGIN
  SELECT * FROM categorias;
END$$

-- 8) Obtener todas las marcas de producto
DROP PROCEDURE IF EXISTS spGetAllMarcaProducto$$
CREATE PROCEDURE spGetAllMarcaProducto()
BEGIN
  SELECT * FROM marcas
  WHERE tipo != 'vehiculo';
END$$

-- 9) Obtener todas las marcas de vehículo
DROP PROCEDURE IF EXISTS spGetAllMarcaVehiculo$$
CREATE PROCEDURE spGetAllMarcaVehiculo()
BEGIN
  SELECT * FROM marcas
  WHERE tipo = 'vehiculo'
  ORDER BY nombre ASC;
<<<<<<< HEAD
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
    
    SELECT LAST_INSERT_ID() AS idproducto;
END$$


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
SELECT idproducto, descripcion FROM productos ORDER BY idproducto DESC LIMIT 5;
=======
END$$

-- 10) Obtener todos los tipos de vehículo
DROP PROCEDURE IF EXISTS spGetAllTipoVehiculo$$
CREATE PROCEDURE spGetAllTipoVehiculo()
BEGIN
  SELECT idtipov, tipov
  FROM tipovehiculos
  ORDER BY tipov ASC;
END$$
>>>>>>> 4db45f40872fbcd49ef01e50c51865c619e11939

-- 11) Obtener subcategorías por categoría
DROP PROCEDURE IF EXISTS spGetSubcategoriaByCategoria$$
CREATE PROCEDURE spGetSubcategoriaByCategoria(
  IN _idcategoria INT
)
BEGIN
  SELECT s.idsubcategoria, s.subcategoria
  FROM subcategorias s
  WHERE s.idcategoria = _idcategoria;
END$$

-- 12) Obtener modelos por tipo y marca
DROP PROCEDURE IF EXISTS spGetModelosByTipoMarca$$
CREATE PROCEDURE spGetModelosByTipoMarca(
  IN p_idtipov INT,
  IN p_idmarca INT
)
BEGIN
  SELECT m.idmodelo, m.modelo
  FROM Modelos m
  WHERE m.idtipov = p_idtipov
    AND m.idmarca = p_idmarca;
END$$

<<<<<<< HEAD
-- DIN PRODUCTO
DELIMITER $$
=======
-- 13) Obtener servicios por subcategoría
DROP PROCEDURE IF EXISTS spGetServicioBySubcategoria$$
CREATE PROCEDURE spGetServicioBySubcategoria(
  IN _idsubcategoria INT
)
BEGIN
  SELECT s.idservicio, s.idsubcategoria, s.servicio
  FROM servicios s
  WHERE s.idsubcategoria = _idsubcategoria;
END$$

-- 14) Obtener persona por ID
DROP PROCEDURE IF EXISTS spGetPersonaById$$
>>>>>>> 4db45f40872fbcd49ef01e50c51865c619e11939
CREATE PROCEDURE spGetPersonaById(
  IN _idpersona INT
)
BEGIN
  SELECT * FROM personas
  WHERE idpersona = _idpersona;
END$$

-- 15) Obtener empresa por ID
DROP PROCEDURE IF EXISTS spGetEmpresaById$$
CREATE PROCEDURE spGetEmpresaById(
  IN _idempresa INT
)
BEGIN
  SELECT * FROM empresas
  WHERE idempresa = _idempresa;
END$$

-- 16) Buscar persona (por DNI o NOMBRE)
DROP PROCEDURE IF EXISTS spBuscarPersona$$
CREATE PROCEDURE spBuscarPersona(
  IN _tipoBusqueda VARCHAR(20),
  IN _criterio VARCHAR(100)
)
BEGIN
  IF _tipoBusqueda = 'DNI' THEN
    SELECT * FROM personas
    WHERE numdoc LIKE CONCAT('%', _criterio, '%');
  ELSEIF _tipoBusqueda = 'NOMBRE' THEN
    SELECT * FROM personas
    WHERE CONCAT(nombres, ' ', apellidos)
      LIKE CONCAT('%', _criterio, '%');
  END IF;
END$$

-- 17) Buscar empresa (por RUC, RAZONSOCIAL o NOMBRECOMERCIAL)
DROP PROCEDURE IF EXISTS spBuscarEmpresa$$
CREATE PROCEDURE spBuscarEmpresa(
  IN _tipoBusqueda VARCHAR(20),
  IN _criterio VARCHAR(100)
)
BEGIN
  IF _tipoBusqueda = 'RUC' THEN
    SELECT * FROM empresas
    WHERE ruc LIKE CONCAT('%', _criterio, '%');
  ELSEIF _tipoBusqueda = 'RAZONSOCIAL' THEN
    SELECT * FROM empresas
    WHERE razonsocial LIKE CONCAT('%', _criterio, '%');
  ELSEIF _tipoBusqueda = 'NOMBRECOMERCIAL' THEN
    SELECT * FROM empresas
    WHERE nomcomercial LIKE CONCAT('%', _criterio, '%');
  END IF;
END$$

-- 18) Obtener vehículos por cliente
DROP PROCEDURE IF EXISTS spGetVehiculoByCliente$$
CREATE PROCEDURE spGetVehiculoByCliente(
  IN _idcliente INT
)
BEGIN
  SELECT
    v.idvehiculo,
    CONCAT(tv.tipov, ' ', ma.nombre, ' ', v.color, ' (', v.placa, ')') AS vehiculo
  FROM vehiculos v
    LEFT JOIN propietarios p ON v.idvehiculo = p.idvehiculo
    LEFT JOIN modelos m ON v.idmodelo = m.idmodelo
    LEFT JOIN tipovehiculos tv ON m.idtipov = tv.idtipov
    LEFT JOIN marcas ma ON m.idmarca = ma.idmarca
  WHERE p.idcliente = _idcliente;
END$$

-- Restaurar delimitador por defecto

DROP PROCEDURE IF EXISTS spRegisterOrdenServicio$$
DELIMITER $$
CREATE PROCEDURE spRegisterOrdenServicio (
  IN _idadmin      INT,
  IN _idmecanico   INT,
  IN _idpropietario INT,
  IN _idcliente    INT,
  IN _idvehiculo   INT,
  IN _kilometraje  DECIMAL(10,2),
  IN _observaciones VARCHAR(255),
  IN _ingresogrua  BOOLEAN,
  IN _fechaingreso DATETIME,
  IN _fecharecordatorio DATE
)
BEGIN
  INSERT INTO ordenservicios (
    idadmin, idmecanico, idpropietario, idcliente,
    idvehiculo, kilometraje, observaciones,
    ingresogrua, fechaingreso, fecharecordatorio
  )
  VALUES (
    _idadmin, _idmecanico, _idpropietario, _idcliente,
    _idvehiculo, _kilometraje, _observaciones,
    _ingresogrua, _fechaingreso, _fecharecordatorio
  );
  SELECT LAST_INSERT_ID() AS idorden;
END $$


DROP PROCEDURE IF EXISTS spInsertDetalleOrden$$
DELIMITER $$
CREATE PROCEDURE spInsertDetalleOrden (
  IN _idorden   INT,
  IN _idservicio INT,
  IN _precio     DECIMAL(10,2)
)
BEGIN
  INSERT INTO detalleordenservicios (
    idorden, idservicio, precio
  )
  VALUES (
    _idorden, _idservicio, _precio
  );
END $$

DELIMITER ;