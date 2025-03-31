/* Registrar un nuevo cliente*/
-- insert into contactabilidad (contactabilidad) values ('redes sociales');

 -- select * from empresas;
 -- select * from personas;
 -- select * from clientes;
-- drop procedure spRegisterPersona;
 -- call spRegisterCliente('empresa',null, null,null,null,null,'correoempresa@gmail.com',null,null,'nueva salud','empresa SAC','912498430','12345678948',1);
 
 

DELIMITER $$
CREATE PROCEDURE spRegisterClientePersona(
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
    DECLARE _idpersona INT;
    
    -- Insertar en la tabla personas
    INSERT INTO personas (nombres, apellidos, tipodoc, numdoc, direccion, correo, telprincipal, telalternativo)
    VALUES (_nombres, _apellidos, _tipodoc, _numdoc, _direccion, _correo, _telprincipal, _telalternativo);
    
    -- Obtener el ID de la persona insertada
    SET _idpersona = LAST_INSERT_ID();
    
    -- Insertar en la tabla clientes a la persona
    INSERT INTO clientes (idpersona, idcontactabilidad) 
    VALUES (_idpersona, _idcontactabilidad);
END $$

DELIMITER $$


DELIMITER $$

CREATE PROCEDURE spRegisterClienteEmpresa(
    IN _ruc CHAR(11),
    IN _nomcomercial VARCHAR(80),
    IN _razonsocial VARCHAR(80),
    IN _telefono VARCHAR(20),
    IN _correo VARCHAR(100),
    IN _idcontactabilidad INT
)
BEGIN
    DECLARE _idempresa INT;
    
    -- Insertar en la tabla empresas
    INSERT INTO empresas (ruc, nomcomercial, razonsocial, telefono, correo)
    VALUES (_ruc, _nomcomercial, _razonsocial, _telefono, _correo);
    
    -- Obtener el ID de la empresa insertada
    SET _idempresa = LAST_INSERT_ID();
    
    -- Insertar en la tabla clientes vinculando la empresa
    INSERT INTO clientes (idempresa, idcontactabilidad) 
    VALUES (_idempresa, _idcontactabilidad);
END $$

DELIMITER $$



        
        DELIMITER $$
        INSERT INTO contactabilidad (contactabilidad) VALUES 
        ('Redes sociales'),
        ('Folletos'),
        ('Campaña publicitaria'),
        ('Recomendacion');
        DELIMITER $$
        -- select * from contactabilidad;
        -- drop procedure spGetAllContactabilidad;
        DELIMITER $$
        CREATE PROCEDURE spGetAllContactabilidad()
        BEGIN 
        SELECT * FROM contactabilidad ORDER BY contactabilidad ASC;
        END $$
        DELIMITER $$
        
        
        DELIMITER $$

CREATE PROCEDURE spGetModelosByTipoMarca (
    IN p_idtipov INT,
    IN p_idmarca INT
)
BEGIN
    SELECT 
        m.idmodelo,
        m.modelo
    FROM Modelos m
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
CREATE PROCEDURE spGetAllTipoVehiculo()
BEGIN
SELECT
idtipov,
tipov
FROM
tipovehiculos ORDER BY tipov ASC;
END $$

DELIMITER $$
         
