/* Registrar un nuevo cliente*/
-- insert into contactabilidad (contactabilidad) values ('redes sociales');

 -- select * from clientes;
-- drop procedure spRegisterPersona;
 -- call spRegisterCliente('empresa',null, null,null,null,null,'correoempresa@gmail.com',null,null,'nueva salud','empresa SAC','912498430','12345678948',1);
 
 
DELIMITER $$
create procedure spRegisterPersona(
in _nombres 		varchar(50),
in _apellidos 		varchar(50),
in _tipodoc 		varchar(30),
in _numdoc 			varchar(20),
in _direccion 		varchar(70),
in _correo 			varchar(100),
in _telprincipal 	varchar(20),
in _telalternativo 	varchar(20)
)
begin
INSERT INTO personas (nombres, apellidos, tipodoc, numdoc, direccion, correo, telprincipal, telalternativo)
		VALUES (_nombres, _apellidos, _tipodoc, _numdoc, _direccion, _correo, _telprincipal, _telalternativo);
        SELECT LAST_INSERT_ID() AS idpersona;
END $$
DELIMITER $$

DELIMITER $$
create procedure spRegisterClientePersona(
in _idpersona int,
in _idempresa int,
in _idcontactabilidad int
)
begin
INSERT INTO clientes (idpersona, idempresa, idcontactabilidad)
		VALUES (_idpersona, NULL, _idcontactabilidad);
end $$
DELIMITER $$

DELIMITER $$
create procedure spRegisterClienteEmpresa(
in _idpersona int,
in _idempresa int,
in _idcontactabilidad int
)
begin 
INSERT INTO clientes (idpersona, idempresa, idcontactabilidad)
		VALUES (NULL, _idempresa, _idcontactabilidad);
end $$
DELIMITER $$

create procedure spRegisterEmpresa(
in _nomcomercial varchar(80),
in _razonsocial varchar(80),
in _telefono varchar(20),
in _ruc char(11)
)
begin
insert into empresas (nomcomercial, razonsocial, telefono, ruc) values
(_nomcomercial, _razonsocial, _telefono, _ruc);

 SELECT LAST_INSERT_ID() AS idempresa; 
end $$
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
	DECLARE _idpersona INT default null;
	DECLARE _idempresa INT default null;
	
	START TRANSACTION;
	
	/* si en el radiobutton del formulario se escogio persona, se insertara ahi ademas que en la tabla cliente*/
	IF _tipo = 'persona' THEN
		 -- INSERT INTO personas (nombres, apellidos, tipodoc, numdoc, direccion, correo, telprincipal, telalternativo)
		 -- VALUES (_nombres, _apellidos, _tipodoc, _numdoc, _direccion, _correo, _telprincipal, _telalternativo);
        
        CALL spRegisterPersona(_nombres,_apellidos,_tipodoc,_numdoc,_direccion,_correo,_telprincipal,_telalternativo); -- para no sobrecargar este sp se hace una llamda de procedimiento 
		
		SET _idpersona = LAST_INSERT_ID(); /*hace de que ahora el valor que tiene el id de persona es el ultimo del que se tiene registro */
		
		CALL spRegisterClientePersona(_idpersona,null,_idcontactabilidad); 
		
		/* ahora si se selecciono empresa, se insertara ahi y luego al de cliente*/
		
		ELSEIF _tipo = 'empresa' THEN
		
        call spRegisterEmpresa(_nomcomercial, _razonsocial, _telefono, _ruc);
		
		SET _idempresa = LAST_INSERT_ID(); /*lo mismo, solo que ahora se le asigna el ultimo id que se ha registrado de la tabla de empresa*/
		
		call spRegisterClienteEmpresa(null,_idempresa, _idcontactabilidad);
		
		END IF;
		
		COMMIT;
		
		SELECT COALESCE(_idpersona, _idempresa) AS idcliente; /* esta linea devuelve el id del cliente insertado*/
		
		END $$
		DELIMITER $$
        
        DELIMITER $$
        insert into contactabilidad (contactabilidad) values 
        ('folletos'),
        ('Campaña publicitaria'),
        ('Recomendacion');
        DELIMITER $$
        -- select * from contactabilidad;
        -- drop procedure spGetAllContactabilidad;
        DELIMITER $$
        create procedure spGetAllContactabilidad()
        BEGIN 
        select * from contactabilidad order by contactabilidad ASC;
        END $$
        DELIMITER $$
        
        
        
        
        
