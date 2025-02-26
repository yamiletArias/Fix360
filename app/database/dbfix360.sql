DROP DATABASE IF EXISTS dbfix360;
CREATE DATABASE dbfix360;
USE dbfix360;

DROP TABLE IF EXISTS personas;
CREATE TABLE personas(

idpersona 	INT 					PRIMARY KEY 	AUTO_INCREMENT,
nombres 		VARCHAR(50)			NOT NULL,
apellidos 	VARCHAR(50)			NOT NULL,
tipodoc 		VARCHAR(30)			NOT NULL,
numdoc 		CHAR(20)				NOT NULL,
direccion 	VARCHAR(70)			NOT NULL,
correo 		VARCHAR(100)		NOT NULL,
telefono 	VARCHAR(20)			NOT NULL,
CONSTRAINT uq_numdoc UNIQUE(numdoc)

)ENGINE = INNODB;

DROP TABLE IF EXISTS empresas;
CREATE TABLE empresas(
idempresa 		INT 				PRIMARY KEY 	AUTO_INCREMENT,
razonsocial 	VARCHAR(80)		NOT NULL,
telefono 		VARCHAR(20)		NOT NULL,
correo 			VARCHAR(100)	NOT NULL,
ruc 				CHAR(11)			NOT NULL,
CONSTRAINT uq_ruc UNIQUE(ruc)
)ENGINE = INNODB;

DROP TABLE IF EXISTS proveedores;
CREATE TABLE proveedores(
idproveedor 	INT 				PRIMARY KEY 	AUTO_INCREMENT,
idempresa 		INT 				NOT NULL,
CONSTRAINT fk_idempresa FOREIGN KEY (idempresa) REFERENCES empresas(idempresa)
) ENGINE = INNODB;

DROP TABLE IF EXISTS clientes;
CREATE TABLE clientes(
idcliente 		INT 				PRIMARY KEY 	AUTO_INCREMENT,
idempresa 		INT,
idpersona 		INT,
CONSTRAINT fk_idempresa_1 FOREIGN KEY (idempresa) REFERENCES empresas (idempresa),
CONSTRAINT fk_idpersona FOREIGN KEY (idpersona) REFERENCES personas (idpersona),
CONSTRAINT chk_cliente CHECK (
        (idempresa IS NOT NULL AND idpersona IS NULL) OR 
        (idempresa IS NULL AND idpersona IS NOT NULL)
    )
) ENGINE = INNODB;

DROP TABLE IF EXISTS roles;
CREATE TABLE roles(
idrol 			INT 				PRIMARY KEY 	AUTO_INCREMENT,
rol   			VARCHAR(30) 	NOT NULL
)ENGINE = INNODB;

DROP TABLE IF EXISTS marcas;
CREATE TABLE marcas(
idmarca 			INT 				PRIMARY KEY 	AUTO_INCREMENT,
nombre 			VARCHAR(50)		NOT NULL,
tipo				VARCHAR(50)		NOT NULL
)ENGINE = INNODB;

DROP TABLE IF EXISTS contratos;
CREATE TABLE contratos(
idcontrato 		INT 				PRIMARY KEY 	AUTO_INCREMENT,
idrol 			INT 				NOT NULL,
idpersona 		INT 				NOT NULL,
fechainicio 	DATE 				NOT NULL,
fechafin 		DATE 				NOT NULL,
CONSTRAINT fk_idrol FOREIGN KEY (idrol) REFERENCES roles (idrol),
CONSTRAINT fk_idpersona_1 FOREIGN KEY (idpersona) REFERENCES personas (idpersona)
) ENGINE = INNODB;

DROP TABLE IF EXISTS colaboradores;
CREATE TABLE colaboradores(
idcolaborador	INT 				PRIMARY KEY 	AUTO_INCREMENT,
idcontrato 		INT 				NOT NULL,
namuser			VARCHAR(50)		NOT NULL,
passuser			VARCHAR(255)	NOT NULL,
estado 			BOOLEAN 			DEFAULT TRUE,
CONSTRAINT fk_idcontrato FOREIGN KEY (idcontrato) REFERENCES contratos (idcontrato),
CONSTRAINT uq_namuser UNIQUE (namuser)
)ENGINE = INNODB;

DROP TABLE IF EXISTS categorias;
CREATE TABLE categorias(
idcategoria 	INT 				PRIMARY KEY 	AUTO_INCREMENT,
nombre 			VARCHAR(50)		NOT NULL
)ENGINE = INNODB;

DROP TABLE IF EXISTS subcategorias;
CREATE TABLE subcategorias(
idsubcategoria INT 				PRIMARY KEY 	AUTO_INCREMENT,
idcategoria 	INT 				NOT NULL,
nombre			VARCHAR(50) 	NOT NULL,
CONSTRAINT fk_idcategoria FOREIGN KEY (idcategoria) REFERENCES categorias (idcategoria)
)ENGINE = INNODB;

DROP TABLE IF EXISTS productos;
CREATE TABLE productos(
idproducto 		INT 				PRIMARY KEY 	AUTO_INCREMENT,
idmarca 			INT 				NOT NULL,
idsubcategoria INT 				NOT NULL,
nombre 			VARCHAR(50)		NOT NULL,
precio 			DECIMAL(7,2) 	NOT NULL,
presentacion	VARCHAR(40)		NOT NULL,
undmedida		VARCHAR(40)    NOT NULL,
cantidad 		INT 				DEFAULT 0,
CONSTRAINT fk_idmarca FOREIGN KEY (idmarca) REFERENCES marcas (idmarca),
CONSTRAINT fk_subcategoria FOREIGN KEY (idsubcategoria) REFERENCES subcategorias (idsubcategoria),
CONSTRAINT chk_precio CHECK (precio >= 0),
CONSTRAINT chk_cantidad CHECK (cantidad >= 0),
CONSTRAINT uq_nombre UNIQUE (nombre, idsubcategoria)
)ENGINE = INNODB;

DROP TABLE IF EXISTS kardex;
CREATE TABLE kardex(
idkardex 		INT 				PRIMARY KEY 	AUTO_INCREMENT,
idproducto		INT 				NOT NULL,
fecha				DATE 				NOT NULL,
stockmin			INT 				NOT NULL,
stockmax			INT 				NULL,
CONSTRAINT fk_idproducto FOREIGN KEY (idproducto) REFERENCES productos (idproducto),
CHECK (stockmin >= 0 AND (stockmax IS NULL OR stockmax >= 0))

)ENGINE = INNODB;

DROP TABLE IF EXISTS tipomovimientos;
CREATE TABLE tipomovimientos(
idtipomov 		INT 				PRIMARY KEY 	AUTO_INCREMENT,
flujo 			ENUM('entrada', 'salida') 		NOT NULL,
tipomov			VARCHAR(40)		NOT NULL
)ENGINE = INNODB;

DROP TABLE IF EXISTS movimientos;
CREATE TABLE movimientos(
idmovimiento 	INT 				PRIMARY KEY 	AUTO_INCREMENT,
idkardex 		INT 				NOT NULL,
idtipomov 		INT 				NOT NULL,
fecha 			DATE 				DEFAULT CURRENT_TIMESTAMP,
cantidad 		INT 				NOT NULL,
saldorestante  INT 				NOT NULL,
updated_at  	TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
CONSTRAINT fk_idkardex FOREIGN KEY (idkardex) REFERENCES kardex (idkardex),
CONSTRAINT fk_idtipomov FOREIGN KEY (idtipomov) REFERENCES tipomovimientos (idtipomov),
CONSTRAINT chk_saldorestante CHECK (saldorestante >= 0),
CONSTRAINT chk_movimientos_cantidad CHECK (cantidad > 0)


 )ENGINE = INNODB;
 
 DROP TABLE IF EXISTS ventas;
CREATE TABLE ventas (
idventa 			INT 				PRIMARY KEY 	AUTO_INCREMENT,
idcliente 		INT 				NOT NULL,
idcolaborador 	INT 				NOT NULL,
tipocom 			VARCHAR(30)		NOT NULL,
fechahora 		DATETIME 		DEFAULT 			CURRENT_TIMESTAMP,
numserie 		VARCHAR(10) 	NOT NULL,
numcom 			VARCHAR(10) 	NOT NULL,
created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  
updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
CONSTRAINT fk_idcliente FOREIGN KEY (idcliente) REFERENCES clientes (idcliente),
CONSTRAINT fk_idcolaborador FOREIGN KEY (idcolaborador) REFERENCES colaboradores (idcolaborador),
CONSTRAINT uq_venta UNIQUE (idcliente, tipocom, numserie, numcom)
)ENGINE = INNODB; 

DROP TABLE IF EXISTS detalleventa;
CREATE TABLE detalleventa(
iddetventa 		INT 				PRIMARY KEY 	AUTO_INCREMENT,
idproducto 		INT 				NOT NULL,
idventa 			INT 				NOT NULL,
cantidad 		INT 				NOT NULL,
precioventa 	DECIMAL(7,2)	NOT NULL,
descuento 		DECIMAL(5,2)  	DEFAULT 0,
created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  
updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
CONSTRAINT fk_idproducto_1 FOREIGN KEY (idproducto) REFERENCES productos (idproducto),
CONSTRAINT fk_idventa FOREIGN KEY(idventa) REFERENCES ventas (idventa),
CONSTRAINT chk_detalleventa CHECK (cantidad > 0 AND precioventa > 0)

)ENGINE = INNODB;

DROP TABLE IF EXISTS compras;
CREATE TABLE compras (
idcompra 		INT 				PRIMARY KEY 	AUTO_INCREMENT,
idproveedor 	INT 				NOT NULL,
idcolaborador 	INT 				NOT NULL,
fechacompra  	DATE 				NOT NULL,
fecharegistro  DATE 				DEFAULT 			CURRENT_TIMESTAMP,
tipocom 			VARCHAR(30)		NOT NULL,
numserie 		VARCHAR(10) 	NOT NULL,
numcom 			VARCHAR(10) 	NOT NULL,
moneda 			VARCHAR(20)		NOT NULL,
created_at  	TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  
updated_at  	TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
CONSTRAINT fk_idproveedor FOREIGN KEY (idproveedor) REFERENCES proveedores (idproveedor),
CONSTRAINT fk_idcolaborador_1 FOREIGN KEY (idcolaborador) REFERENCES colaboradores (idcolaborador),
CONSTRAINT uq_compra UNIQUE (idproveedor, tipocom, numserie, numcom)

)ENGINE = INN
ODB;

DROP TABLE IF EXISTS detallecompra;
CREATE TABLE detallecompra(
iddetcompra 	INT 				PRIMARY KEY 	AUTO_INCREMENT,
idcompra 		INT 				NOT NULL,
idproducto 		INT 				NOT NULL,
cantidad 		INT 				NOT NULL,
preciocompra   DECIMAL(7,2)	NOT NULL,
descuento 		DECIMAL(5,2)   DEFAULT 0,
created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  
updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
CONSTRAINT fk_idcompra FOREIGN KEY (idcompra) REFERENCES compras (idcompra),
CONSTRAINT fk_idproducto_2 FOREIGN KEY (idproducto) REFERENCES productos (idproducto),
CONSTRAINT chk_detallecompra CHECK (cantidad > 0 AND preciocompra > 0)

)ENGINE = INNODB;