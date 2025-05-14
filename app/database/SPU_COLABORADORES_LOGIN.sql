USE dbfix360;

DROP PROCEDURE IF EXISTS `spu_colaboradores_login`;
DELIMITER //
CREATE PROCEDURE spu_colaboradores_login(IN _namuser VARCHAR(50))
BEGIN
    SELECT 
        C.idcolaborador,
        C.namuser,
        C.passuser,
        P.nombres,
        P.apellidos,
        R.rol
    FROM colaboradores C
    INNER JOIN contratos CO ON CO.idcontrato = C.idcontrato
    INNER JOIN personas P ON P.idpersona = CO.idpersona
    INNER JOIN roles R ON R.idrol = CO.idrol
    WHERE C.namuser = _namuser 
    AND C.estado = TRUE;
END //
DELIMITER ;

CALL spu_colaboradores_login('juan.perez'); -- Admin
CALL spu_colaboradores_login('carlos.diaz'); -- Gerente
CALL spu_colaboradores_login('luis.rodriguez'); -- Mecanico




/*
-- 1) Registrar un nuevo colaborador (hashear contraseña)
-- 2) Login de colaborador (verifica credenciales, estado activo y contrato vigente)
*/
-- 1) Registrar colaborador
CALL spRegisterColaborador('ejemplo1','ejemplo1',1)

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
DROP PROCEDURE IF EXISTS spLoginColaborador;
DELIMITER $$
CREATE PROCEDURE spLoginColaborador(
  IN _namuser    VARCHAR(50),
  IN _passuser   VARCHAR(255)
)
BEGIN
  DECLARE _hashed_pwd      VARCHAR(64);
  DECLARE _idcolaborador   INT;
  DECLARE _count           INT;

  -- Hashear la contraseña recibida
  SET _hashed_pwd = SHA2(_passuser, 256);

  -- Buscar colaborador con user/pass y estado TRUE
  SELECT c.idcolaborador
    INTO _idcolaborador
    FROM colaboradores c
   WHERE c.namuser = _namuser
     AND c.passuser = _hashed_pwd
     AND c.estado = TRUE;

  -- Contar contratos vigentes
  SELECT COUNT(*)
    INTO _count
    FROM contratos t
    JOIN colaboradores c2 ON c2.idcontrato = t.idcontrato
   WHERE c2.idcolaborador = _idcolaborador
     AND t.fechainicio <= CURDATE()
     AND (t.fechafin IS NULL OR t.fechafin >= CURDATE());

  IF _count = 1 THEN
    -- Login exitoso
    SELECT 'SUCCESS' AS STATUS, _idcolaborador AS idcolaborador;
  ELSE
    -- Credenciales inválidas o sin contrato vigente
    SELECT 'FAILURE' AS STATUS, NULL AS idcolaborador;
  END IF;
END$$
-- select * from colaboradores;
