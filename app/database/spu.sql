USE dbfix360;

/* SP PARA PERSONAS */ 
DELIMITER $$

CREATE PROCEDURE spRegisterPersona( 
    IN _nombres VARCHAR(50),
    IN _apellidos VARCHAR(50),
    IN _tipodoc VARCHAR(30),
    IN _numdoc CHAR(20),
    IN _direccion VARCHAR(70),
    IN _correo VARCHAR(100),
    IN _telprincipal VARCHAR(20),
    IN _telalternativo VARCHAR(20)
)
BEGIN
    INSERT INTO personas (nombres, apellidos, tipodoc, numdoc, direccion, correo, telprincipal, telalternativo)
    VALUES (_nombres, _apellidos, _tipodoc, _numdoc, _direccion, _correo, _telprincipal, _telalternativo);
END $$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE spListPersonas() 
BEGIN
    SELECT * FROM personas;
END $$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE spGetPersonaByNumdoc(
    IN _numdoc CHAR(20)
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
    IN _telprincipal VARCHAR(20),
    IN _telalternativo VARCHAR(20)
)
BEGIN
    UPDATE personas 
    SET nombres = _nombres, 
        apellidos = _apellidos, 
        tipodoc = _tipodoc, 
        numdoc = _numdoc, 
        direccion = _direccion, 
        correo = _correo, 
        telprincipal = _telprincipal, 
        telalternativo = _telalternativo
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

/* SP PARA EMPRESAS*/

DELIMITER $$

-- Procedimiento para registrar una empresa
CREATE PROCEDURE spRegisterEmpresa( 
    IN _nomcomercial VARCHAR(80),
    IN _razonsocial VARCHAR(80),
    IN _telefono VARCHAR(20),
    IN _correo VARCHAR(100),
    IN _ruc CHAR(11)
)
BEGIN
    INSERT INTO empresas (nomcomercial, razonsocial, telefono, correo, ruc)
    VALUES (_nomcomercial, _razonsocial, _telefono, _correo, _ruc);
END $$

DELIMITER ;

DELIMITER $$

-- Procedimiento para listar todas las empresas
CREATE PROCEDURE spListEmpresas() 
BEGIN
    SELECT * FROM empresas;
END $$

DELIMITER ;

DELIMITER $$

-- Procedimiento para obtener una empresa por RUC
CREATE PROCEDURE spGetEmpresaByRUC(
    IN _ruc CHAR(11)
)
BEGIN
    SELECT * FROM empresas WHERE ruc = _ruc;
END $$

DELIMITER ;

DELIMITER $$

-- Procedimiento para actualizar los datos de una empresa
CREATE PROCEDURE spUpdateEmpresa(
    IN _idempresa INT,
    IN _nomcomercial VARCHAR(80),
    IN _razonsocial VARCHAR(80),
    IN _telefono VARCHAR(20),
    IN _correo VARCHAR(100),
    IN _ruc CHAR(11)
)
BEGIN
    UPDATE empresas 
    SET nomcomercial = _nomcomercial, 
        razonsocial = _razonsocial, 
        telefono = _telefono, 
        correo = _correo, 
        ruc = _ruc
    WHERE idempresa = _idempresa;
END $$

DELIMITER ;

DELIMITER $$

-- Procedimiento para eliminar una empresa por ID
CREATE PROCEDURE spDeleteEmpresa(
    IN _idempresa INT
)
BEGIN
    DELETE FROM empresas WHERE idempresa = _idempresa;
END $$

DELIMITER ;

/* SP PARA CONTACTABILIDAD*/

DELIMITER $$

-- Procedimiento para registrar un tipo de contactabilidad
CREATE PROCEDURE spRegisterContactabilidad(
    IN _contactabilidad VARCHAR(20)
)
BEGIN
    INSERT INTO contactabilidad (contactabilidad)
    VALUES (_contactabilidad);
END $$

DELIMITER ;

DELIMITER $$

-- Procedimiento para listar todas las opciones de contactabilidad
CREATE PROCEDURE spListContactabilidad()
BEGIN
    SELECT * FROM contactabilidad;
END $$

DELIMITER ;

DELIMITER $$

-- Procedimiento para obtener un registro de contactabilidad por ID
CREATE PROCEDURE spGetContactabilidadById(
    IN _idcontactabilidad INT
)
BEGIN
    SELECT * FROM contactabilidad WHERE idcontactabilidad = _idcontactabilidad;
END $$

DELIMITER ;

DELIMITER $$

-- Procedimiento para actualizar una opción de contactabilidad
CREATE PROCEDURE spUpdateContactabilidad(
    IN _idcontactabilidad INT,
    IN _contactabilidad VARCHAR(20)
)
BEGIN
    UPDATE contactabilidad 
    SET contactabilidad = _contactabilidad
    WHERE idcontactabilidad = _idcontactabilidad;
END $$

DELIMITER ;

DELIMITER $$

-- Procedimiento para eliminar una opción de contactabilidad por ID
CREATE PROCEDURE spDeleteContactabilidad(
    IN _idcontactabilidad INT
)
BEGIN
    DELETE FROM contactabilidad WHERE idcontactabilidad = _idcontactabilidad;
END $$

DELIMITER ;

/* SP PARA PROOVEDORES */

DELIMITER $$

-- Procedimiento para registrar un proveedor
CREATE PROCEDURE spRegisterProveedor(
    IN _idempresa INT
)
BEGIN
    INSERT INTO proveedores (idempresa)
    VALUES (_idempresa);
END $$

DELIMITER ;

DELIMITER $$

-- Procedimiento para listar todos los proveedores
CREATE PROCEDURE spListProveedores()
BEGIN
    SELECT p.idproveedor, e.nomcomercial, e.razonsocial, e.telefono, e.correo, e.ruc
    FROM proveedores p
    INNER JOIN empresas e ON p.idempresa = e.idempresa;
END $$

DELIMITER ;

DELIMITER $$

-- Procedimiento para obtener un proveedor por ID
CREATE PROCEDURE spGetProveedorById(
    IN _idproveedor INT
)
BEGIN
    SELECT p.idproveedor, e.nomcomercial, e.razonsocial, e.telefono, e.correo, e.ruc
    FROM proveedores p
    INNER JOIN empresas e ON p.idempresa = e.idempresa
    WHERE p.idproveedor = _idproveedor;
END $$

DELIMITER ;

DELIMITER $$

-- Procedimiento para actualizar un proveedor
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

-- Procedimiento para eliminar un proveedor
CREATE PROCEDURE spDeleteProveedor(
    IN _idproveedor INT
)
BEGIN
    DELETE FROM proveedores WHERE idproveedor = _idproveedor;
END $$

DELIMITER ;


/* SP PARA CLIENTES*/

DELIMITER $$

-- Procedimiento para registrar un cliente (puede ser empresa o persona)
CREATE PROCEDURE spRegisterCliente(
    IN _idempresa INT,
    IN _idpersona INT,
    IN _idcontactabilidad INT
)
BEGIN
    INSERT INTO clientes (idempresa, idpersona, idcontactabilidad)
    VALUES (_idempresa, _idpersona, _idcontactabilidad);
END $$

DELIMITER ;

DELIMITER $$

-- Procedimiento para listar todos los clientes con su información
CREATE PROCEDURE spListClientes()
BEGIN
    SELECT 
        c.idcliente,
        e.nomcomercial AS empresa,
        p.nombres AS persona,
        co.contactabilidad
    FROM clientes c
    LEFT JOIN empresas e ON c.idempresa = e.idempresa
    LEFT JOIN personas p ON c.idpersona = p.idpersona
    INNER JOIN contactabilidad co ON c.idcontactabilidad = co.idcontactabilidad;
END $$

DELIMITER ;

DELIMITER $$

-- Procedimiento para obtener un cliente por ID
CREATE PROCEDURE spGetClienteById(
    IN _idcliente INT
)
BEGIN
    SELECT 
        c.idcliente,
        e.nomcomercial AS empresa,
        p.nombres AS persona,
        co.contactabilidad
    FROM clientes c
    LEFT JOIN empresas e ON c.idempresa = e.idempresa
    LEFT JOIN personas p ON c.idpersona = p.idpersona
    INNER JOIN contactabilidad co ON c.idcontactabilidad = co.idcontactabilidad
    WHERE c.idcliente = _idcliente;
END $$

DELIMITER ;

DELIMITER $$

-- Procedimiento para actualizar un cliente
CREATE PROCEDURE spUpdateCliente(
    IN _idcliente INT,
    IN _idempresa INT,
    IN _idpersona INT,
    IN _idcontactabilidad INT
)
BEGIN
    UPDATE clientes 
    SET idempresa = _idempresa, 
        idpersona = _idpersona, 
        idcontactabilidad = _idcontactabilidad
    WHERE idcliente = _idcliente;
END $$

DELIMITER ;

DELIMITER $$

-- Procedimiento para eliminar un cliente
CREATE PROCEDURE spDeleteCliente(
    IN _idcliente INT
)
BEGIN
    DELETE FROM clientes WHERE idcliente = _idcliente;
END $$

DELIMITER ;


/* SPU PARA ROLES*/

DELIMITER $$

-- Procedimiento para registrar un nuevo rol
CREATE PROCEDURE spRegisterRol(
    IN _rol VARCHAR(30)
)
BEGIN
    INSERT INTO roles (rol) VALUES (_rol);
END $$

DELIMITER ;

DELIMITER $$

-- Procedimiento para listar todos los roles
CREATE PROCEDURE spListRoles()
BEGIN
    SELECT * FROM roles;
END $$

DELIMITER ;

DELIMITER $$

-- Procedimiento para obtener un rol por ID
CREATE PROCEDURE spGetRolById(
    IN _idrol INT
)
BEGIN
    SELECT * FROM roles WHERE idrol = _idrol;
END $$

DELIMITER ;

DELIMITER $$

-- Procedimiento para actualizar un rol
CREATE PROCEDURE spUpdateRol(
    IN _idrol INT,
    IN _rol VARCHAR(30)
)
BEGIN
    UPDATE roles SET rol = _rol WHERE idrol = _idrol;
END $$

DELIMITER ;

DELIMITER $$

-- Procedimiento para eliminar un rol
CREATE PROCEDURE spDeleteRol(
    IN _idrol INT
)
BEGIN
    DELETE FROM roles WHERE idrol = _idrol;
END $$

DELIMITER ;


/* SPU PARA MARCAS */

DELIMITER $$

-- Procedimiento para registrar una nueva marca
CREATE PROCEDURE spRegisterMarca(
    IN _nombre VARCHAR(50),
    IN _tipo VARCHAR(50)
)
BEGIN
    INSERT INTO marcas (nombre, tipo) VALUES (_nombre, _tipo);
END $$

DELIMITER ;

DELIMITER $$

-- Procedimiento para listar todas las marcas
CREATE PROCEDURE spListMarcas()
BEGIN
    SELECT * FROM marcas;
END $$

DELIMITER ;

DELIMITER $$

-- Procedimiento para obtener una marca por su ID
CREATE PROCEDURE spGetMarcaById(
    IN _idmarca INT
)
BEGIN
    SELECT * FROM marcas WHERE idmarca = _idmarca;
END $$

DELIMITER ;

DELIMITER $$

-- Procedimiento para actualizar una marca
CREATE PROCEDURE spUpdateMarca(
    IN _idmarca INT,
    IN _nombre VARCHAR(50),
    IN _tipo VARCHAR(50)
)
BEGIN
    UPDATE marcas SET nombre = _nombre, tipo = _tipo WHERE idmarca = _idmarca;
END $$

DELIMITER ;

DELIMITER $$

-- Procedimiento para eliminar una marca
CREATE PROCEDURE spDeleteMarca(
    IN _idmarca INT
)
BEGIN
    DELETE FROM marcas WHERE idmarca = _idmarca;
END $$

DELIMITER ;

/* SPU PARA CONTRATOS */

DELIMITER $$

-- Procedimiento para registrar un nuevo contrato
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

-- Procedimiento para listar todos los contratos
CREATE PROCEDURE spListContratos()
BEGIN
    SELECT * FROM contratos;
END $$

DELIMITER ;

DELIMITER $$

-- Procedimiento para obtener un contrato por su ID
CREATE PROCEDURE spGetContratoById(
    IN _idcontrato INT
)
BEGIN
    SELECT * FROM contratos WHERE idcontrato = _idcontrato;
END $$

DELIMITER ;

DELIMITER $$

-- Procedimiento para actualizar un contrato
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

-- Procedimiento para eliminar un contrato
CREATE PROCEDURE spDeleteContrato(
    IN _idcontrato INT
)
BEGIN
    DELETE FROM contratos WHERE idcontrato = _idcontrato;
END $$

DELIMITER ;


/* SPU PARA COLABORADORES*/

DELIMITER $$

CREATE PROCEDURE spRegisterColaborador(
    IN _idcontrato INT,
    IN _namuser VARCHAR(50),
    IN _passuser VARCHAR(255),
    IN _estado BOOLEAN
)
BEGIN
    INSERT INTO colaboradores (idcontrato, namuser, passuser, estado)
    VALUES (_idcontrato, _namuser, _passuser, _estado);
END $$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE spListColaboradores()
BEGIN
    SELECT * FROM colaboradores;
END $$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE spGetColaboradorById(
    IN _idcolaborador INT
)
BEGIN
    SELECT * FROM colaboradores WHERE idcolaborador = _idcolaborador;
END $$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE spUpdateColaborador(
    IN _idcolaborador INT,
    IN _idcontrato INT,
    IN _namuser VARCHAR(50),
    IN _passuser VARCHAR(255),
    IN _estado BOOLEAN
)
BEGIN
    UPDATE colaboradores
    SET idcontrato = _idcontrato,
        namuser = _namuser,
        passuser = _passuser,
        estado = _estado
    WHERE idcolaborador = _idcolaborador;
END $$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE spDeleteColaborador(
    IN _idcolaborador INT
)
BEGIN
    DELETE FROM colaboradores WHERE idcolaborador = _idcolaborador;
END $$

DELIMITER ;

/* SPU PARA CATEGORIAS */

DELIMITER $$

CREATE PROCEDURE spRegisterCategoria(
    IN _categoria VARCHAR(50)
)
BEGIN
    INSERT INTO categorias (categoria) 
    VALUES (_categoria);
END $$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE spListCategorias()
BEGIN
    SELECT * FROM categorias;
END $$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE spGetCategoriaById(
    IN _idcategoria INT
)
BEGIN
    SELECT * FROM categorias WHERE idcategoria = _idcategoria;
END $$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE spUpdateCategoria(
    IN _idcategoria INT,
    IN _categoria VARCHAR(50)
)
BEGIN
    UPDATE categorias 
    SET categoria = _categoria
    WHERE idcategoria = _idcategoria;
END $$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE spDeleteCategoria(
    IN _idcategoria INT
)
BEGIN
    DELETE FROM categorias WHERE idcategoria = _idcategoria;
END $$

DELIMITER ;

/* SPU PARA SUBCATEGORIAS*/

DELIMITER $$

CREATE PROCEDURE spRegisterSubcategoria(
    IN _idcategoria INT,
    IN _subcategoria VARCHAR(50)
)
BEGIN
    INSERT INTO subcategorias (idcategoria, subcategoria)
    VALUES (_idcategoria, _subcategoria);
END $$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE spListSubcategorias()
BEGIN
    SELECT s.*, c.categoria
    FROM subcategorias s
    INNER JOIN categorias c ON s.idcategoria = c.idcategoria;
END $$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE spGetSubcategoriaById(
    IN _idsubcategoria INT
)
BEGIN
    SELECT s.*, c.categoria
    FROM subcategorias s
    INNER JOIN categorias c ON s.idcategoria = c.idcategoria
    WHERE s.idsubcategoria = _idsubcategoria;
END $$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE spUpdateSubcategoria(
    IN _idsubcategoria INT,
    IN _idcategoria INT,
    IN _subcategoria VARCHAR(50)
)
BEGIN
    UPDATE subcategorias
    SET idcategoria = _idcategoria,
        subcategoria = _subcategoria
    WHERE idsubcategoria = _idsubcategoria;
END $$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE spDeleteSubcategoria(
    IN _idsubcategoria INT
)
BEGIN
    DELETE FROM subcategorias WHERE idsubcategoria = _idsubcategoria;
END $$

DELIMITER ;

/*  SPU PARA PRODUCTOS */

DROP PROCEDURE IF EXISTS spRegisterProducto;
CREATE PROCEDURE spRegisterProducto(
    IN _idmarca INT,
    IN _idsubcategoria INT,
    IN _descripcion VARCHAR(50),
    IN _precio DECIMAL(7,2),
    IN _presentacion VARCHAR(40),
    IN _undmedida VARCHAR(40),
    IN _cantidad DECIMAL(10,2),
    IN _img VARCHAR(255)
)
BEGIN
    INSERT INTO productos (idmarca, idsubcategoria, descripcion, precio, presentacion, undmedida, cantidad, img)
    VALUES (_idmarca, _idsubcategoria, _descripcion, _precio, _presentacion, _undmedida, _cantidad, _img);
END;

DROP PROCEDURE IF EXISTS spListProductos;
CREATE PROCEDURE spListProductos()
BEGIN
    SELECT 
        p.idproducto,
        p.descripcion,
        p.precio,
        p.presentacion,
        p.undmedida,
        p.cantidad,
        p.img,
        m.nombre AS marca,
        s.subcategoria,
        c.categoria
    FROM productos p
    INNER JOIN marcas m ON p.idmarca = m.idmarca
    INNER JOIN subcategorias s ON p.idsubcategoria = s.idsubcategoria
    INNER JOIN categorias c ON s.idcategoria = c.idcategoria;
END;

DROP PROCEDURE IF EXISTS spGetProductoById;
CREATE PROCEDURE spGetProductoById(IN p_idproducto INT)
BEGIN
    SELECT 
        p.idproducto,
        p.descripcion,
        p.precio,
        p.presentacion,
        p.undmedida,
        p.cantidad,
        p.img,
        m.nombre AS marca,
        s.subcategoria,
        c.categoria
    FROM productos p
    INNER JOIN marcas m ON p.idmarca = m.idmarca
    INNER JOIN subcategorias s ON p.idsubcategoria = s.idsubcategoria
    INNER JOIN categorias c ON s.idcategoria = c.idcategoria
    WHERE p.idproducto = p_idproducto;
END;

DROP PROCEDURE IF EXISTS spUpdateProducto;
CREATE PROCEDURE spUpdateProducto(
    IN _idproducto INT,
    IN _idmarca INT,
    IN _idsubcategoria INT,
    IN _descripcion VARCHAR(50),
    IN _precio DECIMAL(7,2),
    IN _presentacion VARCHAR(40),
    IN _undmedida VARCHAR(40),
    IN _cantidad DECIMAL(10,2),
    IN _img VARCHAR(255)
)
BEGIN
    UPDATE productos 
    SET idmarca 			= _idmarca,
        idsubcategoria 	= _idsubcategoria,
        descripcion 		= _descripcion,
        precio 			= _precio,
        presentacion 	= _presentacion,
        undmedida 		= _undmedida,
        cantidad 			= _cantidad,
        img 				= _img
    WHERE idproducto 	= _idproducto;
END;

DROP PROCEDURE IF EXISTS spDeleteProducto;
CREATE PROCEDURE spDeleteProducto(IN p_idproducto INT)
BEGIN
    DELETE FROM productos WHERE idproducto = p_idproducto;
END;


/* SPU PARA KARDEX */

DROP PROCEDURE IF EXISTS spRegisterKardex;
CREATE PROCEDURE spRegisterKardex(
    IN _idproducto INT,
    IN _fecha DATE,
    IN _stockmin INT,
    IN _stockmax INT
)
BEGIN
    INSERT INTO kardex (idproducto, fecha, stockmin, stockmax)
    VALUES (_idproducto, _fecha, _stockmin, _stockmax);
END;

DROP PROCEDURE IF EXISTS spUpdateKardex;
CREATE PROCEDURE spUpdateKardex(
    IN _idkardex INT,
    IN _idproducto INT,
    IN _fecha DATE,
    IN _stockmin INT,
    IN _stockmax INT
)
BEGIN
    UPDATE kardex SET 
    idproducto 	= _idproducto, 
    fecha 			= _fecha, 
    stockmin 		= _stockmin, 
    stockmax 		= _stockmax
    WHERE idkardex = _idkardex;
END;

DROP PROCEDURE IF EXISTS spDeleteKardex;
CREATE PROCEDURE spDeleteKardex(
    IN _idkardex INT
)
BEGIN
    DELETE FROM kardex WHERE idkardex = _idkardex;
END;

DROP PROCEDURE IF EXISTS spGetAllKardex;
CREATE PROCEDURE spGetAllKardex()
BEGIN
    SELECT k.*, p.descripcion AS producto
    FROM kardex k
    INNER JOIN productos p ON k.idproducto = p.idproducto;
END;

DROP PROCEDURE IF EXISTS spFindKardex;
CREATE PROCEDURE spFindKardex(
    IN _idproducto INT
)
BEGIN
    SELECT * FROM kardex WHERE idproducto = _idproducto;
END;


/* SPU PARA TIPOS DE MOVIMIENTOS */



/* SPU PARA MOVIMIENTOS*/


 /*SPU PARA VENTAS*/


/* SPU PARA DETALLE DE VENTAS*/



/* SPU PARA COMPRAS */


 /* SPU PARA EL DETALLE DE COMPRA*/
 

