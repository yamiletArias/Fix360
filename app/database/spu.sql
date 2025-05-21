-- Cambiar delimitador para definir procedimiento
-- 1) Registrar cliente (persona)
DROP PROCEDURE IF EXISTS spRegisterClientePersona;
DELIMITER $$
CREATE PROCEDURE spRegisterClientePersona (
  IN _nombres         VARCHAR(50),
  IN _apellidos       VARCHAR(50),
  IN _tipodoc         VARCHAR(30),
  IN _numdoc          CHAR(20),
  IN _numruc          CHAR(11),
  IN _direccion       VARCHAR(70),
  IN _correo          VARCHAR(100),
  IN _telprincipal    VARCHAR(20),
  IN _telalternativo  VARCHAR(20),
  IN _idcontactabilidad INT
)
BEGIN
  DECLARE _idpersona INT;
  INSERT INTO personas (
    nombres, apellidos, tipodoc, numdoc,
    numruc, direccion, correo,
    telprincipal, telalternativo
  ) VALUES (
    _nombres, _apellidos, _tipodoc, _numdoc,
    NULLIF(_numruc, ''), NULLIF(_direccion, ''), NULLIF(_correo, ''),
    _telprincipal, NULLIF(_telalternativo, '')
  );
  SET _idpersona = LAST_INSERT_ID();
  INSERT INTO clientes (idpersona, idcontactabilidad)
  VALUES (_idpersona, _idcontactabilidad);
END$$

-- 2) Registrar cliente (empresa)
/*
DROP PROCEDURE IF EXISTS spRegisterClienteEmpresa;
DELIMITER $$
CREATE PROCEDURE spRegisterClienteEmpresa (
  IN _ruc            CHAR(11),
  IN _nomcomercial   VARCHAR(80),
  IN _razonsocial    VARCHAR(80),
  IN _telefono       VARCHAR(20),
  IN _correo         VARCHAR(100),
  IN _idcontactabilidad INT
)
BEGIN
  DECLARE _idempresa INT;
  INSERT INTO empresas (
    ruc, nomcomercial, razonsocial,
    telefono, correo
  ) VALUES (
    _ruc, _nomcomercial, _razonsocial,
    NULLIF(_telefono, ''), NULLIF(_correo, '')
  );
  SET _idempresa = LAST_INSERT_ID();
  INSERT INTO clientes (idempresa, idcontactabilidad)
  VALUES (_idempresa, _idcontactabilidad);
END$$
*/

-- 2) PROCEDMIENTO PARA EL REGISTRO REAL DE CLIENTE EMPRESA (PARA QUE SE VEA EN PROVEEDORES AL REGISTRAR)
DROP PROCEDURE IF EXISTS spRegisterClienteEmpresa;
DELIMITER $$
CREATE PROCEDURE spRegisterClienteEmpresa (
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
  INSERT INTO empresas (
    ruc,
    nomcomercial,
    razonsocial,
    telefono,
    correo
  )
  VALUES (
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
  VALUES (_idempresa, _idcontactabilidad);
  -- Insertar en la tabla proveedores solo si no existe
  IF NOT EXISTS (
    SELECT 1 FROM proveedores WHERE idempresa = _idempresa
  ) THEN
    INSERT INTO proveedores (idempresa)
    VALUES (_idempresa);
  END IF;
END $$

-- 3) Registrar vehículo y propietario
DROP PROCEDURE IF EXISTS spRegisterVehiculo;
DELIMITER $$
CREATE PROCEDURE spRegisterVehiculo(
  IN _idmodelo       INT,
  IN _idtcombustible INT,
  IN _placa          CHAR(6),
  IN _anio           CHAR(4),
  IN _numserie       VARCHAR(50),
  IN _color          VARCHAR(20),
  IN _vin            CHAR(17),
  IN _numchasis      CHAR(17),
  IN _idcliente      INT
)
BEGIN
  DECLARE _idvehiculo INT;
  INSERT INTO vehiculos (
    idmodelo, idtcombustible, placa,
    anio, numserie, color,
    vin, numchasis
  ) VALUES (
    _idmodelo, _idtcombustible, _placa,
    NULLIF(_anio, ''), NULLIF(_numserie, ''),
    _color,
    NULLIF(_vin, ''), NULLIF(_numchasis, '')
  );
  SET _idvehiculo = LAST_INSERT_ID();
  INSERT INTO propietarios (idcliente, idvehiculo)
  VALUES (_idcliente, _idvehiculo);
END$$
/*
-- 4) Registrar producto
DROP PROCEDURE IF EXISTS spRegisterProducto;
DELIMITER $$
CREATE PROCEDURE spRegisterProducto(
  IN _idsubcategoria INT,
  IN _idmarca        INT,
  IN _descripcion    VARCHAR(50),
  IN _precio         DECIMAL(7,2),
  IN _presentacion   VARCHAR(40),
  IN _undmedida      VARCHAR(40),
  IN _cantidad       DECIMAL(10,2),
  IN _img            VARCHAR(255)
)
BEGIN
  INSERT INTO productos (
    idsubcategoria, idmarca, descripcion,
    precio, presentacion, undmedida,
    cantidad, img
  ) VALUES (
    _idsubcategoria, _idmarca, _descripcion,
    _precio, _presentacion, _undmedida,
    _cantidad, NULLIF(_img, '')
  );
END$$
*/

DROP PROCEDURE IF EXISTS spRegisterProducto;
DELIMITER $$
CREATE PROCEDURE spRegisterProducto(
  IN  _idsubcategoria INT,
  IN  _idmarca        INT,
  IN  _descripcion    VARCHAR(50),
  IN  _precio         DECIMAL(7,2),
  IN  _presentacion   VARCHAR(40),
  IN  _undmedida      VARCHAR(40),
  IN  _cantidad       DECIMAL(10,2),
  IN  _img            VARCHAR(255),
  IN  _stockmin       INT,
  IN  _stockmax       INT,           -- puede ser NULL si no lo usas
  OUT _idproducto     INT
)
BEGIN
  -- 1) Inserto el producto
  INSERT INTO productos 
    (idsubcategoria, idmarca, descripcion, precio, presentacion, undmedida, cantidad, img)
  VALUES 
    (_idsubcategoria, _idmarca, _descripcion, _precio, _presentacion, _undmedida, _cantidad, NULLIF(_img, ''));

  -- 2) Capturo el id que acabo de generar
  SET _idproducto = LAST_INSERT_ID();

  -- 3) Inserto el kardex asociado
  INSERT INTO kardex
    (idproducto, fecha, stockmin, stockmax)
  VALUES
    (_idproducto,
     CURDATE(),          -- o la fecha que prefieras
     _stockmin,
     NULLIF(_stockmax,'')
    );
END$$

-- select * from kardex
-- select * from productos
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
INSERT INTO tipocombustibles (tcombustible) VALUES(_tcombustible);
 SELECT LAST_INSERT_ID() AS idtcombustible;
END $$

-- Restaurar delimitador por defecto
CALL spGetClienteById(12);
DROP PROCEDURE IF EXISTS spGetClienteById;
DELIMITER $$
CREATE PROCEDURE spGetClienteById(
  IN _idcliente INT
)
BEGIN
  SELECT
    CASE
      WHEN c.idpersona IS NOT NULL
        THEN CONCAT(pe.apellidos, ' ', pe.nombres)
      ELSE em.nomcomercial
    END AS propietario
  FROM clientes AS c
  LEFT JOIN personas AS pe
    ON c.idpersona = pe.idpersona
  LEFT JOIN empresas AS em
    ON c.idempresa = em.idempresa
  WHERE c.idcliente = _idcliente
  LIMIT 1;
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
  IN _fechaingreso      DATETIME
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
    fechaingreso
  )
  VALUES (
    _idadmin,
    _idpropietario,
    _idcliente,
    _idvehiculo,
    _kilometraje,
    NULLIF(_observaciones, ''),
    _ingresogrua,
    _fechaingreso
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
  IN _modo    ENUM('semana','mes','dia'),
  IN _fecha   DATE,
  IN _estado  CHAR(1)          -- 'A' para activas, 'D' para desactivadas
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
    -- propietario
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
    JOIN clientes cli_prop
      ON o.idpropietario = cli_prop.idcliente
    LEFT JOIN personas cli_prop_pe
      ON cli_prop.idpersona = cli_prop_pe.idpersona
    LEFT JOIN empresas cli_prop_em
      ON cli_prop.idempresa = cli_prop_em.idempresa
    JOIN clientes cli_c
      ON o.idcliente = cli_c.idcliente
    LEFT JOIN personas cli_c_pe
      ON cli_c.idpersona = cli_c_pe.idpersona
    LEFT JOIN empresas cli_c_em
      ON cli_c.idempresa = cli_c_em.idempresa
  WHERE DATE(o.fechaingreso) BETWEEN start_date AND end_date
    AND o.estado = _estado           -- filtro por estado
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
IN _estado 			BOOLEAN,
IN _foto			VARCHAR(255)
)
BEGIN
INSERT INTO observaciones (idcomponente,idorden,estado,foto) VALUES (_idcomponente,_idorden,_estado,NULLIF(_foto, ''));
END $$

DROP PROCEDURE IF EXISTS spRegisterComponente;
DELIMITER $$
CREATE PROCEDURE spRegisterComponente(
IN _componente VARCHAR(50)
)
BEGIN
INSERT INTO componentes (componente) VALUES (_componente);
END $$

DROP PROCEDURE IF EXISTS spRegisterRecordatorio;
DELIMITER $$
CREATE PROCEDURE spRegisterRecordatorio(
IN _idpropietario INT,
IN _fchproxvisita DATE,
IN _comentario 	  VARCHAR(255)
)
BEGIN
INSERT INTO agendas (idpropietario,fchproxvisita,comentario) VALUES 
(_idpropietario,_fchproxvisita,_comentario);
END $$

-- call spListAgendasPorPeriodo('dia','2025-05-10','A')

DROP PROCEDURE IF EXISTS spListAgendasPorPeriodo;
DELIMITER $$
CREATE PROCEDURE spListAgendasPorPeriodo(
  IN _modo    ENUM('semana','mes','dia'),
  IN _fecha   DATE,
  IN _estado  ENUM('P','R','C','H','A')  -- 'A' = Activos (P o R)
)
BEGIN
  DECLARE start_date DATE;
  DECLARE end_date   DATE;

  -- 1. Calcular el rango según modo
  IF _modo = 'semana' THEN
    SET start_date = DATE_SUB(_fecha, INTERVAL WEEKDAY(_fecha) DAY);
    SET end_date   = DATE_ADD(start_date, INTERVAL 6 DAY);
  ELSEIF _modo = 'mes' THEN
    SET start_date = DATE_FORMAT(_fecha, '%Y-%m-01');
    SET end_date   = LAST_DAY(_fecha);
  ELSE  -- 'dia'
    SET start_date = _fecha;
    SET end_date   = _fecha;
  END IF;

  -- 2. Selección con lógica de “Activos”
  SELECT
    a.idagenda,
    a.idpropietario,
    a.fchproxvisita,
    a.comentario,
    a.estado,
    CONCAT(p.nombres, ' ', p.apellidos) AS nomcliente,
    p.telprincipal,
    p.telalternativo,
    p.correo
  FROM agendas AS a
    JOIN clientes AS c
      ON a.idpropietario = c.idcliente
    JOIN personas AS p
      ON c.idpersona = p.idpersona
  WHERE
    a.fchproxvisita BETWEEN start_date AND end_date
    AND (
      -- Si piden “Activos” (A), mostrar P y R
      (_estado = 'A' AND a.estado IN ('P','R'))
      -- Sino, filtrar por el estado exacto
      OR (_estado <> 'A' AND a.estado = _estado)
    )
  ORDER BY a.fchproxvisita;
END$$

DROP PROCEDURE IF EXISTS spUpdateEstado;
DELIMITER $$
CREATE PROCEDURE spUpdateEstado(
IN _idagenda INT,
IN _estado ENUM('P','R','C','H')
)
BEGIN 
UPDATE agendas SET
estado = _estado
WHERE idagenda = _idagenda;
END $$

DROP PROCEDURE IF EXISTS spReprogramarRecordatorio;
DELIMITER $$
CREATE PROCEDURE spReprogramarRecordatorio(
  IN _idagenda        INT,
  IN _nueva_fecha     DATE
)
BEGIN
  UPDATE agendas
  SET
    fchproxvisita = _nueva_fecha,
    estado        = 'R'
  WHERE
    idagenda = _idagenda;
END$$

-- call spReprogramarRecordatorio(5,'2025-05-10')
-- select * from vwRecordatoriosHoy
-- select * from agendas;

-- call spGetVentasByVehiculo(1)

DROP PROCEDURE IF EXISTS spGetVentasByVehiculo;
DELIMITER $$
CREATE PROCEDURE spGetVentasByVehiculo(
  IN _idvehiculo INT
)
BEGIN
  SELECT
    v.idventa,
    
    -- Quién registró la venta
    v.idcolaborador                               AS idregistrador,
    CONCAT(pe_reg.nombres, ' ', pe_reg.apellidos) AS registrador,
    
    -- Propietario en ese momento
    prop.idcliente                                 AS idpropietario,
    CASE
      WHEN cli_prop.idpersona IS NOT NULL
        THEN CONCAT(pe_prop.nombres, ' ', pe_prop.apellidos)
      ELSE COALESCE(em_prop.nomcomercial, em_prop.razonsocial)
    END                                             AS propietario,
    
    -- Cliente de la venta
    v.idcliente                                    AS idcliente,
    CASE
      WHEN cli_c.idpersona IS NOT NULL
        THEN CONCAT(pe_c.nombres, ' ', pe_c.apellidos)
      ELSE COALESCE(em_c.nomcomercial, em_c.razonsocial)
    END                                             AS cliente,
    
    -- Detalles de la venta
    v.tipocom                                 AS tipo_comprobante,
    v.fechahora,
    CONCAT(v.numserie, '-', v.numcom)         AS comprobante,
    v.kilometraje,
    v.estado
  FROM ventas v
    -- Datos de quien registra
    JOIN colaboradores col_reg ON v.idcolaborador = col_reg.idcolaborador
    JOIN contratos     ctr_reg ON col_reg.idcontrato   = ctr_reg.idcontrato
    JOIN personas      pe_reg  ON ctr_reg.idpersona    = pe_reg.idpersona

    -- Buscamos el propietario vigente usando fecha de venta
    LEFT JOIN propietarios prop 
      ON prop.idvehiculo = v.idvehiculo
     AND prop.fechainicio <= v.fechahora
     AND (prop.fechafinal   IS NULL OR prop.fechafinal   >= v.fechahora)
    LEFT JOIN clientes    cli_prop ON prop.idcliente = cli_prop.idcliente
    LEFT JOIN personas    pe_prop  ON cli_prop.idpersona = pe_prop.idpersona
    LEFT JOIN empresas    em_prop  ON cli_prop.idempresa = em_prop.idempresa

    -- Cliente que hace la compra
    LEFT JOIN clientes    cli_c ON v.idcliente = cli_c.idcliente
    LEFT JOIN personas    pe_c  ON cli_c.idpersona = pe_c.idpersona
    LEFT JOIN empresas    em_c  ON cli_c.idempresa = em_c.idempresa

  WHERE v.idvehiculo = _idvehiculo
  ORDER BY v.fechahora;
END$$


-- call spGetOrdenesByVehiculo(1)

DROP PROCEDURE IF EXISTS spGetOrdenesByVehiculo;
DELIMITER $$
CREATE PROCEDURE spGetOrdenesByVehiculo(
  IN _idvehiculo INT
)
BEGIN
  SELECT
    o.idorden,

    -- Quién registró la orden
    o.idadmin                                     AS idadmin,
    CONCAT(pe_reg.nombres, ' ', pe_reg.apellidos) AS Administrador,

    -- Propietario en ese momento
    o.idpropietario                               AS idpropietario,
    CASE
      WHEN cli_prop.idpersona IS NOT NULL
        THEN CONCAT(pe_prop.nombres, ' ', pe_prop.apellidos)
      ELSE COALESCE(em_prop.nomcomercial, em_prop.razonsocial)
    END                                           AS propietario,

    -- Cliente que encargó la orden
    o.idcliente                                   AS idcliente,
    CASE
      WHEN cli_c.idpersona IS NOT NULL
        THEN CONCAT(pe_c.nombres, ' ', pe_c.apellidos)
      ELSE COALESCE(em_c.nomcomercial, em_c.razonsocial)
    END                                           AS cliente,

    -- Kilometraje y estado
    o.kilometraje                                 AS kilometraje,
    o.estado                                      AS estado,

    o.fechaingreso,
    o.fechasalida
  FROM ordenservicios o

    -- Datos del registrador (colaborador → contrato → persona)
    JOIN colaboradores col    ON o.idadmin    = col.idcolaborador
    JOIN contratos    ctr     ON col.idcontrato = ctr.idcontrato
    JOIN personas     pe_reg  ON ctr.idpersona = pe_reg.idpersona

    -- Datos del propietario al momento
    LEFT JOIN clientes  cli_prop ON o.idpropietario = cli_prop.idcliente
    LEFT JOIN personas  pe_prop  ON cli_prop.idpersona   = pe_prop.idpersona
    LEFT JOIN empresas  em_prop  ON cli_prop.idempresa   = em_prop.idempresa

    -- Datos del cliente que generó la orden
    LEFT JOIN clientes  cli_c    ON o.idcliente    = cli_c.idcliente
    LEFT JOIN personas  pe_c     ON cli_c.idpersona = pe_c.idpersona
    LEFT JOIN empresas  em_c     ON cli_c.idempresa = em_c.idempresa

  WHERE o.idvehiculo = _idvehiculo
  ORDER BY o.fechaingreso;
END$$

-- call spGetDetalleOrdenServicio(2)
DROP PROCEDURE IF EXISTS spGetDetalleOrdenServicio;
DELIMITER $$
CREATE PROCEDURE spGetDetalleOrdenServicio(
  IN _idorden INT
)
BEGIN
  SELECT
    dos.iddetorden                           AS iddetorden,
    dos.idservicio,
    s.servicio,
    
    dos.idmecanico,
    CONCAT(pe_me.nombres, ' ', pe_me.apellidos) AS mecanico,
    
    dos.precio
  FROM detalleordenservicios dos
    JOIN servicios     s     ON dos.idservicio = s.idservicio
    JOIN colaboradores col   ON dos.idmecanico = col.idcolaborador
    JOIN contratos     ctr   ON col.idcontrato = ctr.idcontrato
    JOIN personas      pe_me ON ctr.idpersona = pe_me.idpersona
  WHERE dos.idorden = _idorden
  ORDER BY dos.iddetorden;
END$$

DROP PROCEDURE IF EXISTS spGetJustificacionByOrden;
DELIMITER $$
CREATE PROCEDURE spGetJustificacionByOrden(
  IN _idorden INT
)
BEGIN
  SELECT justificacion
    FROM ordenservicios
   WHERE idorden = _idorden;
END$$

DROP PROCEDURE IF EXISTS spUpdateVehiculoConHistorico;
DELIMITER $$
CREATE PROCEDURE spUpdateVehiculoConHistorico(
  IN p_idvehiculo       INT,
  IN p_idmodelo         INT,
  IN p_idtcombustible   INT,
  IN p_placa            CHAR(6),
  IN p_anio             CHAR(4),
  IN p_numserie         VARCHAR(50),
  IN p_color            VARCHAR(20),
  IN p_vin              CHAR(17),
  IN p_numchasis        CHAR(17),
  IN p_idcliente_nuevo  INT
)
BEGIN
  DECLARE v_idcliente_actual INT;

  START TRANSACTION;

    -- 1) Actualizamos el vehículo
    UPDATE vehiculos
    SET
      idmodelo       = p_idmodelo,
      idtcombustible = p_idtcombustible,
      placa          = p_placa,
      anio           = NULLIF(p_idanio,   ''),    -- Si p_anio = '', se pone NULL
      numserie       = NULLIF(p_numserie, ''),    -- Si p_numserie = '', se pone NULL
      color          = p_color,
      vin            = NULLIF(p_vin,    ''),      -- Si p_vin = '', se pone NULL
      numchasis      = NULLIF(p_numchasis, ''),
      modificado     = NOW()
    WHERE idvehiculo = p_idvehiculo;

    -- 2) Obtenemos el propietario actual (sin fechafinal)
    SELECT idcliente
      INTO v_idcliente_actual
    FROM propietarios
    WHERE idvehiculo  = p_idvehiculo
      AND fechafinal IS NULL
    LIMIT 1;

    -- 3) Si cambió de propietario, cerramos el anterior e insertamos el nuevo
    IF v_idcliente_actual IS NULL
       OR v_idcliente_actual <> p_idcliente_nuevo
    THEN
      -- 3a) Cerramos la relación anterior (si existía)
      UPDATE propietarios
      SET fechafinal = CURDATE()
      WHERE idvehiculo  = p_idvehiculo
        AND fechafinal IS NULL;

      -- 3b) Insertamos la nueva relación
      INSERT INTO propietarios (
        idcliente, idvehiculo, fechainicio, fechafinal
      ) VALUES (
        p_idcliente_nuevo,
        p_idvehiculo,
        CURDATE(),
        NULL
      );
    END IF;

  COMMIT;

  -- 4) Al final devolvemos el idcliente que ahora es propietario activo
  SELECT
    p.idcliente AS idcliente_propietario_nuevo
  FROM propietarios AS p
  WHERE p.idvehiculo = p_idvehiculo
    AND p.fechafinal IS NULL
  LIMIT 1;
END $$


DROP PROCEDURE IF EXISTS spDeleteObservacion;
DELIMITER $$
CREATE PROCEDURE spDeleteObservacion(
IN _idobservacion INT
)
BEGIN
DELETE FROM observaciones WHERE idobservacion = _idobservacion;
END $$

DROP PROCEDURE IF EXISTS spUpdateObservacion;
DELIMITER $$
CREATE PROCEDURE spUpdateObservacion(
IN _idobservacion INT,
IN _idcomponente INT,
IN _estado BOOLEAN,
IN _foto VARCHAR(255)

)
BEGIN
UPDATE observaciones SET
idcomponente = _idcomponente,
estado = _estado,
foto = _foto
WHERE idobservacion = _idobservacion;
END $$


DROP PROCEDURE spDeleteOrdenServicio;
DELIMITER $$
CREATE PROCEDURE spDeleteOrdenServicio(
IN _idorden INT,
IN _justificacion VARCHAR(255)
)
BEGIN
UPDATE ordenservicios SET 
estado = 'D',
justificacion = _justificacion
WHERE idorden = _idorden;
END $$
-- select * from detalleordenservicios where idorden = 1;
-- call spGetDetalleOrdenServicio(1)
DROP PROCEDURE IF EXISTS spGetDetalleOrdenServicio;
DELIMITER $$
CREATE PROCEDURE spGetDetalleOrdenServicio(
  IN _idorden INT
)
BEGIN

  -- 1) Cabecera de la orden
  SELECT
    o.idorden,
    DATE_FORMAT(o.fechaingreso, '%d/%m/%Y %H:%i') AS fecha_ingreso,
    CASE WHEN o.fechasalida IS NULL 
         THEN NULL 
         ELSE DATE_FORMAT(o.fechasalida, '%d/%m/%Y %H:%i') 
    END AS fecha_salida,
    o.ingresogrua,
    o.kilometraje,
    o.observaciones,

    -- Propietario
    COALESCE(
      CONCAT(pp.apellidos, ' ', pp.nombres),
      pe.nomcomercial
    ) AS propietario,

    -- Cliente
    COALESCE(
      CONCAT(cp.apellidos, ' ', cp.nombres),
      ce.nomcomercial
    ) AS cliente,

    -- Vehículo
    CONCAT(tv.tipov, ' ', ma.nombre, ' ', vh.color, ' (', vh.placa, ')') AS vehiculo

  FROM ordenservicios o
    JOIN clientes cprop ON o.idpropietario = cprop.idcliente
    LEFT JOIN personas pp    ON cprop.idpersona = pp.idpersona
    LEFT JOIN empresas pe    ON cprop.idempresa = pe.idempresa

    JOIN clientes ccli  ON o.idcliente = ccli.idcliente
    LEFT JOIN personas cp    ON ccli.idpersona = cp.idpersona
    LEFT JOIN empresas ce    ON ccli.idempresa = ce.idempresa

    JOIN vehiculos vh   ON o.idvehiculo = vh.idvehiculo
    JOIN modelos m      ON vh.idmodelo   = m.idmodelo
    JOIN tipovehiculos tv ON m.idtipov   = tv.idtipov
    JOIN marcas ma      ON m.idmarca     = ma.idmarca

  WHERE o.idorden = _idorden
    AND o.estado = 'A';

  -- 2) Detalle de servicios y mecánicos
  SELECT
    dos.iddetorden,
    -- Nombre del mecánico (persona o username si no hay persona)
    COALESCE(
      CONCAT(mp.apellidos, ' ', mp.nombres),
      col.namuser
    ) AS mecanico,
    s.servicio,
    dos.precio

  FROM detalleordenservicios dos
    JOIN ordenservicios o ON dos.idorden = o.idorden
    JOIN colaboradores col ON dos.idmecanico = col.idcolaborador
    LEFT JOIN contratos ct ON col.idcontrato = ct.idcontrato
    LEFT JOIN personas mp  ON ct.idpersona = mp.idpersona

    JOIN servicios s      ON dos.idservicio = s.idservicio

  WHERE dos.idorden = _idorden
    AND dos.estado = 'A'
  ORDER BY dos.iddetorden;

  -- 3) Total de la orden
  SELECT
    ROUND(SUM(dos.precio),2) AS total_orden
  FROM detalleordenservicios dos
  WHERE dos.idorden = _idorden
    AND dos.estado = 'A';

END$$

-- select * from vehiculos
-- call spGetUltimoKilometraje(9)
-- explicacion: junta tanto ordenes de servicio como ventas, se lista de manera descendente, para traer el mas reciente, le ponemos limit 1 para solo recibir el 1ero 
 
-- call spListEgresosPorPeriodo('dia','2025-05-01')
DROP PROCEDURE IF EXISTS spListEgresosPorPeriodo;
DELIMITER $$
CREATE PROCEDURE spListEgresosPorPeriodo(
  IN _modo   ENUM('semana','mes','dia'),
  IN _fecha  DATE,
  IN _estado ENUM('A','D')
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
  e.idegreso,
  DATE_FORMAT(e.fecharegistro, '%d/%m/%Y') AS fecha,
  TIME(e.fecharegistro) AS hora,
  adm.namuser    AS registrador,
  col.namuser    AS receptor,
  e.concepto,
  e.monto,
  e.numcomprobante,
  e.justificacion
FROM egresos e
    JOIN colaboradores adm ON e.idadmin       = adm.idcolaborador
    JOIN colaboradores col ON e.idcolaborador = col.idcolaborador
  WHERE DATE(e.fecharegistro) BETWEEN start_date AND end_date
    AND e.estado = _estado
  ORDER BY e.fecharegistro;
END$$

-- 3) SP: registrar un nuevo egreso
DROP PROCEDURE IF EXISTS spRegisterEgreso;
DELIMITER $$
CREATE PROCEDURE spRegisterEgreso(
  IN _idadmin        INT,
  IN _idcolaborador  INT,
  IN _idformapago    INT,
  IN _concepto       VARCHAR(100),
  IN _monto          DECIMAL(10,2),
  IN _numcomprobante VARCHAR(20)
)
BEGIN
  INSERT INTO egresos (
    idadmin,
    idcolaborador,
    idformapago,
    concepto,
    monto,
    numcomprobante
  ) VALUES (
    _idadmin,
    _idcolaborador,
    _idformapago,
    _concepto,
    _monto,
    NULLIF(_numcomprobante, '')
  );
  SELECT LAST_INSERT_ID() AS idegreso;
END$$



-- 4) SP: \"eliminar\" un egreso (marcar estado = 'D')
DROP PROCEDURE IF EXISTS spDeleteEgreso;
DELIMITER $$
CREATE PROCEDURE spDeleteEgreso(
  IN _idegreso       INT,
  IN _justificacion  VARCHAR(255)
)
BEGIN
  UPDATE egresos
     SET estado       = 'D',
         justificacion = _justificacion
   WHERE idegreso     = _idegreso;
END$$

-- select * from egresos

-- call spDeleteOrdenServicio(1)
-- CALL spGetUltimoKilometraje(8);
-- select * from ordenservicios;

DROP PROCEDURE IF EXISTS spListMovimientosPorProductoPorPeriodo;
DELIMITER $$
CREATE PROCEDURE spListMovimientosPorProductoPorPeriodo(
    IN in_idproducto INT,
    IN in_modo       VARCHAR(10),
    IN in_fecha      DATE
)
BEGIN
    DECLARE v_inicio DATE;
    DECLARE v_fin    DATE;

    -- Determina el rango según el modo
    IF in_modo = 'dia' THEN
        SET v_inicio = in_fecha;
        SET v_fin    = in_fecha;
    ELSEIF in_modo = 'semana' THEN
        SET v_inicio = DATE_SUB(in_fecha, INTERVAL WEEKDAY(in_fecha) DAY);
        SET v_fin    = DATE_ADD(v_inicio, INTERVAL 6 DAY);
    ELSEIF in_modo = 'mes' THEN
        SET v_inicio = DATE_SUB(in_fecha, INTERVAL DAY(in_fecha)-1 DAY);
        SET v_fin    = LAST_DAY(in_fecha);
    ELSE
        SET v_inicio = in_fecha;
        SET v_fin    = in_fecha;
    END IF;

    -- Selección de movimientos
    SELECT
        m.fecha,
        tm.flujo,
        tm.tipomov       AS tipo_movimiento,
        m.cantidad,
        m.saldorestante  AS saldo_restante
    FROM movimientos AS m
    INNER JOIN kardex           AS k  ON k.idkardex = m.idkardex
    INNER JOIN tipomovimientos  AS tm ON tm.idtipomov  = m.idtipomov
    WHERE k.idproducto = in_idproducto
      AND m.fecha BETWEEN v_inicio AND v_fin
    ORDER BY m.fecha
    ;
END $$

-- 1) Actualizar Persona
DROP PROCEDURE IF EXISTS spUpdatePersona;
DELIMITER $$
CREATE PROCEDURE spUpdatePersona(
  IN _idpersona       INT,
  IN _nombres         VARCHAR(50),
  IN _apellidos       VARCHAR(50),
  IN _tipodoc         VARCHAR(30),
  IN _numdoc          CHAR(20),
  IN _numruc          CHAR(11),
  IN _direccion       VARCHAR(70),
  IN _correo          VARCHAR(100),
  IN _telprincipal    VARCHAR(20),
  IN _telalternativo  VARCHAR(20)
)
BEGIN
  UPDATE personas
     SET nombres        = _nombres,
         apellidos      = _apellidos,
         tipodoc        = _tipodoc,
         numdoc         = _numdoc,
         numruc         = NULLIF(_numruc, ''),
         direccion      = NULLIF(_direccion, ''),
         correo         = NULLIF(_correo, ''),
         telprincipal   = _telprincipal,
         telalternativo = NULLIF(_telalternativo, ''),
         modificado = NOW()
   WHERE idpersona      = _idpersona;
END$$

-- 2) Actualizar Empresa
DROP PROCEDURE IF EXISTS spUpdateEmpresa;
DELIMITER $$
CREATE PROCEDURE spUpdateEmpresa(
  IN _idempresa     INT,
  IN _nomcomercial  VARCHAR(80),
  IN _razonsocial   VARCHAR(80),
  IN _telefono      VARCHAR(20),
  IN _correo        VARCHAR(100)
)
BEGIN
  UPDATE empresas
     SET nomcomercial = _nomcomercial,
         razonsocial  = _razonsocial,
         telefono     = NULLIF(_telefono, ''),
         correo       = NULLIF(_correo, ''),
         modificado = NOW()
   WHERE idempresa    = _idempresa;
END$$

DROP PROCEDURE IF EXISTS spGetVehiculoConPropietario;
DELIMITER $$
CREATE PROCEDURE spGetVehiculoConPropietario(
  IN _idvehiculo INT
)
BEGIN
  SELECT
    v.idvehiculo,
    v.idmodelo,
    m.idmarca,
    m.idtipov,
    v.idtcombustible,

    -- los nombres legibles
    tv.tipov                       AS tipo_vehiculo,
    ma.nombre                      AS marca,
    m.modelo                       AS modelo,
    tc.tcombustible                AS combustible,

    -- el resto de campos
    v.placa,
    v.color,
    v.anio,
    v.numserie,
    v.vin,
    v.numchasis,

    p.idcliente                    AS idcliente_propietario,
    CASE
      WHEN c.idpersona IS NOT NULL
        THEN CONCAT(pe.apellidos, ' ', pe.nombres)
      ELSE em.nomcomercial
    END                             AS propietario

  FROM vehiculos AS v
    JOIN modelos AS m      ON v.idmodelo       = m.idmodelo
    JOIN tipovehiculos AS tv ON m.idtipov      = tv.idtipov
    JOIN marcas AS ma      ON m.idmarca        = ma.idmarca
    JOIN tipocombustibles AS tc ON v.idtcombustible = tc.idtcombustible

    LEFT JOIN propietarios AS p
      ON v.idvehiculo     = p.idvehiculo
     AND p.fechafinal IS NULL
    LEFT JOIN clientes AS c
      ON p.idcliente      = c.idcliente
    LEFT JOIN personas AS pe
      ON c.idpersona      = pe.idpersona
    LEFT JOIN empresas AS em
      ON c.idempresa      = em.idempresa

  WHERE v.idvehiculo = _idvehiculo
  LIMIT 1;
END $$
DELIMITER ;

-- CALL spGetVehiculoConPropietario(5);

DROP PROCEDURE IF EXISTS spRegisterMarcaVehiculo;
DELIMITER $$
CREATE PROCEDURE spRegisterMarcaVehiculo(
IN _nombre VARCHAR(50)
)
BEGIN
INSERT INTO marcas (nombre,tipo) VALUES (_nombre,'vehiculo');
 SELECT LAST_INSERT_ID() AS idmarca;
END $$

DROP PROCEDURE IF EXISTS spRegisterModelo;
DELIMITER $$
CREATE PROCEDURE spRegisterModelo(
IN _idtipov INT,
IN _idmarca INT,
IN _modelo VARCHAR(100)
)
BEGIN
INSERT INTO modelos (idtipov,idmarca,modelo) VALUES (_idtipov,_idmarca,_modelo);
SELECT LAST_INSERT_ID() AS idmodelo;
END $$

DROP PROCEDURE IF EXISTS spRegisterMarcaProducto;
DELIMITER $$
CREATE PROCEDURE spRegisterMarcaProducto(
IN _nombre VARCHAR(50)
)
BEGIN
INSERT INTO marcas (nombre, tipo) VALUES (_nombre, 'producto');
SELECT LAST_INSERT_ID() AS idmarca;
END $$

DROP PROCEDURE IF EXISTS spRegisterCategoria;
DELIMITER $$
CREATE PROCEDURE spRegisterCategoria(
IN _categoria VARCHAR(50)
)
BEGIN 
INSERT INTO categorias (categoria) VALUES (_categoria);
SELECT LAST_INSERT_ID() AS idcategoria;
END $$

DROP PROCEDURE IF EXISTS spRegisterSubcategoria;
DELIMITER $$
CREATE PROCEDURE spRegisterSubcategoria(
IN _idcategoria INT,
IN _subcategoria VARCHAR(50)
)
BEGIN 
INSERT INTO subcategorias (idcategoria, subcategoria) VALUES (_idcategoria, _subcategoria);
SELECT LAST_INSERT_ID() AS idsubcategoria;
END $$


