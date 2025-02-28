USE dbfix360;

/* CRUD PARA PERSONAS */ 
DELIMITER $$

CREATE PROCEDURE spRegisterPersona( 
    IN _nombres VARCHAR(50),
    IN _apellidos VARCHAR(50),
    IN _tipodoc VARCHAR(30),
    IN _numdoc CHAR(20),
    IN _direccion VARCHAR(70),
    IN _correo VARCHAR(100),
    IN _telefono VARCHAR(20)
)
BEGIN
    INSERT INTO personas (nombres, apellidos, tipodoc, numdoc, direccion, correo, telefono)
    VALUES (_nombres, _apellidos, _tipodoc, _numdoc, _direccion, _correo, _telefono);
END $$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE spListPersonas() 
BEGIN
    SELECT * FROM personas;
END $$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE spGetPersonaBynumdoc(
    IN _numdoc INT
)
BEGIN
    SELECT * FROM personas WHERE numdoc = _numdoc;
END $$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE spUpdatePersona(
    IN _idpersona INT,
    IN _nombres VARCHAR(50),
    IN _apellidos VARCHAR(50),
    IN _tipodoc VARCHAR(30),
    IN _numdoc CHAR(20),
    IN _direccion VARCHAR(70),
    IN _correo VARCHAR(100),
    IN _telefono VARCHAR(20)
)
BEGIN
    UPDATE personas 
    SET nombres = _nombres, 
        apellidos = _apellidos, 
        tipodoc = _tipodoc, 
        numdoc = _numdoc, 
        direccion = _direccion, 
        correo = _correo, 
        telefono = _telefono
    WHERE idpersona = _idpersona;
END $$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE spDeletePersona(
    IN _idpersona INT
)
BEGIN
    DELETE FROM personas WHERE idpersona = _idpersona;
END $$

DELIMITER ;


DELIMITER $$

CREATE PROCEDURE spRegisterEmpresa( 
    IN _razonsocial VARCHAR(80),
    IN _telefono VARCHAR(20),
    IN _correo VARCHAR(100),
    IN _ruc CHAR(11)
)
BEGIN
    INSERT INTO empresas (razonsocial, telefono, correo, ruc)
    VALUES (_razonsocial, _telefono, _correo, _ruc);
END $$

DELIMITER ;

/* CRUD PARA EMPRESAS */ 
DELIMITER $$

CREATE PROCEDURE spListEmpresas() 
BEGIN
    SELECT * FROM empresas;
END $$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE spGetEmpresaByRuc(
    IN _ruc CHAR(11)
)
BEGIN
    SELECT * FROM empresas WHERE ruc = _ruc;
END $$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE spUpdateEmpresa(
    IN _idempresa INT,
    IN _razonsocial VARCHAR(80),
    IN _telefono VARCHAR(20),
    IN _correo VARCHAR(100),
    IN _ruc CHAR(11)
)
BEGIN
    UPDATE empresas 
    SET razonsocial = _razonsocial, 
        telefono = _telefono, 
        correo = _correo, 
        ruc = _ruc
    WHERE idempresa = _idempresa;
END $$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE spDeleteEmpresa(
    IN _idempresa INT
)
BEGIN
    DELETE FROM empresas WHERE idempresa = _idempresa;
END $$

DELIMITER ;


/* SPU PARA PROOVEDORES */

DELIMITER $$

CREATE PROCEDURE spRegisterProveedor(
    IN _idempresa INT
)
BEGIN
    INSERT INTO proveedores (idempresa) 
    VALUES (_idempresa);
END $$

DELIMITER ;

DELIMITER $$


CREATE PROCEDURE spListProveedores()
BEGIN
    SELECT 
        p.idproveedor,
        e.idempresa,
        e.razonsocial,
        e.telefono,
        e.correo,
        e.ruc
    FROM proveedores p
    INNER JOIN empresas e ON p.idempresa = e.idempresa;
END $$

DELIMITER ;

DELIMITER $$


CREATE PROCEDURE spGetProveedorById(
    IN _idproveedor INT
)
BEGIN
    SELECT 
        p.idproveedor,
        e.idempresa,
        e.razonsocial,
        e.telefono,
        e.correo,
        e.ruc
    FROM proveedores p
    INNER JOIN empresas e ON p.idempresa = e.idempresa
    WHERE p.idproveedor = _idproveedor;
END $$

DELIMITER ;

DELIMITER $$


CREATE PROCEDURE spUpdateProveedor(
    IN _idproveedor INT,
    IN _idempresa INT
)
BEGIN
    UPDATE proveedores 
    SET idempresa = _idempresa
    WHERE idproveedor = _idproveedor;
END $$

DELIMITER ;

DELIMITER $$


CREATE PROCEDURE spDeleteProveedor(
    IN _idproveedor INT
)
BEGIN
    DELETE FROM proveedores WHERE idproveedor = _idproveedor;
END $$

DELIMITER ;



DELIMITER $$

CREATE PROCEDURE spGetProveedorByEmpresaId(
    IN _idempresa INT
)
BEGIN
    SELECT 
        p.idproveedor,
        e.idempresa,
        e.razonsocial,
        e.telefono,
        e.correo,
        e.ruc
    FROM proveedores p
    INNER JOIN empresas e ON p.idempresa = e.idempresa
    WHERE p.idempresa = _idempresa;
END $$

DELIMITER ;

/* BUSCAR AL CLIENTE POR EL IDCLIENTE O IDEMPRESA */

DELIMITER $$

-- 🔹 Registrar un nuevo cliente
CREATE PROCEDURE spRegisterCliente(
    IN _idempresa INT,
    IN _idpersona INT
)
BEGIN
    -- Verificar que solo se registre una de las dos claves foráneas
    IF (_idempresa IS NOT NULL AND _idpersona IS NULL) OR 
       (_idempresa IS NULL AND _idpersona IS NOT NULL) THEN
        INSERT INTO clientes (idempresa, idpersona) 
        VALUES (_idempresa, _idpersona);
    ELSE
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Debe proporcionar solo idempresa o idpersona, no ambos.';
    END IF;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Listar todos los clientes
CREATE PROCEDURE spListClientes()
BEGIN
    SELECT 
        c.idcliente,
        e.idempresa,
        e.razonsocial AS empresa,
        p.idpersona,
        CONCAT(p.nombres, ' ', p.apellidos) AS persona
    FROM clientes c
    LEFT JOIN empresas e ON c.idempresa = e.idempresa
    LEFT JOIN personas p ON c.idpersona = p.idpersona;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Obtener un cliente por su ID
CREATE PROCEDURE spGetClienteById(
    IN _idcliente INT
)
BEGIN
    SELECT 
        c.idcliente,
        e.idempresa,
        e.razonsocial AS empresa,
        p.idpersona,
        CONCAT(p.nombres, ' ', p.apellidos) AS persona
    FROM clientes c
    LEFT JOIN empresas e ON c.idempresa = e.idempresa
    LEFT JOIN personas p ON c.idpersona = p.idpersona
    WHERE c.idcliente = _idcliente;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Actualizar un cliente
CREATE PROCEDURE spUpdateCliente(
    IN _idcliente INT,
    IN _idempresa INT,
    IN _idpersona INT
)
BEGIN
    -- Verificar que solo se registre una de las dos claves foráneas
    IF (_idempresa IS NOT NULL AND _idpersona IS NULL) OR 
       (_idempresa IS NULL AND _idpersona IS NOT NULL) THEN
        UPDATE clientes 
        SET idempresa = _idempresa, 
            idpersona = _idpersona
        WHERE idcliente = _idcliente;
    ELSE
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Debe proporcionar solo idempresa o idpersona, no ambos.';
    END IF;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Eliminar un cliente
CREATE PROCEDURE spDeleteCliente(
    IN _idcliente INT
)
BEGIN
    DELETE FROM clientes WHERE idcliente = _idcliente;
END $$

DELIMITER ;


DELIMITER $$

CREATE PROCEDURE spGetClienteByEmpresaOPersona(
    IN _idempresa INT,
    IN _idpersona INT
)
BEGIN
    SELECT 
        c.idcliente,
        e.idempresa,
        e.razonsocial,
        e.telefono AS empresa_telefono,
        e.correo AS empresa_correo,
        e.ruc,
        p.idpersona,
        p.nombres,
        p.apellidos,
        p.tipodoc,
        p.numdoc,
        p.direccion,
        p.correo AS persona_correo,
        p.telefono AS persona_telefono
    FROM clientes c
    LEFT JOIN empresas e ON c.idempresa = e.idempresa
    LEFT JOIN personas p ON c.idpersona = p.idpersona
    WHERE (c.idempresa = _idempresa AND _idempresa IS NOT NULL) 
       OR (c.idpersona = _idpersona AND _idpersona IS NOT NULL);
END $$

DELIMITER ;


/* SPU PARA ROLES*/

DELIMITER $$


CREATE PROCEDURE spRegisterRol(
    IN _rol VARCHAR(30)
)
BEGIN
    INSERT INTO roles (rol) 
    VALUES (_rol);
END $$

DELIMITER ;

DELIMITER $$


CREATE PROCEDURE spListRoles()
BEGIN
    SELECT * FROM roles;
END $$

DELIMITER ;

DELIMITER $$


CREATE PROCEDURE spGetRolById(
    IN _idrol INT
)
BEGIN
    SELECT * FROM roles WHERE idrol = _idrol;
END $$

DELIMITER ;

DELIMITER $$


CREATE PROCEDURE spUpdateRol(
    IN _idrol INT,
    IN _rol VARCHAR(30)
)
BEGIN
    UPDATE roles 
    SET rol = _rol
    WHERE idrol = _idrol;
END $$

DELIMITER ;

DELIMITER $$


CREATE PROCEDURE spDeleteRol(
    IN _idrol INT
)
BEGIN
    DELETE FROM roles WHERE idrol = _idrol;
END $$

DELIMITER ;

/* SPU PARA MARCAS */

DELIMITER $$

-- 🔹 Registrar una nueva marca
CREATE PROCEDURE spRegisterMarca(
    IN _nombre VARCHAR(50),
    IN _tipo VARCHAR(50)
)
BEGIN
    INSERT INTO marcas (nombre, tipo) 
    VALUES (_nombre, _tipo);
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Listar todas las marcas
CREATE PROCEDURE spListMarcas()
BEGIN
    SELECT * FROM marcas;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Obtener una marca por su ID
CREATE PROCEDURE spGetMarcaById(
    IN _idmarca INT
)
BEGIN
    SELECT * FROM marcas WHERE idmarca = _idmarca;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Actualizar una marca
CREATE PROCEDURE spUpdateMarca(
    IN _idmarca INT,
    IN _nombre VARCHAR(50),
    IN _tipo VARCHAR(50)
)
BEGIN
    UPDATE marcas 
    SET nombre = _nombre, 
        tipo = _tipo
    WHERE idmarca = _idmarca;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Eliminar una marca
CREATE PROCEDURE spDeleteMarca(
    IN _idmarca INT
)
BEGIN
    DELETE FROM marcas WHERE idmarca = _idmarca;
END $$

DELIMITER ;

/* SPU PARA CONTRATOS */

DELIMITER $$

-- 🔹 Registrar un nuevo contrato
CREATE PROCEDURE spRegisterContrato(
    IN _idrol INT,
    IN _idpersona INT,
    IN _fechainicio DATE,
    IN _fechafin DATE
)
BEGIN
    INSERT INTO contratos (idrol, idpersona, fechainicio, fechafin) 
    VALUES (_idrol, _idpersona, _fechainicio, _fechafin);
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Listar todos los contratos
CREATE PROCEDURE spListContratos()
BEGIN
    SELECT c.idcontrato, c.idrol, r.rol, c.idpersona, p.nombres, p.apellidos, c.fechainicio, c.fechafin
    FROM contratos c
    INNER JOIN roles r ON c.idrol = r.idrol
    INNER JOIN personas p ON c.idpersona = p.idpersona;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Obtener un contrato por su ID
CREATE PROCEDURE spGetContratoById(
    IN _idcontrato INT
)
BEGIN
    SELECT c.idcontrato, c.idrol, r.rol, c.idpersona, p.nombres, p.apellidos, c.fechainicio, c.fechafin
    FROM contratos c
    INNER JOIN roles r ON c.idrol = r.idrol
    INNER JOIN personas p ON c.idpersona = p.idpersona
    WHERE c.idcontrato = _idcontrato;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Actualizar un contrato
CREATE PROCEDURE spUpdateContrato(
    IN _idcontrato INT,
    IN _idrol INT,
    IN _idpersona INT,
    IN _fechainicio DATE,
    IN _fechafin DATE
)
BEGIN
    UPDATE contratos 
    SET idrol = _idrol, 
        idpersona = _idpersona,
        fechainicio = _fechainicio,
        fechafin = _fechafin
    WHERE idcontrato = _idcontrato;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Eliminar un contrato
CREATE PROCEDURE spDeleteContrato(
    IN _idcontrato INT
)
BEGIN
    DELETE FROM contratos WHERE idcontrato = _idcontrato;
END $$

DELIMITER ;

/* SPU PARA COLABORADORES*/

DELIMITER $$

-- 🔹 Registrar un nuevo colaborador
CREATE PROCEDURE spRegisterColaborador(
    IN _idcontrato INT,
    IN _namuser VARCHAR(50),
    IN _passuser VARCHAR(255)
)
BEGIN
    INSERT INTO colaboradores (idcontrato, namuser, passuser, estado) 
    VALUES (_idcontrato, _namuser, _passuser, TRUE);
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Listar todos los colaboradores con información del contrato
CREATE PROCEDURE spListColaboradores()
BEGIN
    SELECT c.idcolaborador, c.idcontrato, co.fechainicio, co.fechafin, 
           c.namuser, c.estado
    FROM colaboradores c
    INNER JOIN contratos co ON c.idcontrato = co.idcontrato;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Obtener un colaborador por su ID
CREATE PROCEDURE spGetColaboradorById(
    IN _idcolaborador INT
)
BEGIN
    SELECT c.idcolaborador, c.idcontrato, co.fechainicio, co.fechafin, 
           c.namuser, c.estado
    FROM colaboradores c
    INNER JOIN contratos co ON c.idcontrato = co.idcontrato
    WHERE c.idcolaborador = _idcolaborador;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Actualizar un colaborador
CREATE PROCEDURE spUpdateColaborador(
    IN _idcolaborador INT,
    IN _idcontrato INT,
    IN _namuser VARCHAR(50),
    IN _passuser VARCHAR(255)
)
BEGIN
    UPDATE colaboradores 
    SET idcontrato = _idcontrato, 
        namuser = _namuser, 
        passuser = _passuser
    WHERE idcolaborador = _idcolaborador;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Eliminar un colaborador
CREATE PROCEDURE spDeleteColaborador(
    IN _idcolaborador INT
)
BEGIN
    DELETE FROM colaboradores WHERE idcolaborador = _idcolaborador;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Cambiar el estado de un colaborador (activar/desactivar)
CREATE PROCEDURE spChangeEstadoColaborador(
    IN _idcolaborador INT,
    IN _estado BOOLEAN
)
BEGIN
    UPDATE colaboradores 
    SET estado = _estado
    WHERE idcolaborador = _idcolaborador;
END $$

DELIMITER ;

/* SPU PARA CATEGORIAS */

DELIMITER $$

-- 🔹 Registrar una nueva categoría
CREATE PROCEDURE spRegisterCategoria(
    IN _nombre VARCHAR(50)
)
BEGIN
    INSERT INTO categorias (nombre) VALUES (_nombre);
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Listar todas las categorías
CREATE PROCEDURE spListCategorias()
BEGIN
    SELECT idcategoria, nombre FROM categorias;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Obtener una categoría por su ID
CREATE PROCEDURE spGetCategoriaById(
    IN _idcategoria INT
)
BEGIN
    SELECT idcategoria, nombre FROM categorias WHERE idcategoria = _idcategoria;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Actualizar una categoría
CREATE PROCEDURE spUpdateCategoria(
    IN _idcategoria INT,
    IN _nombre VARCHAR(50)
)
BEGIN
    UPDATE categorias 
    SET nombre = _nombre 
    WHERE idcategoria = _idcategoria;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Eliminar una categoría
CREATE PROCEDURE spDeleteCategoria(
    IN _idcategoria INT
)
BEGIN
    DELETE FROM categorias WHERE idcategoria = _idcategoria;
END $$

DELIMITER ;


/* SPU PARA SUBCATEGORIAS*/

DELIMITER $$

-- 🔹 Registrar una nueva subcategoría
CREATE PROCEDURE spRegisterSubcategoria(
    IN _idcategoria INT,
    IN _nombre VARCHAR(50)
)
BEGIN
    INSERT INTO subcategorias (idcategoria, nombre) VALUES (_idcategoria, _nombre);
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Listar todas las subcategorías
CREATE PROCEDURE spListSubcategorias()
BEGIN
    SELECT idsubcategoria, idcategoria, nombre FROM subcategorias;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Obtener una subcategoría por su ID
CREATE PROCEDURE spGetSubcategoriaById(
    IN _idsubcategoria INT
)
BEGIN
    SELECT idsubcategoria, idcategoria, nombre 
    FROM subcategorias 
    WHERE idsubcategoria = _idsubcategoria;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Obtener todas las subcategorías de una categoría específica
CREATE PROCEDURE spGetSubcategoriasByCategoria(
    IN _idcategoria INT
)
BEGIN
    SELECT idsubcategoria, nombre 
    FROM subcategorias 
    WHERE idcategoria = _idcategoria;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Actualizar una subcategoría
CREATE PROCEDURE spUpdateSubcategoria(
    IN _idsubcategoria INT,
    IN _idcategoria INT,
    IN _nombre VARCHAR(50)
)
BEGIN
    UPDATE subcategorias 
    SET idcategoria = _idcategoria, nombre = _nombre 
    WHERE idsubcategoria = _idsubcategoria;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Eliminar una subcategoría
CREATE PROCEDURE spDeleteSubcategoria(
    IN _idsubcategoria INT
)
BEGIN
    DELETE FROM subcategorias WHERE idsubcategoria = _idsubcategoria;
END $$

DELIMITER ;

/*  SPU PARA PRODUCTOS */

DELIMITER $$

-- 🔹 Registrar un nuevo producto
CREATE PROCEDURE spRegisterProducto(
    IN _idmarca INT,
    IN _idsubcategoria INT,
    IN _nombre VARCHAR(50),
    IN _precio DECIMAL(7,2),
    IN _presentacion VARCHAR(40),
    IN _undmedida VARCHAR(40),
    IN _cantidad INT
)
BEGIN
    INSERT INTO productos (idmarca, idsubcategoria, nombre, precio, presentacion, undmedida, cantidad) 
    VALUES (_idmarca, _idsubcategoria, _nombre, _precio, _presentacion, _undmedida, _cantidad);
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Listar todos los productos
CREATE PROCEDURE spListProductos()
BEGIN
    SELECT 
        p.idproducto, p.nombre, p.precio, p.presentacion, p.undmedida, p.cantidad,
        m.nombre AS marca, s.nombre AS subcategoria
    FROM productos p
    JOIN marcas m ON p.idmarca = m.idmarca
    JOIN subcategorias s ON p.idsubcategoria = s.idsubcategoria;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Obtener un producto por su ID
CREATE PROCEDURE spGetProductoById(
    IN _idproducto INT
)
BEGIN
    SELECT 
        p.idproducto, p.nombre, p.precio, p.presentacion, p.undmedida, p.cantidad,
        m.nombre AS marca, s.nombre AS subcategoria
    FROM productos p
    JOIN marcas m ON p.idmarca = m.idmarca
    JOIN subcategorias s ON p.idsubcategoria = s.idsubcategoria
    WHERE p.idproducto = _idproducto;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Obtener todos los productos de una marca específica
CREATE PROCEDURE spGetProductosByMarca(
    IN _idmarca INT
)
BEGIN
    SELECT 
        p.idproducto, p.nombre, p.precio, p.presentacion, p.undmedida, p.cantidad, s.nombre AS subcategoria
    FROM productos p
    JOIN subcategorias s ON p.idsubcategoria = s.idsubcategoria
    WHERE p.idmarca = _idmarca;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Obtener todos los productos de una subcategoría específica
CREATE PROCEDURE spGetProductosBySubcategoria(
    IN _idsubcategoria INT
)
BEGIN
    SELECT 
        p.idproducto, p.nombre, p.precio, p.presentacion, p.undmedida, p.cantidad, m.nombre AS marca
    FROM productos p
    JOIN marcas m ON p.idmarca = m.idmarca
    WHERE p.idsubcategoria = _idsubcategoria;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Actualizar un producto
CREATE PROCEDURE spUpdateProducto(
    IN _idproducto INT,
    IN _idmarca INT,
    IN _idsubcategoria INT,
    IN _nombre VARCHAR(50),
    IN _precio DECIMAL(7,2),
    IN _presentacion VARCHAR(40),
    IN _undmedida VARCHAR(40),
    IN _cantidad INT
)
BEGIN
    UPDATE productos 
    SET idmarca = _idmarca, 
        idsubcategoria = _idsubcategoria, 
        nombre = _nombre, 
        precio = _precio, 
        presentacion = _presentacion, 
        undmedida = _undmedida, 
        cantidad = _cantidad
    WHERE idproducto = _idproducto;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Eliminar un producto
CREATE PROCEDURE spDeleteProducto(
    IN _idproducto INT
)
BEGIN
    DELETE FROM productos WHERE idproducto = _idproducto;
END $$

DELIMITER ;

/* SPU PARA KARDEX */

DELIMITER $$

-- 🔹 Registrar un nuevo kardex
CREATE PROCEDURE spRegisterKardex(
    IN _idproducto INT,
    IN _fecha DATE,
    IN _stockmin INT,
    IN _stockmax INT
)
BEGIN
    INSERT INTO kardex (idproducto, fecha, stockmin, stockmax) 
    VALUES (_idproducto, _fecha, _stockmin, _stockmax);
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Listar todos los registros del kardex
CREATE PROCEDURE spListKardex()
BEGIN
    SELECT 
        k.idkardex, k.fecha, k.stockmin, k.stockmax,
        p.nombre AS producto, p.idproducto
    FROM kardex k
    JOIN productos p ON k.idproducto = p.idproducto;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Obtener un registro del kardex por ID
CREATE PROCEDURE spGetKardexById(
    IN _idkardex INT
)
BEGIN
    SELECT 
        k.idkardex, k.fecha, k.stockmin, k.stockmax,
        p.nombre AS producto, p.idproducto
    FROM kardex k
    JOIN productos p ON k.idproducto = p.idproducto
    WHERE k.idkardex = _idkardex;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Obtener registros del kardex filtrados por producto
CREATE PROCEDURE spGetKardexByProducto(
    IN _idproducto INT
)
BEGIN
    SELECT 
        k.idkardex, k.fecha, k.stockmin, k.stockmax
    FROM kardex k
    WHERE k.idproducto = _idproducto;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Actualizar un registro del kardex
CREATE PROCEDURE spUpdateKardex(
    IN _idkardex INT,
    IN _idproducto INT,
    IN _fecha DATE,
    IN _stockmin INT,
    IN _stockmax INT
)
BEGIN
    UPDATE kardex 
    SET idproducto = _idproducto, 
        fecha = _fecha, 
        stockmin = _stockmin, 
        stockmax = _stockmax
    WHERE idkardex = _idkardex;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Eliminar un registro del kardex
CREATE PROCEDURE spDeleteKardex(
    IN _idkardex INT
)
BEGIN
    DELETE FROM kardex WHERE idkardex = _idkardex;
END $$

DELIMITER ;

/* SPU PARA TIPOS DE MOVIMIENTOS */

DELIMITER $$

-- 🔹 Registrar un nuevo tipo de movimiento
CREATE PROCEDURE spRegisterTipoMov(
    IN _flujo ENUM('entrada', 'salida'),
    IN _tipomov VARCHAR(40)
)
BEGIN
    INSERT INTO tipomovimientos (flujo, tipomov) 
    VALUES (_flujo, _tipomov);
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Listar todos los tipos de movimientos
CREATE PROCEDURE spListTipoMov()
BEGIN
    SELECT idtipomov, flujo, tipomov FROM tipomovimientos;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Obtener un tipo de movimiento por ID
CREATE PROCEDURE spGetTipoMovById(
    IN _idtipomov INT
)
BEGIN
    SELECT idtipomov, flujo, tipomov 
    FROM tipomovimientos 
    WHERE idtipomov = _idtipomov;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Actualizar un tipo de movimiento
CREATE PROCEDURE spUpdateTipoMov(
    IN _idtipomov INT,
    IN _flujo ENUM('entrada', 'salida'),
    IN _tipomov VARCHAR(40)
)
BEGIN
    UPDATE tipomovimientos 
    SET flujo = _flujo, 
        tipomov = _tipomov
    WHERE idtipomov = _idtipomov;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Eliminar un tipo de movimiento
CREATE PROCEDURE spDeleteTipoMov(
    IN _idtipomov INT
)
BEGIN
    DELETE FROM tipomovimientos WHERE idtipomov = _idtipomov;
END $$

DELIMITER ;

/* SPU PARA MOVIMIENTOS*/

DELIMITER $$

-- 🔹 Registrar un nuevo movimiento
CREATE PROCEDURE spRegisterMovimiento(
    IN _idkardex INT,
    IN _idtipomov INT,
    IN _cantidad INT
)
BEGIN
    DECLARE _stock_actual INT;
    DECLARE _saldorestante INT;
    
    -- Obtener el stock actual del kardex
    SELECT stockmax INTO _stock_actual FROM kardex WHERE idkardex = _idkardex;

    -- Validar que el stock exista en el kardex
    IF _stock_actual IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Kardex no encontrado';
    END IF;
    
    -- Calcular el saldo restante según el tipo de movimiento
    IF (SELECT flujo FROM tipomovimientos WHERE idtipomov = _idtipomov) = 'entrada' THEN
        SET _saldorestante = _stock_actual + _cantidad;
    ELSE
        SET _saldorestante = _stock_actual - _cantidad;
    END IF;
    
    -- Validar que el saldo restante no sea negativo
    IF _saldorestante < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: No hay suficiente stock para la salida';
    END IF;
    
    -- Insertar el movimiento
    INSERT INTO movimientos (idkardex, idtipomov, cantidad, saldorestante) 
    VALUES (_idkardex, _idtipomov, _cantidad, _saldorestante);
    
    -- Actualizar el stock en el kardex
    UPDATE kardex SET stockmax = _saldorestante WHERE idkardex = _idkardex;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Listar todos los movimientos
CREATE PROCEDURE spListMovimientos()
BEGIN
    SELECT m.idmovimiento, m.idkardex, k.idproducto, p.nombre AS producto,
           m.idtipomov, t.flujo, t.tipomov,
           m.fecha, m.cantidad, m.saldorestante, m.updated_at
    FROM movimientos m
    INNER JOIN kardex k ON m.idkardex = k.idkardex
    INNER JOIN productos p ON k.idproducto = p.idproducto
    INNER JOIN tipomovimientos t ON m.idtipomov = t.idtipomov;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Obtener un movimiento por ID
CREATE PROCEDURE spGetMovimientoById(
    IN _idmovimiento INT
)
BEGIN
    SELECT m.idmovimiento, m.idkardex, k.idproducto, p.nombre AS producto,
           m.idtipomov, t.flujo, t.tipomov,
           m.fecha, m.cantidad, m.saldorestante, m.updated_at
    FROM movimientos m
    INNER JOIN kardex k ON m.idkardex = k.idkardex
    INNER JOIN productos p ON k.idproducto = p.idproducto
    INNER JOIN tipomovimientos t ON m.idtipomov = t.idtipomov
    WHERE m.idmovimiento = _idmovimiento;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Actualizar un movimiento
CREATE PROCEDURE spUpdateMovimiento(
    IN _idmovimiento INT,
    IN _idkardex INT,
    IN _idtipomov INT,
    IN _cantidad INT
)
BEGIN
    DECLARE _stock_actual INT;
    DECLARE _saldorestante INT;
    
    -- Obtener el stock actual del kardex
    SELECT stockmax INTO _stock_actual FROM kardex WHERE idkardex = _idkardex;

    -- Validar que el stock exista en el kardex
    IF _stock_actual IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Kardex no encontrado';
    END IF;
    
    -- Calcular el saldo restante según el tipo de movimiento
    IF (SELECT flujo FROM tipomovimientos WHERE idtipomov = _idtipomov) = 'entrada' THEN
        SET _saldorestante = _stock_actual + _cantidad;
    ELSE
        SET _saldorestante = _stock_actual - _cantidad;
    END IF;
    
    -- Validar que el saldo restante no sea negativo
    IF _saldorestante < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: No hay suficiente stock para la salida';
    END IF;
    
    -- Actualizar el movimiento
    UPDATE movimientos 
    SET idkardex = _idkardex, idtipomov = _idtipomov, cantidad = _cantidad, saldorestante = _saldorestante 
    WHERE idmovimiento = _idmovimiento;
    
    -- Actualizar el stock en el kardex
    UPDATE kardex SET stockmax = _saldorestante WHERE idkardex = _idkardex;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Eliminar un movimiento
CREATE PROCEDURE spDeleteMovimiento(
    IN _idmovimiento INT
)
BEGIN
    DECLARE _idkardex INT;
    DECLARE _cantidad INT;
    DECLARE _idtipomov INT;
    DECLARE _flujo ENUM('entrada', 'salida');
    DECLARE _stock_actual INT;
    DECLARE _saldorestante INT;

    -- Obtener los datos del movimiento
    SELECT idkardex, idtipomov, cantidad INTO _idkardex, _idtipomov, _cantidad 
    FROM movimientos WHERE idmovimiento = _idmovimiento;

    -- Validar si el movimiento existe
    IF _idkardex IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Movimiento no encontrado';
    END IF;

    -- Obtener el stock actual del kardex
    SELECT stockmax INTO _stock_actual FROM kardex WHERE idkardex = _idkardex;

    -- Determinar si es entrada o salida
    SELECT flujo INTO _flujo FROM tipomovimientos WHERE idtipomov = _idtipomov;

    -- Ajustar el saldo restante
    IF _flujo = 'entrada' THEN
        SET _saldorestante = _stock_actual - _cantidad;
    ELSE
        SET _saldorestante = _stock_actual + _cantidad;
    END IF;

    -- Validar que el saldo restante no sea negativo
    IF _saldorestante < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: No se puede eliminar porque el stock quedaría negativo';
    END IF;

    -- Eliminar el movimiento
    DELETE FROM movimientos WHERE idmovimiento = _idmovimiento;

    -- Actualizar el stock en el kardex
    UPDATE kardex SET stockmax = _saldorestante WHERE idkardex = _idkardex;
END $$

DELIMITER ;
 /*SPU PARA VENTAS*/
DELIMITER $$

CREATE PROCEDURE spRegisterVenta(
    IN _idcliente INT,
    IN _idcolaborador INT,
    IN _tipocom VARCHAR(30),
    IN _numserie VARCHAR(10),
    IN _numcom VARCHAR(10)
)
BEGIN
    DECLARE cliente_existente INT;
    DECLARE colaborador_existente INT;
    
    -- Validar existencia del cliente
    SELECT COUNT(*) INTO cliente_existente FROM clientes WHERE idcliente = _idcliente;
    IF cliente_existente = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Cliente no encontrado';
    END IF;
    
    -- Validar existencia del colaborador
    SELECT COUNT(*) INTO colaborador_existente FROM colaboradores WHERE idcolaborador = _idcolaborador;
    IF colaborador_existente = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Colaborador no encontrado';
    END IF;
    
    -- Insertar la venta
    INSERT INTO ventas (idcliente, idcolaborador, tipocom, numserie, numcom)
    VALUES (_idcliente, _idcolaborador, _tipocom, _numserie, _numcom);
END $$

DELIMITER ;


DELIMITER $$

-- 🔹 Listar todas las ventas
CREATE PROCEDURE spListVentas()
BEGIN
    SELECT v.idventa, v.idcliente, c.nombre AS cliente, 
           v.idcolaborador, col.nombre AS colaborador,
           v.tipocom, v.numserie, v.numcom, 
           v.fechahora, v.created_at, v.updated_at
    FROM ventas v
    INNER JOIN clientes c ON v.idcliente = c.idcliente
    INNER JOIN colaboradores col ON v.idcolaborador = col.idcolaborador;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Obtener una venta por ID
CREATE PROCEDURE spGetVentaById(
    IN _idventa INT
)
BEGIN
    SELECT v.idventa, v.idcliente, c.nombre AS cliente, 
           v.idcolaborador, col.nombre AS colaborador,
           v.tipocom, v.numserie, v.numcom, 
           v.fechahora, v.created_at, v.updated_at
    FROM ventas v
    INNER JOIN clientes c ON v.idcliente = c.idcliente
    INNER JOIN colaboradores col ON v.idcolaborador = col.idcolaborador
    WHERE v.idventa = _idventa;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Actualizar una venta
CREATE PROCEDURE spUpdateVenta(
    IN _idventa INT,
    IN _idcliente INT,
    IN _idcolaborador INT,
    IN _tipocom VARCHAR(30),
    IN _numserie VARCHAR(10),
    IN _numcom VARCHAR(10)
)
BEGIN
    -- Validar existencia del cliente
    IF NOT EXISTS (SELECT 1 FROM clientes WHERE idcliente = _idcliente) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Cliente no encontrado';
    END IF;
    
    -- Validar existencia del colaborador
    IF NOT EXISTS (SELECT 1 FROM colaboradores WHERE idcolaborador = _idcolaborador) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Colaborador no encontrado';
    END IF;
    
    -- Actualizar la venta
    UPDATE ventas 
    SET idcliente = _idcliente, 
        idcolaborador = _idcolaborador, 
        tipocom = _tipocom, 
        numserie = _numserie, 
        numcom = _numcom
    WHERE idventa = _idventa;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Eliminar una venta
CREATE PROCEDURE spDeleteVenta(
    IN _idventa INT
)
BEGIN
    -- Verificar que la venta exista
    IF NOT EXISTS (SELECT 1 FROM ventas WHERE idventa = _idventa) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Venta no encontrada';
    END IF;
    
    -- Eliminar la venta
    DELETE FROM ventas WHERE idventa = _idventa;
END $$

DELIMITER ;

/* SPU PARA DETALLE DE VENTAS*/

DELIMITER $$

-- 🔹 Registrar un detalle de venta
CREATE PROCEDURE spRegisterDetalleVenta(
    IN _idproducto INT,
    IN _idventa INT,
    IN _cantidad INT,
    IN _precioventa DECIMAL(7,2),
    IN _descuento DECIMAL(5,2)
)
BEGIN
    -- Validar existencia del producto
    IF NOT EXISTS (SELECT 1 FROM productos WHERE idproducto = _idproducto) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Producto no encontrado';
    END IF;
    
    -- Validar existencia de la venta
    IF NOT EXISTS (SELECT 1 FROM ventas WHERE idventa = _idventa) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Venta no encontrada';
    END IF;
    
    -- Validar que los valores sean correctos
    IF _cantidad <= 0 OR _precioventa <= 0 OR _descuento < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Cantidad y precio deben ser mayores a 0, descuento no puede ser negativo';
    END IF;
    
    -- Insertar el detalle de venta
    INSERT INTO detalleventa (idproducto, idventa, cantidad, precioventa, descuento)
    VALUES (_idproducto, _idventa, _cantidad, _precioventa, _descuento);
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Listar detalles de una venta específica
CREATE PROCEDURE spListDetalleVenta(
    IN _idventa INT
)
BEGIN
    SELECT dv.iddetventa, dv.idproducto, p.nombre AS producto, 
           dv.idventa, dv.cantidad, dv.precioventa, dv.descuento, 
           dv.created_at, dv.updated_at
    FROM detalleventa dv
    INNER JOIN productos p ON dv.idproducto = p.idproducto
    WHERE dv.idventa = _idventa;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Obtener un detalle de venta por ID
CREATE PROCEDURE spGetDetalleVentaById(
    IN _iddetventa INT
)
BEGIN
    SELECT dv.iddetventa, dv.idproducto, p.nombre AS producto, 
           dv.idventa, dv.cantidad, dv.precioventa, dv.descuento, 
           dv.created_at, dv.updated_at
    FROM detalleventa dv
    INNER JOIN productos p ON dv.idproducto = p.idproducto
    WHERE dv.iddetventa = _iddetventa;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Actualizar un detalle de venta
CREATE PROCEDURE spUpdateDetalleVenta(
    IN _iddetventa INT,
    IN _idproducto INT,
    IN _idventa INT,
    IN _cantidad INT,
    IN _precioventa DECIMAL(7,2),
    IN _descuento DECIMAL(5,2)
)
BEGIN
    -- Validar existencia del detalle de venta
    IF NOT EXISTS (SELECT 1 FROM detalleventa WHERE iddetventa = _iddetventa) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Detalle de venta no encontrado';
    END IF;

    -- Validar existencia del producto
    IF NOT EXISTS (SELECT 1 FROM productos WHERE idproducto = _idproducto) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Producto no encontrado';
    END IF;

    -- Validar existencia de la venta
    IF NOT EXISTS (SELECT 1 FROM ventas WHERE idventa = _idventa) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Venta no encontrada';
    END IF;

    -- Validar valores correctos
    IF _cantidad <= 0 OR _precioventa <= 0 OR _descuento < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Cantidad y precio deben ser mayores a 0, descuento no puede ser negativo';
    END IF;

    -- Actualizar el detalle de venta
    UPDATE detalleventa 
    SET idproducto = _idproducto, 
        idventa = _idventa, 
        cantidad = _cantidad, 
        precioventa = _precioventa, 
        descuento = _descuento
    WHERE iddetventa = _iddetventa;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Eliminar un detalle de venta
CREATE PROCEDURE spDeleteDetalleVenta(
    IN _iddetventa INT
)
BEGIN
    -- Verificar que el detalle de venta exista
    IF NOT EXISTS (SELECT 1 FROM detalleventa WHERE iddetventa = _iddetventa) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Detalle de venta no encontrado';
    END IF;
    
    -- Eliminar el detalle de venta
    DELETE FROM detalleventa WHERE iddetventa = _iddetventa;
END $$

DELIMITER ;


/* SPU PARA COMPRAS */

DELIMITER $$

-- 🔹 Registrar una compra
CREATE PROCEDURE spRegisterCompra(
    IN _idproveedor INT,
    IN _idcolaborador INT,
    IN _fechacompra DATE,
    IN _tipocom VARCHAR(30),
    IN _numserie VARCHAR(10),
    IN _numcom VARCHAR(10),
    IN _moneda VARCHAR(20)
)
BEGIN
    -- Validar existencia del proveedor
    IF NOT EXISTS (SELECT 1 FROM proveedores WHERE idproveedor = _idproveedor) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Proveedor no encontrado';
    END IF;
    
    -- Validar existencia del colaborador
    IF NOT EXISTS (SELECT 1 FROM colaboradores WHERE idcolaborador = _idcolaborador) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Colaborador no encontrado';
    END IF;

    -- Validar que no se duplique el comprobante
    IF EXISTS (SELECT 1 FROM compras WHERE idproveedor = _idproveedor AND tipocom = _tipocom AND numserie = _numserie AND numcom = _numcom) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Comprobante de compra ya registrado';
    END IF;

    -- Insertar la compra
    INSERT INTO compras (idproveedor, idcolaborador, fechacompra, tipocom, numserie, numcom, moneda)
    VALUES (_idproveedor, _idcolaborador, _fechacompra, _tipocom, _numserie, _numcom, _moneda);
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Listar todas las compras o filtrar por proveedor
CREATE PROCEDURE spListCompras(
    IN _idproveedor INT
)
BEGIN
    IF _idproveedor = 0 THEN
        -- Listar todas las compras
        SELECT c.idcompra, c.idproveedor, p.nombre AS proveedor, 
               c.idcolaborador, col.nombre AS colaborador, 
               c.fechacompra, c.fecharegistro, c.tipocom, 
               c.numserie, c.numcom, c.moneda, c.created_at, c.updated_at
        FROM compras c
        INNER JOIN proveedores p ON c.idproveedor = p.idproveedor
        INNER JOIN colaboradores col ON c.idcolaborador = col.idcolaborador;
    ELSE
        -- Listar compras de un proveedor específico
        SELECT c.idcompra, c.idproveedor, p.nombre AS proveedor, 
               c.idcolaborador, col.nombre AS colaborador, 
               c.fechacompra, c.fecharegistro, c.tipocom, 
               c.numserie, c.numcom, c.moneda, c.created_at, c.updated_at
        FROM compras c
        INNER JOIN proveedores p ON c.idproveedor = p.idproveedor
        INNER JOIN colaboradores col ON c.idcolaborador = col.idcolaborador
        WHERE c.idproveedor = _idproveedor;
    END IF;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Obtener una compra por ID
CREATE PROCEDURE spGetCompraById(
    IN _idcompra INT
)
BEGIN
    SELECT c.idcompra, c.idproveedor, p.nombre AS proveedor, 
           c.idcolaborador, col.nombre AS colaborador, 
           c.fechacompra, c.fecharegistro, c.tipocom, 
           c.numserie, c.numcom, c.moneda, c.created_at, c.updated_at
    FROM compras c
    INNER JOIN proveedores p ON c.idproveedor = p.idproveedor
    INNER JOIN colaboradores col ON c.idcolaborador = col.idcolaborador
    WHERE c.idcompra = _idcompra;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Actualizar una compra
CREATE PROCEDURE spUpdateCompra(
    IN _idcompra INT,
    IN _idproveedor INT,
    IN _idcolaborador INT,
    IN _fechacompra DATE,
    IN _tipocom VARCHAR(30),
    IN _numserie VARCHAR(10),
    IN _numcom VARCHAR(10),
    IN _moneda VARCHAR(20)
)
BEGIN
    -- Validar existencia de la compra
    IF NOT EXISTS (SELECT 1 FROM compras WHERE idcompra = _idcompra) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Compra no encontrada';
    END IF;

    -- Validar existencia del proveedor
    IF NOT EXISTS (SELECT 1 FROM proveedores WHERE idproveedor = _idproveedor) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Proveedor no encontrado';
    END IF;

    -- Validar existencia del colaborador
    IF NOT EXISTS (SELECT 1 FROM colaboradores WHERE idcolaborador = _idcolaborador) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Colaborador no encontrado';
    END IF;

    -- Validar que no se duplique el comprobante
    IF EXISTS (SELECT 1 FROM compras WHERE idproveedor = _idproveedor AND tipocom = _tipocom AND numserie = _numserie AND numcom = _numcom AND idcompra != _idcompra) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Comprobante de compra ya registrado con otro ID';
    END IF;

    -- Actualizar la compra
    UPDATE compras 
    SET idproveedor = _idproveedor, 
        idcolaborador = _idcolaborador, 
        fechacompra = _fechacompra, 
        tipocom = _tipocom, 
        numserie = _numserie, 
        numcom = _numcom, 
        moneda = _moneda
    WHERE idcompra = _idcompra;
END $$

DELIMITER ;

DELIMITER $$

-- 🔹 Eliminar una compra
CREATE PROCEDURE spDeleteCompra(
    IN _idcompra INT
)
BEGIN
    -- Verificar que la compra exista
    IF NOT EXISTS (SELECT 1 FROM compras WHERE idcompra = _idcompra) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Compra no encontrada';
    END IF;
    
    -- Eliminar la compra
    DELETE FROM compras WHERE idcompra = _idcompra;
END $$

DELIMITER ;
 /* SPU PARA EL DETALLE DE COMPRA*/
 
DELIMITER $$

CREATE PROCEDURE spInsertDetcompra (
    IN p_idcompra INT,
    IN p_idproducto INT,
    IN p_cantidad INT,
    IN p_preciocompra DECIMAL(7,2),
    IN p_descuento DECIMAL(5,2)
)
BEGIN
    INSERT INTO detallecompra (idcompra, idproducto, cantidad, preciocompra, descuento)
    VALUES (p_idcompra, p_idproducto, p_cantidad, p_preciocompra, p_descuento);
END $$

DELIMITER ;


DELIMITER //
CREATE PROCEDURE spUpdateDetcompra (
    IN p_iddetcompra INT,
    IN p_idcompra INT,
    IN p_idproducto INT,
    IN p_cantidad INT,
    IN p_preciocompra DECIMAL(7,2),
    IN p_descuento DECIMAL(5,2)
)
BEGIN
    UPDATE detallecompra
    SET idcompra = p_idcompra,
        idproducto = p_idproducto,
        cantidad = p_cantidad,
        preciocompra = p_preciocompra,
        descuento = p_descuento,
        updated_at = CURRENT_TIMESTAMP
    WHERE iddetcompra = p_iddetcompra;
END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE spDeleteDetcompra (
    IN p_iddetcompra INT
)
BEGIN
    DELETE FROM detallecompra WHERE iddetcompra = p_iddetcompra;
END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE spGetDetcompraById (
    IN p_iddetcompra INT
)
BEGIN
    SELECT * FROM detallecompra WHERE iddetcompra = p_iddetcompra;
END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE spListDetcompraByCompra (
    IN p_idcompra INT
)
BEGIN
    SELECT * FROM detallecompra WHERE idcompra = p_idcompra;
END //
DELIMITER ;
