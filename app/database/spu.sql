-- Cambiar delimitador para definir procedimiento
DELIMITER $$
-- 1) Registrar cliente (persona)
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

-- 2) Registrar cliente (empresa)
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

-- 3) Registrar vehículo y propietario
DROP PROCEDURE IF EXISTS spRegisterVehiculo$$
CREATE PROCEDURE spRegisterVehiculo(
  IN _idmodelo INT,
  IN _idtcombustible INT,
  IN _placa CHAR(6),
  IN _anio CHAR(4),
  IN _numserie VARCHAR(50),
  IN _color VARCHAR(20),
  IN _vin CHAR(17),
  IN _numchasis CHAR(17),
  IN _idcliente INT
)
BEGIN
  DECLARE _idvehiculo INT;
  INSERT INTO vehiculos (
    idmodelo,idtcombustible, placa, anio, numserie, color ,vin,numchasis
  )
  VALUES (
    _idmodelo, _idtcombustible, _placa, _anio, _numserie, _color,_vin,_numchasis
  );
  SET _idvehiculo = LAST_INSERT_ID();
  INSERT INTO propietarios (idcliente, idvehiculo)
  VALUES (_idcliente, _idvehiculo);
END$$

-- 4) Registrar producto
DROP PROCEDURE IF EXISTS spRegisterProducto$$
CREATE PROCEDURE spRegisterProducto(
  IN _idsubcategoria INT,
  IN _idmarca INT,
  IN _descripcion VARCHAR(50),
  IN _precio DECIMAL(7,2),
  IN _presentacion VARCHAR(40),
  IN _undmedida VARCHAR(40),
  IN _cantidad DECIMAL(10,2),
  IN _img VARCHAR(255),
  OUT _idproducto INT
)
BEGIN
  INSERT INTO productos (idsubcategoria, idmarca, descripcion, precio, presentacion, undmedida, cantidad, img) 
  VALUES (_idsubcategoria, _idmarca, _descripcion, _precio, _presentacion, _undmedida, _cantidad, _img);
  SET _idproducto = LAST_INSERT_ID();
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
END $$

DELIMITER $$

-- CALL spBuscarPersona ('dni', '761');
-- CALL spBuscarPersona ('nombre', 'herna') -- select * from personas;
-- PROCEDIMIENTO DE PRODUCTOS
-- prueba register productos
-- fin register productos
-- FIN PROCEDIMIENTO DE PRODUCTOS

-- 10) Obtener todos los tipos de vehículo
DROP PROCEDURE IF EXISTS spGetAllTipoVehiculo$$
CREATE PROCEDURE spGetAllTipoVehiculo()
BEGIN
  SELECT idtipov, tipov
  FROM tipovehiculos
  ORDER BY tipov ASC;
END$$

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
-- DIN PRODUCTO
DELIMITER $$

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
DROP PROCEDURE IF EXISTS spGetVehiculoByCliente;
DELIMITER $$
CREATE PROCEDURE spGetVehiculoByCliente(
  IN _idcliente INT
)
BEGIN
  SELECT
    v.idvehiculo,
    tv.tipov,
    ma.nombre,
    m.modelo,
    v.placa,
    v.color,
    v.anio,
    v.vin,
    v.numserie,
    tc.tcombustible,
    v.numchasis,
    v.modificado,
    CONCAT(tv.tipov, ' ', ma.nombre, ' ', v.color, ' (', v.placa, ')') AS vehiculo
  FROM vehiculos v
    LEFT JOIN propietarios p ON v.idvehiculo = p.idvehiculo
    LEFT JOIN modelos m ON v.idmodelo = m.idmodelo
    LEFT JOIN tipovehiculos tv ON m.idtipov = tv.idtipov
    LEFT JOIN marcas ma ON m.idmarca = ma.idmarca
    LEFT JOIN tipocombustibles tc ON v.idtcombustible = tc.idtcombustible
  WHERE p.idcliente = _idcliente;
END$$

-- select * from vwvehiculos;
-- CALL spGetVehiculoByCliente(2)

DROP PROCEDURE IF EXISTS spRegisterTcombustible$$
CREATE PROCEDURE spRegisterTcombustible(
IN _tcombustible VARCHAR(50)
)
BEGIN
INSERT INTO tipocombustibles (tcombustible) VALUES
(_tcombustible);
END $$

-- Restaurar delimitador por defecto
DELIMITER ;

DROP PROCEDURE IF EXISTS spGetClienteById;
DELIMITER $$
CREATE PROCEDURE spGetClienteById(
IN _idcliente INT
)
BEGIN
SELECT
  CASE
    WHEN c.idpersona IS NULL THEN em.nomcomercial
    ELSE CONCAT(pe.nombres, ' ', pe.apellidos)
  END AS propietario
FROM propietarios p
LEFT JOIN vehiculos v
  ON p.idvehiculo = v.idvehiculo
LEFT JOIN clientes c
  ON p.idcliente = c.idcliente
LEFT JOIN modelos m
  ON v.idmodelo = m.idmodelo
LEFT JOIN tipovehiculos t
  ON m.idtipov = t.idtipov
LEFT JOIN marcas ma
  ON m.idmarca = ma.idmarca
LEFT JOIN personas pe
  ON c.idpersona = pe.idpersona
LEFT JOIN empresas em
  ON c.idempresa = em.idempresa
  LEFT JOIN tipocombustibles tc
  ON v.idtcombustible = tc.idtcombustible
  WHERE c.idcliente = _idcliente;
END $$

DROP PROCEDURE IF EXISTS spRegisterOrdenServicio;
DELIMITER $$
CREATE PROCEDURE spRegisterOrdenServicio(
IN _idadmin INT,

)
BEGIN

END $$

-- Semana actual (usa la fecha de hoy en tu servidor)
CALL spListOrdenesPorPeriodo('semana', CURDATE());

-- Mes actual
CALL spListOrdenesPorPeriodo('mes', CURDATE());

-- Día específico
CALL spListOrdenesPorPeriodo('dia', '2025-04-08');

-- CALL spListOrdenesPorPeriodo('dia', '2025-04-08');
DROP PROCEDURE IF EXISTS spListOrdenesPorPeriodo;
DELIMITER $$
CREATE PROCEDURE spListOrdenesPorPeriodo(
  IN _modo   ENUM('semana','mes','dia'),
  IN _fecha  DATE
)
BEGIN
  DECLARE start_date DATE;
  DECLARE end_date   DATE;

  IF _modo = 'semana' THEN
    SET start_date = DATE_SUB(_fecha, INTERVAL WEEKDAY(_fecha) DAY);
    SET end_date   = DATE_ADD(start_date, INTERVAL 6 DAY);

  ELSEIF _modo = 'mes' THEN
    SET start_date = DATE_FORMAT(_fecha, '%Y-%m-01');
    SET end_date   = LAST_DAY(_fecha);

  ELSE
    SET start_date = _fecha;
    SET end_date   = _fecha;
  END IF;

  SELECT
    o.idorden,
    o.fechaingreso,
    o.fechasalida,
    v.placa,

    /* Nombre del PROPIETARIO (quien es dueño del vehículo) */
    CASE
      WHEN pc.idpersona IS NOT NULL THEN CONCAT(pp.nombres,' ',pp.apellidos)
      ELSE pe.nomcomercial
    END AS propietario,

    /* Nombre del CLIENTE (quien contrata el servicio) */
    CASE
      WHEN cc.idpersona IS NOT NULL THEN CONCAT(cp.nombres,' ',cp.apellidos)
      ELSE ce.nomcomercial
    END AS cliente

  FROM ordenservicios o
  JOIN vehiculos      v  ON o.idvehiculo    = v.idvehiculo

  /* Propietario → persona/empresa */
  JOIN propietarios   p  ON o.idpropietario = p.idpropietario
  JOIN clientes       pc ON p.idcliente     = pc.idcliente
  LEFT JOIN personas   pp ON pc.idpersona   = pp.idpersona
  LEFT JOIN empresas   pe ON pc.idempresa   = pe.idempresa

  /* Cliente del servicio → persona/empresa */
  JOIN clientes       cc ON o.idcliente     = cc.idcliente
  LEFT JOIN personas   cp ON cc.idpersona   = cp.idpersona
  LEFT JOIN empresas   ce ON cc.idempresa   = ce.idempresa

  WHERE DATE(o.fechaingreso) BETWEEN start_date AND end_date
    AND o.estado = 'A'
  ORDER BY o.fechaingreso;
END$$
DELIMITER ;

-- Semana actual
-- CALL spListOrdenesPorPeriodo('semana', '2025-04-15');

-- Mes actual
-- CALL spListOrdenesPorPeriodo('mes',   '2025-04-02');

-- Día concreto
-- CALL spListOrdenesPorPeriodo('dia',   '2025-04-08');

DROP PROCEDURE IF EXISTS spListOrdenesPorPeriodo;
DELIMITER $$
CREATE PROCEDURE spListOrdenesPorPeriodo(
  IN _modo   ENUM('semana','mes','dia'),
  IN _fecha  DATE
)
BEGIN
  DECLARE start_date DATE;
  DECLARE end_date   DATE;

  IF _modo = 'semana' THEN
    -- lunes de la semana de _fecha
    SET start_date = DATE_SUB(_fecha, INTERVAL WEEKDAY(_fecha) DAY);
    -- domingo siguiente
    SET end_date   = DATE_ADD(start_date, INTERVAL 6 DAY);

  ELSEIF _modo = 'mes' THEN
    -- primer día del mes
    SET start_date = DATE_FORMAT(_fecha, '%Y-%m-01');
    -- último día del mes
    SET end_date   = LAST_DAY(_fecha);

  ELSE  -- 'dia'
    SET start_date = _fecha;
    SET end_date   = _fecha;
  END IF;

  SELECT
    o.idorden,
    o.fechaingreso,
    o.fechasalida,
    v.placa,
    CASE
      WHEN c.idpersona IS NOT NULL THEN CONCAT(pe.nombres,' ',pe.apellidos)
      ELSE em.nomcomercial
    END AS propietario
  FROM ordenservicios o
  JOIN vehiculos   v  ON o.idvehiculo    = v.idvehiculo
  JOIN propietarios p  ON o.idpropietario = p.idpropietario
  JOIN clientes    c  ON p.idcliente      = c.idcliente
  LEFT JOIN personas pe ON c.idpersona    = pe.idpersona
  LEFT JOIN empresas em ON c.idempresa    = em.idempresa
  WHERE DATE(o.fechaingreso) BETWEEN start_date AND end_date
    AND o.estado = 'A'
  ORDER BY o.fechaingreso;
END$$
DELIMITER ;
-- ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ --

-- call spRegistrarOrdenServicio( )
DROP PROCEDURE IF EXISTS spRegistrarOrdenServicio;
DELIMITER $$
CREATE PROCEDURE spRegistrarOrdenServicio (
  IN  _idvehiculo     INT,
  IN  _idpropietario  INT,
  IN  _fechaIngreso   DATETIME,
  IN  _fechaSalida    DATETIME,
  IN  _detalles       JSON
)
BEGIN
  DECLARE _idorden   INT;
  DECLARE _i         INT DEFAULT 0;
  DECLARE _n         INT;
  DECLARE _iditem    INT;
  DECLARE _cantidad  DECIMAL(10,2);
  DECLARE _precio    DECIMAL(12,2);
  DECLARE _subtotal  DECIMAL(14,2);

  -- Manejador de errores: si algo falla, hace ROLLBACK y relanza
  DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
    ROLLBACK;
    RESIGNAL;
  END;

  START TRANSACTION;

    -- 1) Inserta cabecera
    INSERT INTO ordenservicios
      (idvehiculo, idpropietario, fechaingreso, fechasalida, estado)
    VALUES
      (_idvehiculo, _idpropietario, _fechaIngreso, _fechaSalida, 'A');

    SET _idorden = LAST_INSERT_ID();

    -- 2) Recorre el array JSON de detalles
    SET _n = JSON_LENGTH(_detalles);
    WHILE _i < _n DO
      SET _iditem   = JSON_UNQUOTE(JSON_EXTRACT(_detalles, CONCAT('$[', _i, '].idItem')));
      SET _cantidad = JSON_EXTRACT(_detalles,   CONCAT('$[', _i, '].cantidad'));
      SET _precio   = JSON_EXTRACT(_detalles,   CONCAT('$[', _i, '].precio'));
      SET _subtotal = _cantidad * _precio;

      INSERT INTO detalleordenservicio
        (idorden, iditem, cantidad, precio_unitario, subtotal)
      VALUES
        (_idOrden, _idItem, _cantidad, _precio, _subtotal);

      -- (Opcional) actualizar stock de repuestos
      -- UPDATE productos
      --   SET stock = stock - v_cantidad
      --   WHERE idproducto = v_idItem;

      SET _i = _i + 1;
    END WHILE;

  COMMIT;

  -- Devuelve el ID generado por si lo quieres capturar en la aplicación
  SELECT _idorden AS nuevoidorden;
END$$