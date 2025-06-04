USE dbfix360;
/*
-- 1) Registrar un nuevo colaborador (hashear contraseña)
-- 2) Login de colaborador (verifica credenciales, estado activo y contrato vigente)
*/
-- 1) Registrar colaborador
-- CALL spRegisterColaborador('ejemplo2','ejemplo2',2)
-- CALL spRegisterColaborador('ejemplo1','ejemplo1',1)
-- select * from colaboradores where idcontrato = 1;
-- select * from egresos;
-- select * from componentes
/*
DROP PROCEDURE IF EXISTS spRegisterColaborador;
DELIMITER $$
CREATE PROCEDURE spRegisterColaborador(
  IN _namuser       VARCHAR(50),
  IN _passuser      VARCHAR(255),
  IN _idcontrato    INT
)
BEGIN
  DECLARE _hashed_pwd VARCHAR(64);

  -- Hashear la contraseña con SHA2-256
  SET _hashed_pwd = SHA2(_passuser, 256);

  -- Insertar nuevo colaborador con estado TRUE
  INSERT INTO colaboradores (idcontrato, namuser, passuser, estado)
  VALUES (_idcontrato, _namuser, _hashed_pwd, TRUE);
END$$

-- 2) Login colaborador

-- call spGetColaboradorInfo(1)

-- select * from colaboradores;
