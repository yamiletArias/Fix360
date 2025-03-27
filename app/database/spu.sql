/* Registrar un nuevo cliente*/

-- drop procedure spRegistrarCliente;

DELIMITER $$

CREATE PROCEDURE spRegistrarCliente(
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
	DECLARE _idpersona INT;
	DECLARE _idempresa INT;
	
	START TRANSACTION;
	
	/* si en el radiobutton del formulario se escogio persona, se insertara ahi ademas que en la tabla cliente*/
	IF _tipo = 'persona' THEN
		INSERT INTO personas (nombres, apellidos, tipodoc, numdoc, direccion, correo, telprincipal, telalternativo)
		VALUES (_nombres, _apellidos, _tipodoc, _numdoc, _direccion, _correo, _telprincipal, _telalternativo);
		
		SET _idpersona = LAST_INSERT_ID(); /*hace de que ahora el valor que tiene el id de persona es el ultimo del que se tiene registro */
		
		INSERT INTO clientes (idpersona, idempresa, idcontactabilidad)
		VALUES (_idpersona, NULL, _idcontactabilidad);
		
		/* ahora si se selecciono empresa, se insertara ahi y luego al de cliente*/
		
		ELSEIF _tipo = 'empresa' THEN
		INSERT INTO empresas (nomcomercial, razonsocial, telefono, correo, ruc)
		VALUES (_nomcomercial, _razonsocial, _telefono, _correo, _ruc);
		
		SET _idempresa = LAST_INSERT_ID(); /*lo mismo, solo que ahora se le asigna el ultimo id que se ha registrado de la tabla de empresa*/
		
		INSERT INTO clientes (idpersona, idempresa, idcontactabilidad)
		VALUES (NULL, _idempresa, _idcontactabilidad);
		
		END IF;
		
		COMMIT;
		
		SELECT COALESCE(_idpersona, _idempresa) AS idcliente; /* esta linea devuelve el id del cliente insertado*/
		
		END $$
		DELIMITER ;
