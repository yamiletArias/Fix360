USE dbfix360;
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
  IN  _namuser    VARCHAR(50),
  IN  _passuser   VARCHAR(255)
)
BEGIN
  DECLARE _hashed_pwd    VARCHAR(64);
  DECLARE _idcolaborador INT;
  DECLARE _idrol         INT;
  DECLARE _count         INT;
  DECLARE _fullname      VARCHAR(101);

  SET _hashed_pwd = SHA2(_passuser, 256);

  -- Validar colaborador activo
  SELECT c.idcolaborador
    INTO _idcolaborador
    FROM colaboradores c
   WHERE c.namuser  = _namuser
     AND c.passuser = _hashed_pwd
     AND c.estado   = TRUE
   LIMIT 1;

  -- Verificar contrato vigente
  SELECT COUNT(*) INTO _count
    FROM contratos t
    JOIN colaboradores c2 ON c2.idcontrato = t.idcontrato
   WHERE c2.idcolaborador = _idcolaborador
     AND t.fechainicio    <= CURDATE()
     AND (t.fechafin IS NULL OR t.fechafin >= CURDATE());

  IF _count = 1 THEN
    -- Obtener rol y nombre completo
    SELECT 
      t.idrol,
      CONCAT(p.nombres, ' ', p.apellidos)
    INTO
      _idrol,
      _fullname
    FROM contratos t
    JOIN colaboradores c3 ON c3.idcontrato = t.idcontrato
    JOIN personas     p  ON p.idpersona   = t.idpersona
    WHERE c3.idcolaborador = _idcolaborador
      AND t.fechainicio    <= CURDATE()
      AND (t.fechafin IS NULL OR t.fechafin >= CURDATE())
    LIMIT 1;

    -- Primer result‑set
    SELECT 
      'SUCCESS'      AS STATUS,
      _idcolaborador AS idcolaborador,
      _fullname      AS nombreCompleto;

    -- Segundo result‑set: permisos “manual” en JSON
    SELECT 
      IFNULL(
        CONCAT(
          '[', 
          GROUP_CONCAT(CONCAT('"', REPLACE(v.ruta, '"', '\"'), '"')), 
          ']'
        ),
        '[]'
      ) AS permisos
    FROM rolVistas rv
    JOIN vistas    v ON v.idvista = rv.idvista
    WHERE rv.idrol = _idrol;

  ELSE
    -- Login fallido
    SELECT 
      'FAILURE'       AS STATUS,
      NULL            AS idcolaborador,
      NULL            AS nombreCompleto;
  END IF;
END$$




-- select * from colaboradores;
