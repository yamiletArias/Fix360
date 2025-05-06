-- Cambiar delimitador para definir procedimiento
-- 1) Registrar cliente (persona)
DROP PROCEDURE IF EXISTS spRegisterClientePersona;
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
DROP PROCEDURE IF EXISTS spRegisterClienteEmpresa;
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
DROP PROCEDURE IF EXISTS spRegisterVehiculo;
DELIMITER $$
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
DROP PROCEDURE IF EXISTS spRegisterProducto;
DELIMITER $$
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
DROP PROCEDURE IF EXISTS spRegisterServicio;
DELIMITER $$
CREATE PROCEDURE spRegisterServicio(
  IN _idsubcategoria INT,
  IN _servicio VARCHAR(255)
)
BEGIN
  INSERT INTO servicios (idsubcategoria, servicio)
  VALUES (_idsubcategoria, _servicio);
END$$

-- 6) Obtener todas las contactabilidades
DROP PROCEDURE IF EXISTS spGetAllContactabilidad;
DELIMITER $$
CREATE PROCEDURE spGetAllContactabilidad()
BEGIN
  SELECT * FROM contactabilidad
  ORDER BY contactabilidad ASC;
END$$

-- 7) Obtener todas las categorías
DROP PROCEDURE IF EXISTS spGetAllCategoria;
DELIMITER $$
CREATE PROCEDURE spGetAllCategoria()
BEGIN
  SELECT * FROM categorias;
END$$

-- 8) Obtener todas las marcas de producto
DROP PROCEDURE IF EXISTS spGetAllMarcaProducto;
DELIMITER $$
CREATE PROCEDURE spGetAllMarcaProducto()
BEGIN
  SELECT * FROM marcas
  WHERE tipo != 'vehiculo';
END$$
-- cliente 11
-- select * from ordenservicios;
-- select * from clientes;
-- 9) Obtener todas las marcas de vehículo
DROP PROCEDURE IF EXISTS spGetAllMarcaVehiculo;
DELIMITER $$
CREATE PROCEDURE spGetAllMarcaVehiculo()
BEGIN
  SELECT * FROM marcas
  WHERE tipo = 'vehiculo'
  ORDER BY nombre ASC;
END $$

-- CALL spBuscarPersona ('dni', '761');
-- CALL spBuscarPersona ('nombre', 'herna') -- select * from personas;
-- PROCEDIMIENTO DE PRODUCTOS
-- prueba register productos
-- fin register productos
-- FIN PROCEDIMIENTO DE PRODUCTOS

-- 10) Obtener todos los tipos de vehículo
DROP PROCEDURE IF EXISTS spGetAllTipoVehiculo;
DELIMITER $$
CREATE PROCEDURE spGetAllTipoVehiculo()
BEGIN
  SELECT idtipov, tipov
  FROM tipovehiculos
  ORDER BY tipov ASC;
END$$

-- 11) Obtener subcategorías por categoría
DROP PROCEDURE IF EXISTS spGetSubcategoriaByCategoria;
DELIMITER $$
CREATE PROCEDURE spGetSubcategoriaByCategoria(
  IN _idcategoria INT
)
BEGIN
  SELECT s.idsubcategoria, s.subcategoria
  FROM subcategorias s
  WHERE s.idcategoria = _idcategoria;
END$$

-- 12) Obtener modelos por tipo y marca
DROP PROCEDURE IF EXISTS spGetModelosByTipoMarca;
DELIMITER $$
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

-- 13) Obtener servicios por subcategoría
DROP PROCEDURE IF EXISTS spGetServicioBySubcategoria;
DELIMITER $$
CREATE PROCEDURE spGetServicioBySubcategoria(
  IN _idsubcategoria INT
)
BEGIN
  SELECT s.idservicio, s.idsubcategoria, s.servicio
  FROM servicios s
  WHERE s.idsubcategoria = _idsubcategoria;
END$$

-- 14) Obtener persona por ID
DROP PROCEDURE IF EXISTS spGetPersonaById;
DELIMITER $$
CREATE PROCEDURE spGetPersonaById(
  IN _idpersona INT
)
BEGIN
  SELECT * FROM personas
  WHERE idpersona = _idpersona;
END$$

-- 15) Obtener empresa por ID
DROP PROCEDURE IF EXISTS spGetEmpresaById;
DELIMITER $$
CREATE PROCEDURE spGetEmpresaById(
  IN _idempresa INT
)
BEGIN
  SELECT * FROM empresas
  WHERE idempresa = _idempresa;
END$$

-- 16) Buscar persona (por DNI o NOMBRE)
DROP PROCEDURE IF EXISTS spBuscarPersona;
DELIMITER $$
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
DROP PROCEDURE IF EXISTS spBuscarEmpresa;
DELIMITER $$
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
-- select * from vwclientespersona;
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

DROP PROCEDURE IF EXISTS spRegisterTcombustible;
DELIMITER $$
CREATE PROCEDURE spRegisterTcombustible(
IN _tcombustible VARCHAR(50)
)
BEGIN
INSERT INTO tipocombustibles (tcombustible) VALUES
(_tcombustible);
END $$

-- Restaurar delimitador por defecto

DROP PROCEDURE IF EXISTS spGetClienteById;
DELIMITER $$
CREATE PROCEDURE spGetClienteById(
IN _idcliente INT
)
BEGIN
SELECT
  CASE
    WHEN c.idpersona IS NULL THEN em.nomcomercial
    ELSE CONCAT(pe.apellidos,  ' ' ,pe.nombres)
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
-- Semana actual
-- CALL spListOrdenesPorPeriodo('semana', '2025-04-15');

-- Mes actual
-- CALL spListOrdenesPorPeriodo('mes',   '2025-04-02');

-- Día concreto
-- CALL spListOrdenesPorPeriodo('dia',   '2025-04-08');
-- ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ --
-- select * from o;
-- call spRegisterOrdenServicio(1,1,1,1,200,'nose',True,'2025/10/10',null)
-- call insert
-- SP para insertar la cabecera de la orden de servicio
DROP PROCEDURE IF EXISTS spRegisterOrdenServicio;
DELIMITER $$
CREATE PROCEDURE spRegisterOrdenServicio (
  IN _idadmin           INT,
  IN _idpropietario     INT,
  IN _idcliente         INT,
  IN _idvehiculo        INT,
  IN _kilometraje       DECIMAL(10,2),
  IN _observaciones     VARCHAR(255),
  IN _ingresogrua       BOOLEAN,
  IN _fechaingreso      DATETIME,
  IN _fecharecordatorio DATE
)
BEGIN
  INSERT INTO ordenservicios (
    idadmin,
    idpropietario,
    idcliente,
    idvehiculo,
    kilometraje,
    observaciones,
    ingresogrua,
    fechaingreso,
    fecharecordatorio
  )
  VALUES (
    _idadmin,
    _idpropietario,
    _idcliente,
    _idvehiculo,
    _kilometraje,
    _observaciones,
    _ingresogrua,
    _fechaingreso,
    _fecharecordatorio
  );

  -- Devuelve el nuevo idorden
  SELECT LAST_INSERT_ID() AS idorden;
END$$

-- select * from ordenservicios where idorden = 33;
-- call spInsertDetalleOrdenServicio(33,1,1,200)
-- SP para insertar cada línea de detalle de la orden de servicio
DROP PROCEDURE IF EXISTS spInsertDetalleOrdenServicio;
DELIMITER $$
CREATE PROCEDURE spInsertDetalleOrdenServicio (
  IN _idorden    INT,
  IN _idservicio INT,
  IN _idmecanico        INT,
  IN _precio     DECIMAL(10,2)
)
BEGIN
  INSERT INTO detalleordenservicios (
    idorden,
    idservicio,
    idmecanico,
    precio
  )
  VALUES (
    _idorden,
    _idservicio,
    _idmecanico,
    _precio
  );
END$$

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
    -- propietario: ahora enlazamos correctamente desde clientes
    CASE
      WHEN cli_prop.idpersona IS NOT NULL
        THEN CONCAT(cli_prop_pe.nombres, ' ', cli_prop_pe.apellidos)
      ELSE cli_prop_em.nomcomercial
    END AS propietario,

    -- cliente que hace la orden
    CASE
      WHEN cli_c.idpersona IS NOT NULL
        THEN CONCAT(cli_c_pe.nombres, ' ', cli_c_pe.apellidos)
      ELSE cli_c_em.nomcomercial
    END AS cliente

  FROM ordenservicios o
    JOIN vehiculos v
      ON o.idvehiculo = v.idvehiculo

    -- ← CORRECCIÓN: idpropietario → clientes directamente
    JOIN clientes cli_prop
      ON o.idpropietario = cli_prop.idcliente
    LEFT JOIN personas cli_prop_pe
      ON cli_prop.idpersona = cli_prop_pe.idpersona
    LEFT JOIN empresas cli_prop_em
      ON cli_prop.idempresa = cli_prop_em.idempresa

    -- cliente que hace la orden
    JOIN clientes cli_c
      ON o.idcliente = cli_c.idcliente
    LEFT JOIN personas cli_c_pe
      ON cli_c.idpersona = cli_c_pe.idpersona
    LEFT JOIN empresas cli_c_em
      ON cli_c.idempresa = cli_c_em.idempresa

  WHERE DATE(o.fechaingreso) BETWEEN start_date AND end_date
    AND o.estado = 'A'
  ORDER BY o.fechaingreso;

END$$

DROP PROCEDURE IF EXISTS spInsertFechaSalida;
DELIMITER $$
CREATE PROCEDURE spInsertFechaSalida(
IN _idorden 	INT
)
BEGIN
UPDATE ordenservicios SET
fechasalida = NOW()
WHERE idorden = _idorden;
END $$

DROP PROCEDURE IF EXISTS spGetObservacionByOrden;
DELIMITER $$
CREATE PROCEDURE spGetObservacionByOrden(
  IN _idorden INT
)
BEGIN
  SELECT
    o.idobservacion,
    o.idcomponente,
    co.componente,
    o.idorden,
    o.estado,
    o.foto,
    os.observaciones AS observacion_orden
  FROM observaciones o
  LEFT JOIN componentes co
    ON o.idcomponente = co.idcomponente 
  LEFT JOIN ordenservicios os
    ON o.idorden = os.idorden
  WHERE o.idorden = _idorden;
END $$
DELIMITER ;


-- call spGetObservacionByOrden(39)
-- call spInsertFechaSalida(3)
-- select * from ordenservicios where idorden = 3;
-- select * from ordenservicios;
-- select * from productos;
-- select * from componentes;
-- select * from observaciones;
-- update observaciones set estado = TRUE where idobservacion = 52;
DROP PROCEDURE IF EXISTS spRegisterObservacion;
DELIMITER $$
CREATE PROCEDURE spRegisterObservacion(
IN _idcomponente INT,
IN _idorden 	  INT,
IN _estado 			INT,
IN _foto			VARCHAR(255)
)
BEGIN
INSERT INTO observaciones (idcomponente,idorden,estado,foto) VALUES (_idcomponente,_idorden,_estado,_foto);
END $$


DROP PROCEDURE IF EXISTS spRegisterComponente;
DELIMITER $$
CREATE PROCEDURE spRegisterComponente(
IN _componente VARCHAR(50)
)
BEGIN
INSERT INTO componentes (componente) VALUES (_componente);
END $$
