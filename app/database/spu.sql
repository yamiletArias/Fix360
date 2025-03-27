/* Registrar un nuevo cliente*/
-- insert into contactabilidad (contactabilidad) values ('redes sociales');

 -- select * from clientes;
-- drop procedure spRegisterPersona;
 -- call spRegisterCliente('empresa',null, null,null,null,null,'correoempresa@gmail.com',null,null,'nueva salud','empresa SAC','912498430','12345678948',1);
 
 
DELIMITER $$
CREATE PROCEDURE spRegisterPersona(
IN _nombres 		VARCHAR(50),
IN _apellidos 		VARCHAR(50),
IN _tipodoc 		VARCHAR(30),
IN _numdoc 			VARCHAR(20),
IN _direccion 		VARCHAR(70),
IN _correo 			VARCHAR(100),
IN _telprincipal 	VARCHAR(20),
IN _telalternativo 	VARCHAR(20)
)
BEGIN
INSERT INTO personas (nombres, apellidos, tipodoc, numdoc, direccion, correo, telprincipal, telalternativo)
		VALUES (_nombres, _apellidos, _tipodoc, _numdoc, _direccion, _correo, _telprincipal, _telalternativo);
        SELECT LAST_INSERT_ID() AS idpersona;
END $$
DELIMITER $$

DELIMITER $$
CREATE PROCEDURE spRegisterClientePersona(
IN _idpersona INT,
IN _idempresa INT,
IN _idcontactabilidad INT
)
BEGIN
INSERT INTO clientes (idpersona, idempresa, idcontactabilidad)
		VALUES (_idpersona, NULL, _idcontactabilidad);
END $$
DELIMITER $$

DELIMITER $$
CREATE PROCEDURE spRegisterClienteEmpresa(
IN _idpersona INT,
IN _idempresa INT,
IN _idcontactabilidad INT
)
BEGIN 
INSERT INTO clientes (idpersona, idempresa, idcontactabilidad)
		VALUES (NULL, _idempresa, _idcontactabilidad);
END $$
DELIMITER $$

CREATE PROCEDURE spRegisterEmpresa(
IN _nomcomercial VARCHAR(80),
IN _razonsocial VARCHAR(80),
IN _telefono VARCHAR(20),
IN _ruc CHAR(11)
)
BEGIN
INSERT INTO empresas (nomcomercial, razonsocial, telefono, ruc) VALUES
(_nomcomercial, _razonsocial, _telefono, _ruc);

 SELECT LAST_INSERT_ID() AS idempresa; 
END $$
DELIMITER $$

DELIMITER $$
CREATE PROCEDURE spRegisterCliente(
	IN _tipo 					VARCHAR(10),
	IN _nombres 				VARCHAR(50),
	IN _apellidos 				VARCHAR(50),
	IN _tipodoc 				VARCHAR(30),
	IN _numdoc 					CHAR(20),
	IN _direccion 				VARCHAR(70),
	IN _correo 					VARCHAR(100),
	IN _telprincipal 			VARCHAR(20),
	IN _telalternativo 			VARCHAR(20),
	IN _nomcomercial 			VARCHAR(80),
	IN _razonsocial 			VARCHAR(80),
	IN _telefono 				VARCHAR(20),
	IN _ruc 					CHAR(11),
	IN _idcontactabilidad 	INT	
)
BEGIN
	DECLARE _idpersona INT DEFAULT NULL;
	DECLARE _idempresa INT DEFAULT NULL;
	
	START TRANSACTION;
	
	/* si en el radiobutton del formulario se escogio persona, se insertara ahi ademas que en la tabla cliente*/
	IF _tipo = 'persona' THEN
		 -- INSERT INTO personas (nombres, apellidos, tipodoc, numdoc, direccion, correo, telprincipal, telalternativo)
		 -- VALUES (_nombres, _apellidos, _tipodoc, _numdoc, _direccion, _correo, _telprincipal, _telalternativo);
        
        CALL spRegisterPersona(_nombres,_apellidos,_tipodoc,_numdoc,_direccion,_correo,_telprincipal,_telalternativo); -- para no sobrecargar este sp se hace una llamda de procedimiento 
		
		SET _idpersona = LAST_INSERT_ID(); /*hace de que ahora el valor que tiene el id de persona es el ultimo del que se tiene registro */
		
		CALL spRegisterClientePersona(_idpersona,NULL,_idcontactabilidad); 
	
		/* ahora si se selecciono empresa, se insertara ahi y luego al de cliente*/
		
		ELSEIF _tipo = 'empresa' THEN
		
        CALL spRegisterEmpresa(_nomcomercial, _razonsocial, _telefono, _ruc);
		
		SET _idempresa = LAST_INSERT_ID(); /*lo mismo, solo que ahora se le asigna el ultimo id que se ha registrado de la tabla de empresa*/
		
		CALL spRegisterClienteEmpresa(NULL,_idempresa, _idcontactabilidad);
		
		END IF;
		
		COMMIT;
		
		END $$
		DELIMITER $$
        
        DELIMITER $$
        INSERT INTO contactabilidad (contactabilidad) VALUES 
        ('Folletos'),
        ('Campaña publicitaria'),
        ('Recomendacion'),
        ('Redes sociales');
        DELIMITER $$
        -- select * from contactabilidad;
        -- drop procedure spGetAllContactabilidad;
        DELIMITER $$
        CREATE PROCEDURE spGetAllContactabilidad()
        BEGIN 
        SELECT idcontactabilidad, contactabilidad FROM contactabilidad ORDER BY contactabilidad ASC;
        END $$
        DELIMITER $$        
        
        SELECT * FROM personas;
         
