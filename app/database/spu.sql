/* Registrar un nuevo cliente*/
-- insert into contactabilidad (contactabilidad) values ('redes sociales');
 -- select * from empresas;
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

DELIMITER $$


 DELIMITER $$
 CREATE PROCEDURE spRegisterVehiculo(
IN _idmodelo INT,
IN _placa CHAR(7),
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

-- CALL spGetModelosByTipoMarca(2,3);
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

-- call spBuscarEmpresa ('nombrecomercial', 'SAC');
-- select * from clientes;
-- call spRegistrarVehiculoYPropietario(1,'345345','2025','987987987','rojo','Allinol','20','dni',1);
DELIMITER $$

DROP PROCEDURE IF EXISTS spRegistrarVehiculoYPropietario;
DELIMITER $$
CREATE PROCEDURE spRegistrarVehiculoYPropietario(
    IN _idmodelo INT,
    IN _placa CHAR(7),
    IN _anio CHAR(4),
    IN _numserie VARCHAR(20),
    IN _color VARCHAR(50),
    IN _tipocombustible VARCHAR(30),
    IN _criterio VARCHAR(100),
    IN _tipoBusqueda VARCHAR(20),
    IN _idcliente INT
)
BEGIN
    DECLARE _idvehiculo INT;
    DECLARE _idcliente INT;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        -- Si ocurre un error, revierte la transacción
        SELECT 'Cliente encontrado:', _idcliente;
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error al registrar vehículo o propietario';
    END;

    -- Inicia la transacción
    START TRANSACTION;

    -- 1. Registrar el vehículo y obtener su ID
    INSERT INTO vehiculos (idmodelo, placa, anio, numserie, color, tipocombustible)
    VALUES (_idmodelo, _placa, _anio, _numserie, _color, _tipocombustible);
    
    SET _idvehiculo = LAST_INSERT_ID();

    -- 2. Buscar el ID del cliente según el criterio de búsqueda
    IF _tipoBusqueda = 'DNI' THEN
        SELECT idcliente INTO _idcliente FROM clientes WHERE idpersona = (SELECT idpersona FROM personas WHERE numdoc = _criterio);
    ELSEIF _tipoBusqueda = 'NOMBRE' THEN
        SELECT idcliente INTO _idcliente FROM clientes WHERE idpersona = 
            (SELECT idpersona FROM personas WHERE CONCAT(nombres, ' ', apellidos) LIKE CONCAT('%', _criterio, '%') LIMIT 1);
    ELSEIF _tipoBusqueda = 'RUC' THEN
        SELECT idcliente INTO _idcliente FROM clientes WHERE idempresa = (SELECT idempresa FROM empresas WHERE ruc = _criterio);
    ELSEIF _tipoBusqueda = 'RAZONSOCIAL' THEN
        SELECT idcliente INTO _idcliente FROM clientes WHERE idempresa = 
            (SELECT idempresa FROM empresas WHERE razonsocial LIKE CONCAT('%', _criterio, '%') LIMIT 1);
    ELSEIF _tipoBusqueda = 'NOMBRECOMERCIAL' THEN
        SELECT idcliente INTO _idcliente FROM clientes WHERE idempresa = 
            (SELECT idempresa FROM empresas WHERE nomcomercial LIKE CONCAT('%', _criterio, '%') LIMIT 1);
    END IF;

    -- Si el cliente no existe, lanzar un error
    IF _idcliente IS NULL THEN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Cliente no encontrado';
    END IF;

    -- 3. Insertar al propietario usando el ID del cliente y del vehículo recién creado
    INSERT INTO propietarios (idcliente, idvehiculo, fechainicio)
    VALUES (_idcliente, _idvehiculo, NOW());

    -- Confirmar la transacción
    COMMIT;
END$$

DELIMITER $$

DROP PROCEDURE IF EXISTS spGetClienteByDni;
DELIMITER $$
CREATE PROCEDURE spGetClienteByDni(
IN _dni CHAR(20)
)
BEGIN
SELECT c.idcliente
FROM clientes c
INNER JOIN personas p ON c.idpersona = p.idpersona
WHERE p.numdoc LIKE CONCAT('%', _dni, '%') LIMIT 1;
END $$
DELIMITER $$

-- call spGetClienteByDni('40');

DROP PROCEDURE IF EXISTS spGetOrCreateClientePersona;
DELIMITER $$
CREATE PROCEDURE spGetOrCreateClientePersona(
IN _nombres VARCHAR(50),
IN _apellidos VARCHAR(50),
IN _tipodoc VARCHAR(30),
IN _numdoc CHAR(20),
IN _direccion VARCHAR(70),
IN _correo VARCHAR(100),
IN _telprincipal VARCHAR(20),
IN _telalternativo VARCHAR(20),
IN _idcontactabilidad INT
)
BEGIN 
 DECLARE _idcliente INT;
 
 SELECT c.idcliente
 INTO _idcliente
 FROM clientes c 
 INNER JOIN personas p ON c.idpersona = p.idpersona
 WHERE p.numdoc = _numdoc
 LIMIT 1;
 IF _idcliente IS NULL THEN
 INSERT INTO personas(nombres, apellidos, tipodoc, numdoc, direccion, correo, telprincipal, telalternativo) VALUES
 (_nombres,_apellidos,_tipodoc,_numdoc,_direccion,_correo,_telprincipal,_telalternativo);
 SET _idcliente = LAST_INSERT_ID();
 
 INSERT INTO clientes (idpersona,idcontactabilidad)
 VALUES (_idcliente,_idcontactabilidad);
 
 SET _idcliente = LAST_INSERT_ID();
 END IF;
 
 SELECT _idcliente AS idcliente;
 
 END $$
 DELIMITER $$
 
 DROP PROCEDURE IF EXISTS spGetOrCreatePropietario;
 DELIMITER $$
 CREATE PROCEDURE spGetOrCreatePropietario(
 IN _idcliente INT,
 IN _idvehiculo INT
 )
 
 BEGIN 
 DECLARE _idpropietario INT;
 SELECT idpropietario
 INTO _idpropietario
 FROM propietarios
 WHERE idcliente = _idcliente
 AND idvehiculo = _idvehiculo
 AND fechafinal IS NULL
 LIMIT 1;
 
 IF _idpropietario IS NULL THEN
 INSERT INTO propietarios (idcliente,idvehiculo, fechainicio)
 VALUES (_idcliente,_idvehiculo,NOW());
 SET _idpropietario = LAST_INSERT_ID();
 END IF;
 
 SELECT _idpropietario AS idpropietario;
 END $$
 DELIMITER $$
 /*
 drop procedure if exists spRegisterVehiculo;
 delimiter $$
 create procedure spRegisterVehiculo(
 in _idmodelo int,
 in _placa char(7),
 in _anio char(4),
 in _numserie varchar(20) ,
 in _color varchar(50),
 in _tipocombustible varchar(30)
 )
 begin
 insert into vehiculos (idmodelo,placa,anio,numserie,color,tipocombustible)
 values (_idmodelo,_placa,_anio,_numserie,_color,_tipocombustible);
 
 select last_insert_id() as idvehiculo;
 end $$
 delimiter ;
 */
 DROP PROCEDURE IF EXISTS spRegistrarVehiculoCompleto;
 DELIMITER $$
 CREATE PROCEDURE spRegistrarVehiculoCompleto(
 IN _idmodelo INT,
 IN _placa CHAR(7),
 IN _anio CHAR(4),
 IN _numserie VARCHAR(20),
IN _color VARCHAR(50),
IN _tipocombustible VARCHAR(30),
IN _nombres VARCHAR(50),
IN _apellidos VARCHAR(50),
IN _tipodoc VARCHAR(30),
IN _numdoc CHAR(20),
IN _direccion VARCHAR(70),
IN _correo VARCHAR(100),
IN _telprincipal VARCHAR(20),
IN _telalternativo VARCHAR(20),
IN _idcontactabilidad INT
 )
 BEGIN
 
 DECLARE _idcliente INT;
 DECLARE _idvehiculo INT;
 DECLARE _idpropietario INT;
 
 START TRANSACTION;
 
 CALL spGetOrCreateClientePersona(
 _nombres,_apellidos,_tipodoc,_numdoc,_direccion,_correo,_telprincipal,_telalternativo,_idcontactabilidad
 );
 SELECT @idcliente := idcliente FROM DUAL;
 
 SET _idcliente = @idcliente;
 
 CALL spRegisterVehiculo(
 _idmodelo,_placa,_anio,_numserie,_color,_tipocombustible);
 
 SELECT @idvehiculo := idvehiculo FROM DUAL;
 SET _idvehiculo = @idvehiculo;
 
 CALL spGetOrCreatePropietario(_idcliente,_idvehiculo);
 SELECT @idpropietario := idpropietario FROM DUAL;
 SET _idpropietario = @idpropietario;
 
 COMMIT;
 
 SELECT
 _idcliente AS idcliente,
 _idvehiculo AS idvehiculo,
 _idpropietario AS idpropietario;
 END $$ 
 DELIMITER $$
 






 
