-- Cambiar delimitador para definir procedimiento
-- 1) Registrar cliente (persona)
USE dbfix360;
DELIMITER $$
DROP PROCEDURE IF EXISTS spRegisterClientePersona $$
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
END $$

DROP PROCEDURE IF EXISTS spRegisterClienteEmpresa $$
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
DROP PROCEDURE IF EXISTS spRegisterVehiculo $$
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

DROP PROCEDURE IF EXISTS spRegisterProducto $$
CREATE PROCEDURE spRegisterProducto(
  IN  _idsubcategoria INT,
  IN  _idmarca        INT,
  IN  _descripcion    VARCHAR(50),
  IN  _precio         DECIMAL(7,2),
  IN  _presentacion   VARCHAR(40),
  IN  _undmedida      VARCHAR(40),
  IN  _cantidad       DECIMAL(10,2),  -- sólo para presentacion
  IN  _img            VARCHAR(255),
  IN  _codigobarra    VARCHAR(255),
  IN  _stockInicial   INT,            -- NUEVO: stock real inicial
  IN  _stockmin       INT,
  IN  _stockmax       INT,            -- puede ser NULL
  OUT _idproducto     INT
)
BEGIN
  DECLARE _idkardex   INT;
  DECLARE _idtipomov  INT;

  -- 1) Inserto el producto (cantidad = presentacion)
  INSERT INTO productos 
    (idsubcategoria, idmarca, descripcion, precio, presentacion, undmedida, cantidad, img,codigobarra)
  VALUES 
    (_idsubcategoria,
     _idmarca,
     _descripcion,
     _precio,
     _presentacion,
     _undmedida,
     _cantidad,
     NULLIF(_img, ''),
     NULLIF(_codigobarra,'')
    );

  SET _idproducto = LAST_INSERT_ID();

  -- 2) Creo el kardex con los umbrales
  INSERT INTO kardex
    (idproducto, fecha, stockmin, stockmax)
  VALUES
    (_idproducto,
     CURDATE(),
     _stockmin,
     NULLIF(_stockmax,'')
    );
  SET _idkardex = LAST_INSERT_ID();

  -- 3) Obtengo un tipo de movimiento de ENTRADA
  SELECT idtipomov
    INTO _idtipomov
  FROM tipomovimientos
  WHERE flujo = 'entrada' AND tipomov = 'stock inicial'
  ORDER BY idtipomov
  LIMIT 1;

  -- 4) Registro el movimiento inicial con el stock real
  INSERT INTO movimientos
    (idkardex, idtipomov, fecha, cantidad, preciounit,saldorestante)
  VALUES
    (_idkardex,
     _idtipomov,
     CURDATE(),
     _stockInicial,
     _precio,
     _stockInicial
    );

END$$

DROP PROCEDURE IF EXISTS spRegisterServicio $$
CREATE PROCEDURE spRegisterServicio(
  IN _idsubcategoria INT,
  IN _servicio VARCHAR(255)
)
BEGIN
  INSERT INTO servicios (idsubcategoria, servicio)
  VALUES (_idsubcategoria, _servicio);
  SELECT LAST_INSERT_ID() AS idservicio;
END$$

-- 6) Obtener todas las contactabilidades
DROP PROCEDURE IF EXISTS spGetAllContactabilidad $$
CREATE PROCEDURE spGetAllContactabilidad()
BEGIN
  SELECT * FROM contactabilidad
  ORDER BY contactabilidad ASC;
END$$

-- 7) Obtener todas las categorías
DROP PROCEDURE IF EXISTS spGetAllCategoria $$
CREATE PROCEDURE spGetAllCategoria()
BEGIN
  SELECT * FROM categorias;
END$$

-- 8) Obtener todas las marcas de producto
DROP PROCEDURE IF EXISTS spGetAllMarcaProducto $$
CREATE PROCEDURE spGetAllMarcaProducto()
BEGIN
  SELECT * FROM marcas
  WHERE tipo != 'vehiculo';
END$$
-- cliente 11
-- select * from ordenservicios;
-- select * from clientes;
-- 9) Obtener todas las marcas de vehículo
DROP PROCEDURE IF EXISTS spGetAllMarcaVehiculo $$
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
DROP PROCEDURE IF EXISTS spGetAllTipoVehiculo $$
CREATE PROCEDURE spGetAllTipoVehiculo()
BEGIN
  SELECT idtipov, tipov
  FROM tipovehiculos
  ORDER BY tipov ASC;
END$$

-- 11) Obtener subcategorías por categoría
DROP PROCEDURE IF EXISTS spGetSubcategoriaByCategoria $$
CREATE PROCEDURE spGetSubcategoriaByCategoria(
  IN _idcategoria INT
)
BEGIN
  SELECT s.idsubcategoria, s.subcategoria
  FROM subcategorias s
  WHERE s.idcategoria = _idcategoria;
END$$

-- 12) Obtener modelos por tipo y marca
DROP PROCEDURE IF EXISTS spGetModelosByTipoMarca $$
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
DROP PROCEDURE IF EXISTS spGetServicioBySubcategoria $$
CREATE PROCEDURE spGetServicioBySubcategoria(
  IN _idsubcategoria INT
)
BEGIN
  SELECT s.idservicio, s.idsubcategoria, s.servicio
  FROM servicios s
  WHERE s.idsubcategoria = _idsubcategoria;
END$$

-- 14) Obtener persona por ID
DROP PROCEDURE IF EXISTS spGetPersonaById $$
CREATE PROCEDURE spGetPersonaById(
  IN _idpersona INT
)
BEGIN
  SELECT
    p.*,
    c.idcontactabilidad
  FROM personas AS p
  INNER JOIN clientes AS c
    ON p.idpersona = c.idpersona
  WHERE p.idpersona = _idpersona;
END $$


-- 15) Obtener empresa por ID
DROP PROCEDURE IF EXISTS spGetEmpresaById $$
CREATE PROCEDURE spGetEmpresaById(
  IN _idempresa INT
)
BEGIN
  SELECT
    e.*,
    c.idcontactabilidad
  FROM empresas AS e
  INNER JOIN clientes AS c
    ON e.idempresa = c.idempresa
  WHERE e.idempresa = _idempresa;
END $$


-- 16) Buscar persona (por DNI o NOMBRE)
DROP PROCEDURE IF EXISTS spBuscarPersona $$
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
DROP PROCEDURE IF EXISTS spBuscarEmpresa $$
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
DROP PROCEDURE IF EXISTS spGetVehiculoByCliente $$
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

DROP PROCEDURE IF EXISTS spRegisterTcombustible $$
CREATE PROCEDURE spRegisterTcombustible(
IN _tcombustible VARCHAR(50)
)
BEGIN
INSERT INTO tipocombustibles (tcombustible) VALUES(_tcombustible);
 SELECT LAST_INSERT_ID() AS idtcombustible;
END $$

-- Restaurar delimitador por defecto
-- CALL spGetClienteById(12);
DROP PROCEDURE IF EXISTS spGetClienteById $$
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

DROP PROCEDURE IF EXISTS spRegisterOrdenServicio $$
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
DROP PROCEDURE IF EXISTS spInsertDetalleOrdenServicio $$
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

DROP PROCEDURE IF EXISTS spListOrdenesPorPeriodo $$
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


DROP PROCEDURE IF EXISTS spInsertFechaSalida $$
CREATE PROCEDURE spInsertFechaSalida(
IN _idorden 	INT
)
BEGIN
UPDATE ordenservicios SET
fechasalida = NOW()
WHERE idorden = _idorden;
END $$

DROP PROCEDURE IF EXISTS spGetObservacionByOrden $$
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

DROP PROCEDURE IF EXISTS spRegisterObservacion $$
CREATE PROCEDURE spRegisterObservacion(
IN _idcomponente INT,
IN _idorden 	  INT,
IN _estado 			BOOLEAN,
IN _foto			VARCHAR(255)
)
BEGIN
INSERT INTO observaciones (idcomponente,idorden,estado,foto) VALUES (_idcomponente,_idorden,_estado,NULLIF(_foto, ''));
END $$

DROP PROCEDURE IF EXISTS spRegisterComponente $$
CREATE PROCEDURE spRegisterComponente(
IN _componente VARCHAR(50)
)
BEGIN
INSERT INTO componentes (componente) VALUES (_componente);
END $$

DROP PROCEDURE IF EXISTS spRegisterRecordatorio $$
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

DROP PROCEDURE IF EXISTS spListAgendasPorPeriodo $$
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

DROP PROCEDURE IF EXISTS spUpdateEstado $$
CREATE PROCEDURE spUpdateEstado(
IN _idagenda INT,
IN _estado ENUM('P','R','C','H')
)
BEGIN 
UPDATE agendas SET
estado = _estado
WHERE idagenda = _idagenda;
END $$

DROP PROCEDURE IF EXISTS spReprogramarRecordatorio $$
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

DROP PROCEDURE IF EXISTS spGetVentasByVehiculo $$	
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

DROP PROCEDURE IF EXISTS spGetOrdenesByVehiculo $$
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
DROP PROCEDURE IF EXISTS spGetDetalleOrdenServicio $$
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

DROP PROCEDURE IF EXISTS spGetJustificacionByOrden $$
CREATE PROCEDURE spGetJustificacionByOrden(
  IN _idorden INT
)
BEGIN
  SELECT justificacion
    FROM ordenservicios
   WHERE idorden = _idorden;
END$$

DROP PROCEDURE IF EXISTS spUpdateVehiculoConHistorico $$
CREATE PROCEDURE spUpdateVehiculoConHistorico(
    IN _idvehiculo       INT,
    IN _idmodelo         INT,
    IN _idtcombustible   INT,
    IN _placa            CHAR(6),
    IN _anio             CHAR(4),
    IN _numserie         VARCHAR(50),
    IN _color            VARCHAR(20),
    IN _vin              CHAR(17),
    IN _numchasis        CHAR(17),
    IN _idcliente_nuevo  INT
)
BEGIN
  DECLARE v_idcliente_actual INT;
  DECLARE v_hoy DATE;

  SET v_hoy = CURDATE();

  START TRANSACTION;

    -- 1) Actualizar los datos "estáticos" del vehículo
    UPDATE vehiculos
       SET idmodelo       = _idmodelo,
           idtcombustible = _idtcombustible,
           placa          = _placa,
           anio           = NULLIF(_anio,   ''),    
           numserie       = NULLIF(_numserie, ''),    
           color          = _color,
           vin            = NULLIF(_vin,    ''),      
           numchasis      = NULLIF(_numchasis, ''),
           modificado     = NOW()
     WHERE idvehiculo = _idvehiculo;

    -- 2) Obtener el propietario "activo" (fechafinal IS NULL), si existe
    SELECT idcliente
      INTO v_idcliente_actual
      FROM propietarios
     WHERE idvehiculo  = _idvehiculo
       AND fechafinal IS NULL
     LIMIT 1;

    -- 3) Si existe un propietario activo distinto o simplemente hay que cerrarlo:
    IF v_idcliente_actual IS NOT NULL THEN
      -- 3a) Cerrar vínculo anterior estableciendo fechafinal = hoy
      UPDATE propietarios
         SET fechafinal = v_hoy
       WHERE idvehiculo  = _idvehiculo
         AND fechafinal IS NULL;
    END IF;

    -- 4) Insertar siempre un registro NUEVO con el nuevo propietario
    INSERT INTO propietarios (
      idcliente,
      idvehiculo,
      fechainicio,
      fechafinal
    ) VALUES (
      _idcliente_nuevo,
      _idvehiculo,
      v_hoy,
      NULL
    );

  COMMIT;

  -- 5) Devolver el idcliente que ahora quedó activo ("el último insertado")
  SELECT LAST_INSERT_ID() AS idcliente_propietario_nuevo;
END $$

DROP PROCEDURE IF EXISTS spDeleteObservacion $$
CREATE PROCEDURE spDeleteObservacion(
IN _idobservacion INT
)
BEGIN
DELETE FROM observaciones WHERE idobservacion = _idobservacion;
END $$

DROP PROCEDURE IF EXISTS spUpdateObservacion $$
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


DROP PROCEDURE IF EXISTS spDeleteOrdenServicio $$
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

DROP PROCEDURE IF EXISTS spGetDetalleOrdenServicio $$
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

DROP PROCEDURE IF EXISTS spListEgresosPorPeriodo $$
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
DROP PROCEDURE IF EXISTS spRegisterEgreso $$
CREATE PROCEDURE spRegisterEgreso(
  IN _idadmin        INT,
  IN _idcolaborador  INT,
  IN _idformapago    INT,
  IN _concepto       VARCHAR(100),
  IN _monto          DECIMAL(10,2),
  IN _fecharegistro DATETIME,
  IN _numcomprobante VARCHAR(20)
)
BEGIN
  INSERT INTO egresos (
    idadmin,
    idcolaborador,
    idformapago,
    concepto,
    monto,
    fecharegistro,
    numcomprobante
  ) VALUES (
    _idadmin,
    _idcolaborador,
    _idformapago,
    _concepto,
    _monto,
    _fecharegistro,
    NULLIF(_numcomprobante, '')
  );
  SELECT LAST_INSERT_ID() AS idegreso;
END$$

DROP PROCEDURE IF EXISTS spDeleteEgreso $$
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

DROP PROCEDURE IF EXISTS spListMovimientosPorProductoPorPeriodo $$
CREATE PROCEDURE spListMovimientosPorProductoPorPeriodo(
    IN in_idproducto INT,
    IN in_modo       VARCHAR(10),
    IN in_fecha      DATE
)
BEGIN
    DECLARE v_inicio   DATETIME;
    DECLARE v_fin      DATETIME;
    DECLARE v_msg      VARCHAR(100);

    CASE
      WHEN in_modo = 'dia' THEN
        SET v_inicio = CAST(in_fecha AS DATETIME);
        SET v_fin    = DATE_ADD(v_inicio, INTERVAL 1 DAY);

      WHEN in_modo = 'semana' THEN
        SET v_inicio = DATE_SUB(CAST(in_fecha AS DATETIME), INTERVAL WEEKDAY(in_fecha) DAY);
        SET v_fin    = DATE_ADD(v_inicio, INTERVAL 7 DAY);

      WHEN in_modo = 'mes' THEN
        SET v_inicio = CAST(DATE_FORMAT(in_fecha, '%Y-%m-01') AS DATETIME);
        SET v_fin    = DATE_ADD(v_inicio, INTERVAL 1 MONTH);

      ELSE
        SET v_msg = CONCAT('Modo inválido: ', in_modo);
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_msg;
    END CASE;

    -- <-- aqui ya no hay debug, solo devuelves la tabla
    SELECT
      m.fecha,
      tm.flujo,
      tm.tipomov       AS tipo_movimiento,
      m.cantidad,
      m.saldorestante  AS saldo_restante,
      m.preciounit
    FROM movimientos AS m
    JOIN kardex          AS k  ON k.idkardex = m.idkardex
    JOIN tipomovimientos AS tm ON tm.idtipomov = m.idtipomov
    WHERE k.idproducto = in_idproducto
      AND m.fecha      >= v_inicio
      AND m.fecha      <  v_fin
    ORDER BY m.fecha;
END $$

-- 1) Actualizar Persona
DROP PROCEDURE IF EXISTS spUpdatePersona $$
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
DROP PROCEDURE IF EXISTS spUpdateEmpresa $$
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

DROP PROCEDURE IF EXISTS spGetVehiculoConPropietario $$
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

-- CALL spGetVehiculoConPropietario(5);

DROP PROCEDURE IF EXISTS spRegisterMarcaVehiculo $$
CREATE PROCEDURE spRegisterMarcaVehiculo(
IN _nombre VARCHAR(50)
)
BEGIN
INSERT INTO marcas (nombre,tipo) VALUES (_nombre,'vehiculo');
 SELECT LAST_INSERT_ID() AS idmarca;
END $$

DROP PROCEDURE IF EXISTS spRegisterModelo $$
CREATE PROCEDURE spRegisterModelo(
IN _idtipov INT,
IN _idmarca INT,
IN _modelo VARCHAR(100)
)
BEGIN
INSERT INTO modelos (idtipov,idmarca,modelo) VALUES (_idtipov,_idmarca,_modelo);
SELECT LAST_INSERT_ID() AS idmodelo;
END $$

DROP PROCEDURE IF EXISTS spRegisterMarcaProducto $$
CREATE PROCEDURE spRegisterMarcaProducto(
IN _nombre VARCHAR(50)
)
BEGIN
INSERT INTO marcas (nombre, tipo) VALUES (_nombre, 'producto');
SELECT LAST_INSERT_ID() AS idmarca;
END $$

DROP PROCEDURE IF EXISTS spRegisterCategoria $$
CREATE PROCEDURE spRegisterCategoria(
IN _categoria VARCHAR(50)
)
BEGIN 
INSERT INTO categorias (categoria) VALUES (_categoria);
SELECT LAST_INSERT_ID() AS idcategoria;
END $$

DROP PROCEDURE IF EXISTS spRegisterSubcategoria $$
CREATE PROCEDURE spRegisterSubcategoria(
IN _idcategoria INT,
IN _subcategoria VARCHAR(50)
)
BEGIN 
INSERT INTO subcategorias (idcategoria, subcategoria) VALUES (_idcategoria, _subcategoria);
SELECT LAST_INSERT_ID() AS idsubcategoria;
END $$


DROP PROCEDURE IF EXISTS spUpdateProducto $$
CREATE PROCEDURE spUpdateProducto(
  IN  _idproducto   INT,
  IN  _descripcion  VARCHAR(50),
  IN  _cantidad     DECIMAL(10,2),
  IN  _precio       DECIMAL(7,2),
  IN  _img          VARCHAR(255),    -- ruta o '' para no cambiar
  IN  _codigobarra  VARCHAR(255),    -- ruta o '' para no cambiar
  IN  _stockmin     INT,
  IN  _stockmax     INT               -- puede venir NULL para no cambiar
)
BEGIN
  -- 0) Validaciones de rangos
  IF _cantidad < 0 THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'spUpdateProducto: La cantidad no puede ser negativa';
  END IF;

  IF _precio < 0 THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'spUpdateProducto: El precio no puede ser negativo';
  END IF;

  IF _stockmin < 0 THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'spUpdateProducto: stockmin no puede ser negativo';
  END IF;

  IF _stockmax IS NOT NULL AND _stockmax < 0 THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'spUpdateProducto: stockmax no puede ser negativo';
  END IF;

  IF _stockmax IS NOT NULL AND _stockmax < _stockmin THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'spUpdateProducto: stockmax debe ser mayor o igual a stockmin';
  END IF;

  -- 1) Verificar que el producto exista
  IF NOT EXISTS (SELECT 1 FROM productos WHERE idproducto = _idproducto) THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'spUpdateProducto: Producto no existe';
  END IF;

  -- 2) Actualizar la tabla productos
  UPDATE productos
  SET
    descripcion = _descripcion,
    cantidad    = _cantidad,
    precio      = _precio,
    codigobarra = _codigobarra,
    -- Solo actualizar imagen si se envía un valor no vacío
    img         = CASE 
                    WHEN TRIM(_img) <> '' THEN _img 
                    ELSE img 
                  END
                  
  WHERE idproducto = _idproducto;

  -- 3) Actualizar la tabla kardex asociada
  UPDATE kardex
  SET
    stockmin = _stockmin,
    -- Solo actualizar stockmax si no es NULL
    stockmax = CASE 
                 WHEN _stockmax IS NOT NULL THEN _stockmax 
                 ELSE stockmax 
               END
  WHERE idproducto = _idproducto;
END$$

DROP PROCEDURE IF EXISTS spStockActualPorProducto $$
CREATE PROCEDURE spStockActualPorProducto(
  IN _idproducto INT
)
BEGIN
  DECLARE _idkardex INT;

  -- 1) Buscamos el kardex asociado a ese producto
  SELECT idkardex
    INTO _idkardex
    FROM kardex
   WHERE idproducto = _idproducto
   LIMIT 1;

  -- 2) Si no hay kardex, devolvemos NULLs en los tres campos
  IF _idkardex IS NULL THEN
    SELECT 
      NULL AS stock_actual,
      NULL AS stockmin,
      NULL AS stockmax;
  ELSE
    -- 3) Si existe kardex, devolvemos stockmin/stockmax y calculamos stock_actual
    SELECT
      COALESCE(
        ( SELECT m.saldorestante
            FROM movimientos AS m
           WHERE m.idkardex = _idkardex
           ORDER BY m.idmovimiento DESC
           LIMIT 1
        ),
        k.stockmin     -- si no hay movimientos, usamos stockmin
      )                  AS stock_actual,
      k.stockmin,
      k.stockmax
    FROM kardex AS k
    WHERE k.idkardex = _idkardex;
  END IF;
END$$

DROP PROCEDURE IF EXISTS spGetProductoById $$
CREATE PROCEDURE spGetProductoById(
  IN  _idproducto   INT
)
BEGIN
  -- 1) Verificar que el producto existe
  IF NOT EXISTS (SELECT 1 FROM productos WHERE idproducto = _idproducto) THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'spGetProductoById: Producto no existe';
  END IF;

  -- 2) Devolver datos de producto + marca, categoría, subcategoría + stocks
  SELECT
    p.idproducto,
    p.idmarca,                  -- PARA el <select> de Marca
    sc.idcategoria,             -- PARA el <select> de Categoría
    p.idsubcategoria,           -- PARA el <select> de Subcategoría
    p.descripcion,
    p.presentacion,
    p.undmedida,
    p.cantidad       AS cantidad_por_presentacion,
    p.precio,
    p.img,
    p.codigobarra,
    k.stockmin,
    k.stockmax,
    COALESCE(
      (
        SELECT m.saldorestante
        FROM movimientos AS m
        WHERE m.idkardex = k.idkardex
        ORDER BY m.idmovimiento DESC
        LIMIT 1
      ), 
      0
    ) AS stock_actual
  FROM productos AS p
  LEFT JOIN kardex AS k
    ON p.idproducto = k.idproducto
  LEFT JOIN subcategorias AS sc
    ON p.idsubcategoria = sc.idsubcategoria
  WHERE p.idproducto = _idproducto;
END$$
DROP PROCEDURE IF EXISTS spDeactivateColaborador $$
CREATE PROCEDURE spDeactivateColaborador(
  IN _idcolaborador INT,
  IN _fechafin      DATE
)
BEGIN
UPDATE colaboradores c SET
c.estado = FALSE
WHERE idcolaborador = _idcolaborador;
  -- Actualiza la fecha de fin del contrato activo de ese colaborador
  UPDATE contratos ct
    JOIN colaboradores c ON ct.idcontrato = c.idcontrato
  SET ct.fechafin = _fechafin
  WHERE c.idcolaborador = _idcolaborador;
END $$
-- select * from colaboradores;
DROP PROCEDURE IF EXISTS spUpdateColaborador $$
CREATE PROCEDURE spUpdateColaborador(
  IN _idcolaborador  INT,
  -- Datos persona
  IN _nombres        VARCHAR(50),
  IN _apellidos      VARCHAR(50),
  IN _tipodoc        VARCHAR(30),
  IN _numdoc         CHAR(20),
  IN _direccion      VARCHAR(70),
  IN _correo         VARCHAR(100),
  IN _telprincipal   VARCHAR(20),
  -- Datos contrato
  IN _idrol          INT,
  IN _fechainicio    DATE,
  IN _fechafin       DATE,
  -- Datos de acceso
  IN _namuser        VARCHAR(50),
  IN _passuser       VARCHAR(255)
)
BEGIN
  DECLARE _idcontrato INT;
  DECLARE _idpersona  INT;

  -- Obtenemos contrato y persona asociados
  SELECT idcontrato INTO _idcontrato
    FROM colaboradores
   WHERE idcolaborador = _idcolaborador;

  SELECT idpersona INTO _idpersona
    FROM contratos
   WHERE idcontrato = _idcontrato;

  START TRANSACTION;

    -- 1) Actualizar datos de la persona
    UPDATE personas
       SET nombres        = _nombres,
           apellidos      = _apellidos,
           tipodoc        = _tipodoc,
           numdoc         = _numdoc,
           direccion      = NULLIF(_direccion, ''),
           correo         = NULLIF(_correo, ''),
           telprincipal   = _telprincipal,
           modificado     = NOW()
     WHERE idpersona = _idpersona;

    -- 2) Actualizar datos del contrato
    UPDATE contratos
   SET idrol       = _idrol,
       fechainicio = CASE 
                        WHEN _fechainicio IS NULL OR _fechainicio = '' 
                          THEN fechainicio 
                        ELSE _fechainicio 
                      END,
       fechafin    = CASE 
                        WHEN _fechafin    IS NULL OR _fechafin = '' 
                          THEN fechafin 
                        ELSE _fechafin 
                      END,
       modificado  = NOW()
 WHERE idcontrato = _idcontrato;

    -- 3) Actualizar datos del usuario
    UPDATE colaboradores
        SET namuser  = _namuser,
       passuser = CASE
                    WHEN _passuser IS NULL OR _passuser = '' THEN passuser
                    ELSE SHA2(_passuser,256)
                  END
 WHERE idcolaborador = _idcolaborador;

  COMMIT;
END $$
-- select * from colaboradores;
DROP PROCEDURE IF EXISTS spGetColaboradorById $$
CREATE PROCEDURE spGetColaboradorById(
  IN _idcolaborador INT
)
BEGIN
  SELECT
    c.idcolaborador,
    c.namuser            AS username,
    c.estado             AS usuario_activo,
    -- Datos de la persona
    p.idpersona,
    p.nombres,
    p.apellidos,
    p.tipodoc,
    p.numdoc,    
    p.direccion,
    p.correo,
    p.telprincipal,
    -- Datos del contrato
    ct.idrol,
    r.rol                AS nombre_rol,
    ct.fechainicio,
    ct.fechafin
  FROM colaboradores c
  JOIN contratos    ct ON c.idcontrato = ct.idcontrato
  JOIN personas     p  ON ct.idpersona  = p.idpersona
  JOIN roles        r  ON ct.idrol      = r.idrol
  WHERE c.idcolaborador = _idcolaborador
  LIMIT 1;
END $$

DROP PROCEDURE IF EXISTS spRegisterColaborador $$
CREATE PROCEDURE spRegisterColaborador(
  -- Datos de acceso
  IN _namuser         VARCHAR(50),
  IN _passuser        VARCHAR(255),
  -- Datos de contrato
  IN _idrol           INT,
  IN _fechainicio     DATE,
  IN _fechafin        DATE,               -- NULL si contrato abierto
  -- Datos de persona
  IN _nombres         VARCHAR(50),
  IN _apellidos       VARCHAR(50),
  IN _tipodoc         VARCHAR(30),
  IN _numdoc          CHAR(20),
  IN _direccion       VARCHAR(70),
  IN _correo          VARCHAR(100),
  IN _telprincipal    VARCHAR(20)
)
BEGIN
  DECLARE _hashed_pwd   VARCHAR(64);
  DECLARE _idpersona    INT;
  DECLARE _idcontrato   INT;

  START TRANSACTION;
    -- 1) Hash de la contraseña
    SET _hashed_pwd = SHA2(_passuser, 256);

    -- 2) Insertar persona
    INSERT INTO personas (
      nombres, apellidos, tipodoc, numdoc,
       direccion, correo,
      telprincipal
    ) VALUES (
      _nombres, _apellidos, _tipodoc, _numdoc,
       NULLIF(_direccion,''), NULLIF(_correo,''),
      _telprincipal
    );
    SET _idpersona = LAST_INSERT_ID();

    -- 3) Insertar contrato
    INSERT INTO contratos (
      idrol, idpersona, fechainicio, fechafin
    ) VALUES (
      _idrol, _idpersona, _fechainicio, NULLIF(_fechafin,'')
    );
    SET _idcontrato = LAST_INSERT_ID();

    -- 4) Insertar colaborador con contraseña hasheada
    INSERT INTO colaboradores (
      idcontrato, namuser, passuser, estado
    ) VALUES (
      _idcontrato, _namuser, _hashed_pwd, TRUE
    );
  COMMIT;
END$$

DROP PROCEDURE IF EXISTS spGetDatosGeneralesVehiculo $$
CREATE PROCEDURE spGetDatosGeneralesVehiculo(
    IN in_idvehiculo INT
)
BEGIN
    SELECT
      v.idvehiculo,
      v.placa,
      v.anio,
      v.color,
      v.numserie,
      v.vin,
            v.numchasis,
      -- Unificamos teléfono y correo en un solo alias
      COALESCE(p.telprincipal, e.telefono)    AS telefono_prop,
      COALESCE(p.correo,     e.correo)        AS email_prop,
	
      tv.tipov AS tipo_vehiculo,
      tc.tcombustible,
      m.nombre AS marca,
      mo.modelo,
      c.idcliente AS id_propietario,
      -- Propietario actual (persona o empresa)
      CASE
        WHEN p.idpersona IS NOT NULL THEN CONCAT(p.nombres, ' ', p.apellidos)
        ELSE e.nomcomercial
      END AS propietario,
      COALESCE(p.numdoc, e.ruc) AS documento_propietario,
      pr.fechainicio AS propiedad_desde,
      pr.fechafinal  AS propiedad_hasta
    FROM vehiculos v
    JOIN modelos mo       ON mo.idmodelo = v.idmodelo
    JOIN marcas m         ON mo.idmarca   = m.idmarca
    JOIN tipovehiculos tv ON tv.idtipov   = mo.idtipov
    JOIN tipocombustibles tc ON tc.idtcombustible = v.idtcombustible
    LEFT JOIN propietarios pr ON pr.idvehiculo = v.idvehiculo
                               AND (pr.fechafinal IS NULL OR pr.fechafinal >= CURRENT_DATE)
    LEFT JOIN clientes c      ON c.idcliente = pr.idcliente
    LEFT JOIN personas p      ON p.idpersona = c.idpersona
    LEFT JOIN empresas e      ON e.idempresa = c.idempresa;
END $$

DROP PROCEDURE IF EXISTS spHistorialOrdenesPorVehiculo $$
CREATE PROCEDURE spHistorialOrdenesPorVehiculo(
  IN _modo        ENUM('mes','semestral','anual'),
  IN _fecha       DATE,
  IN _estado      CHAR(1),           -- 'A' activas, 'D' desactivadas
  IN _idvehiculo  INT
)
BEGIN
  DECLARE start_date DATE;
  DECLARE end_date   DATE;

  IF _modo = 'mes' THEN
    SET start_date = DATE_FORMAT(_fecha, '%Y-%m-01');
    SET end_date   = LAST_DAY(_fecha);

  ELSEIF _modo = 'semestral' THEN
    -- Últimos 6 meses desde la fecha
    SET start_date = DATE_SUB(_fecha, INTERVAL 6 MONTH);
    SET end_date   = _fecha;

  ELSEIF _modo = 'anual' THEN
    -- Todo el año calendario de la fecha
    SET start_date = DATE_FORMAT(_fecha, '%Y-01-01');
    SET end_date   = DATE_FORMAT(_fecha, '%Y-12-31');

  END IF;

  SELECT
    o.idorden,
    o.fechaingreso,
    o.fechasalida,
    v.placa,
    CASE
      WHEN cp.idpersona IS NOT NULL
        THEN CONCAT(pp.nombres, ' ', pp.apellidos)
      ELSE ce.nomcomercial
    END AS propietario,
    CASE
      WHEN cc.idpersona IS NOT NULL
        THEN CONCAT(pc.nombres, ' ', pc.apellidos)
      ELSE cce.nomcomercial
    END AS cliente
  FROM ordenservicios o
    JOIN vehiculos v      ON o.idvehiculo    = v.idvehiculo
    JOIN clientes cp      ON o.idpropietario = cp.idcliente
    LEFT JOIN personas pp ON cp.idpersona    = pp.idpersona
    LEFT JOIN empresas ce ON cp.idempresa    = ce.idempresa
    JOIN clientes cc      ON o.idcliente     = cc.idcliente
    LEFT JOIN personas pc ON cc.idpersona    = pc.idpersona
    LEFT JOIN empresas cce ON cc.idempresa   = cce.idempresa
  WHERE DATE(o.fechaingreso) BETWEEN start_date AND end_date
    AND o.estado = _estado
    AND o.idvehiculo = _idvehiculo
  ORDER BY o.fechaingreso;
END$$

DROP PROCEDURE IF EXISTS spHistorialVentasPorVehiculo $$
CREATE PROCEDURE spHistorialVentasPorVehiculo(
  IN _modo        ENUM('mes','semestral','anual'),
  IN _fecha       DATE,
  IN _idvehiculo  INT,
  IN _estado      BOOLEAN              -- TRUE = activas, FALSE = eliminadas
)
BEGIN
  DECLARE start_date DATE;
  DECLARE end_date   DATE;

  IF _modo = 'mes' THEN
    SET start_date = DATE_FORMAT(_fecha, '%Y-%m-01');
    SET end_date   = LAST_DAY(_fecha);

  ELSEIF _modo = 'semestral' THEN
    SET start_date = DATE_SUB(_fecha, INTERVAL 6 MONTH);
    SET end_date   = _fecha;

  ELSEIF _modo = 'anual' THEN
    SET start_date = DATE_FORMAT(_fecha, '%Y-01-01');
    SET end_date   = DATE_FORMAT(_fecha, '%Y-12-31');
  END IF;

  SELECT
    v.idventa                     AS id,
    COALESCE(CONCAT(pp.nombres,' ',pp.apellidos), pe.nomcomercial)
                                  AS propietario,
    CONCAT(v.numserie,'‑',v.numcom) AS comprobante,
    v.kilometraje                 AS kilometraje,
    v.tipocom                     AS tipo_comprobante,
    vt.total_pendiente            AS total_pendiente,
    CASE WHEN vt.total_pendiente=0 THEN 'pagado' ELSE 'pendiente' END AS estado_pago
  FROM ventas v
    JOIN propietarios prop        ON v.idpropietario = prop.idpropietario
    JOIN clientes c               ON prop.idcliente = c.idcliente
    LEFT JOIN personas pp         ON c.idpersona    = pp.idpersona
    LEFT JOIN empresas pe         ON c.idempresa    = pe.idempresa
    JOIN vista_saldos_por_venta vt ON v.idventa      = vt.idventa
  WHERE DATE(v.fechahora) BETWEEN start_date AND end_date
    AND v.estado = _estado
    AND v.idvehiculo = _idvehiculo
  ORDER BY v.fechahora;
END$$

DROP PROCEDURE IF EXISTS spLoginColaborador $$
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

DROP PROCEDURE IF EXISTS spGetColaboradorInfo $$
CREATE PROCEDURE spGetColaboradorInfo(
    IN in_idcolaborador INT
)
BEGIN
    SELECT
        CONCAT(p.nombres, ' ', p.apellidos) AS nombreCompleto,
        col.namuser,
        r.rol,
        -- Construye un JSON array de rutas: ["ruta1","ruta2",...]
        CONCAT(
          '[',
          IFNULL(
            GROUP_CONCAT(
              CONCAT(
                '"',
                REPLACE(v.ruta, '"', '\"'),
                '"'
              )
            ),
            ''
          ),
          ']'
        ) AS permisos
    FROM colaboradores AS col
    JOIN contratos    AS ct ON ct.idcontrato = col.idcontrato
    JOIN personas     AS p  ON p.idpersona  = ct.idpersona
    JOIN roles        AS r  ON r.idrol      = ct.idrol
    LEFT JOIN rolVistas AS rv ON rv.idrol   = r.idrol
    LEFT JOIN vistas    AS v  ON v.idvista  = rv.idvista
    WHERE col.idcolaborador = in_idcolaborador
    GROUP BY col.idcolaborador
    LIMIT 1;
END $$

DROP PROCEDURE IF EXISTS spListOrdenesPorVehiculo $$
CREATE PROCEDURE spListOrdenesPorVehiculo(
    IN in_idvehiculo INT
)
BEGIN
    SELECT
      o.idorden,
      o.fechaingreso,
      o.fechasalida,
      o.kilometraje,
      o.ingresogrua,
      o.estado,
      o.observaciones,
      col.namuser AS tecnico,
      -- total de mano de obra y repuestos (se asume cantidad = 1 por registro)
      SUM(CASE WHEN srv.servicio LIKE '%mano%' THEN dos.precio ELSE 0 END) AS total_mano_obra,
      SUM(CASE WHEN srv.servicio NOT LIKE '%mano%' THEN dos.precio ELSE 0 END) AS total_repuestos
    FROM ordenservicios o
    JOIN detalleordenservicios dos ON dos.idorden = o.idorden
    JOIN servicios srv             ON srv.idservicio = dos.idservicio
    JOIN colaboradores col         ON col.idcolaborador = dos.idmecanico
    WHERE o.idvehiculo = in_idvehiculo
    GROUP BY
      o.idorden, o.fechaingreso, o.fechasalida,
      o.kilometraje, o.ingresogrua, o.estado,
      o.observaciones, col.namuser;
END $$

DROP PROCEDURE IF EXISTS spListVentasPorVehiculo $$
CREATE PROCEDURE spListVentasPorVehiculo(
    IN in_idvehiculo INT
)
BEGIN
    SELECT
      v.idventa,
      v.fechahora,
      v.tipocom,
      CONCAT(v.numserie, '-', v.numcom) AS comprobante,
      v.moneda,
      v.kilometraje,
      col.namuser AS vendedor,
      SUM(dv.precioventa * dv.cantidad * (1 - dv.descuento/100)) AS total_neto,
      COUNT(DISTINCT dv.idproducto) AS items_vendidos
    FROM ventas v
    JOIN detalleventa dv ON dv.idventa = v.idventa
    JOIN colaboradores col ON col.idcolaborador = v.idcolaborador
    WHERE v.idvehiculo = in_idvehiculo
    GROUP BY
      v.idventa, v.fechahora, v.tipocom,
      v.numserie, v.numcom, v.moneda,
      v.kilometraje, col.namuser;
END $$

DELIMITER ;