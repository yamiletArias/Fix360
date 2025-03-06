DROP DATABASE IF EXISTS dbfix360;
CREATE DATABASE dbfix360;
USE dbfix360;

DROP TABLE IF EXISTS personas;
CREATE TABLE personas(

idpersona 	 	INT 					PRIMARY KEY 	AUTO_INCREMENT,
nombres 		 	VARCHAR(50)			NOT NULL,
apellidos 	 	VARCHAR(50)			NOT NULL,
tipodoc 		 	VARCHAR(30)			NOT NULL,
numdoc 		 	CHAR(20)				NOT NULL,
direccion 	 	VARCHAR(70)			NOT NULL,
correo 		 	VARCHAR(100)		NOT NULL,
telprincipal 	VARCHAR(20)			NOT NULL,
telalternativo VARCHAR(20)   		NULL,
CONSTRAINT uq_numdoc UNIQUE(numdoc)

)ENGINE = INNODB;

DROP TABLE IF EXISTS empresas;
CREATE TABLE empresas(

idempresa 		INT 				PRIMARY KEY 	AUTO_INCREMENT,
nomcomercial  	VARCHAR(80)		NOT NULL,
razonsocial 	VARCHAR(80)		NOT NULL,
telefono 		VARCHAR(20)		NOT NULL,
correo 			VARCHAR(100)	NOT NULL,
ruc 				CHAR(11)			NOT NULL,
CONSTRAINT uq_ruc UNIQUE(ruc)

)ENGINE = INNODB;

DROP TABLE IF EXISTS contactabilidad;
CREATE TABLE contactabilidad(

idcontactabilidad 	INT 		   PRIMARY KEY 		AUTO_INCREMENT,
contactabilidad		VARCHAR(20) NOT NULL,
CONSTRAINT uq_contac UNIQUE(contactabilidad)

)ENGINE = INNODB;

DROP TABLE IF EXISTS proveedores;

CREATE TABLE proveedores(
idproveedor 	INT 				PRIMARY KEY 	AUTO_INCREMENT,
idempresa 		INT 				NOT NULL,
CONSTRAINT fk_idempresa FOREIGN KEY (idempresa) REFERENCES empresas(idempresa)

) ENGINE = INNODB;

DROP TABLE IF EXISTS clientes;
CREATE TABLE clientes(

idcliente 			INT 				PRIMARY KEY 	AUTO_INCREMENT,
idempresa 			INT,
idpersona 			INT,
idcontactabilidad INT 				NOT NULL,
CONSTRAINT fk_idempresa_1 FOREIGN KEY (idempresa) REFERENCES empresas (idempresa),
CONSTRAINT fk_idpersona FOREIGN KEY (idpersona) REFERENCES personas (idpersona),
CONSTRAINT chk_cliente CHECK ((idempresa IS NOT NULL AND idpersona IS NULL) OR (idempresa IS NULL AND idpersona IS NOT NULL)),
CONSTRAINT fk_idcontactabilidad FOREIGN KEY (idcontactabilidad) REFERENCES contactabilidad(idcontactabilidad)

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
passuser		VARCHAR(255)	NOT NULL,
estado 			BOOLEAN 			DEFAULT TRUE,
CONSTRAINT fk_idcontrato FOREIGN KEY (idcontrato) REFERENCES contratos (idcontrato),
CONSTRAINT uq_namuser UNIQUE (namuser)
)ENGINE = INNODB;

DROP TABLE IF EXISTS categorias;
CREATE TABLE categorias(
idcategoria 	INT 				PRIMARY KEY 	AUTO_INCREMENT,
categoria 		VARCHAR(50)		NOT NULL,
CONSTRAINT uq_categoria UNIQUE (categoria)

)ENGINE = INNODB;

DROP TABLE IF EXISTS subcategorias;
CREATE TABLE subcategorias(
idsubcategoria INT 				PRIMARY KEY 	AUTO_INCREMENT,
idcategoria 	INT 				NOT NULL,
subcategoria	VARCHAR(50) 	NOT NULL,
CONSTRAINT fk_idcategoria FOREIGN KEY (idcategoria) REFERENCES categorias (idcategoria),
CONSTRAINT uq_subcategoria UNIQUE (idcategoria, subcategoria)

)ENGINE = INNODB;

DROP TABLE IF EXISTS productos;
CREATE TABLE productos(
idproducto 		INT 				PRIMARY KEY 	AUTO_INCREMENT,
idmarca 			INT 				NOT NULL,
idsubcategoria INT 				NOT NULL,
descripcion 	VARCHAR(50)		NOT NULL,
precio 			DECIMAL(7,2) 	NOT NULL,
presentacion	VARCHAR(40)		NOT NULL,
undmedida		VARCHAR(40)    NOT NULL,
cantidad 		DECIMAL(10,2) 	NOT NULL,
img            VARCHAR(255)   NULL, 
CONSTRAINT fk_idmarca FOREIGN KEY (idmarca) REFERENCES marcas (idmarca),
CONSTRAINT fk_subcategoria FOREIGN KEY (idsubcategoria) REFERENCES subcategorias (idsubcategoria),
CONSTRAINT chk_precio CHECK (precio >= 0),
CONSTRAINT chk_cantidad CHECK (cantidad > 0),
CONSTRAINT uq_descripcion UNIQUE (descripcion, idsubcategoria)
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
CONSTRAINT chk_saldorestante CHECK (cantidad > 0),
CONSTRAINT chk_movimientos_cantidad CHECK (saldorestante > 0)

 )ENGINE = INNODB;
 
 DROP TABLE IF EXISTS ventas;
CREATE TABLE ventas (

idventa 			INT 				PRIMARY KEY 	AUTO_INCREMENT,
idcliente 		INT 				NOT NULL,
idcolaborador 	INT 				NOT NULL,
tipocom 			ENUM('boleta', 'factura') 		NOT NULL,
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
CONSTRAINT fk_idventa_2 FOREIGN KEY(idventa) REFERENCES ventas (idventa),
CONSTRAINT chk_detalleventa CHECK (cantidad > 0 AND precioventa > 0),
CONSTRAINT chk_descuento CHECK (descuento BETWEEN 0 AND 100)

)ENGINE = INNODB;

DROP TABLE IF EXISTS ordenservicios;
CREATE TABLE ordenservicios(

idorden 			INT 				PRIMARY KEY 	AUTO_INCREMENT,
idadmin 			INT 				NOT NULL,
idmecanico		INT 				NOT NULL,
idcliente 		INT 				NOT NULL,
idvehiculo 		INT 				NOT NULL,
kilometraje 	DECIMAL(10,2)	NOT NULL,
observaciones 	VARCHAR(255)	NOT NULL,
ingresogrua 	BOOLEAN 			NOT NULL,
fechaingreso 	DATETIME 		DEFAULT 			CURRENT_TIMESTAMP,
fechasalida 	DATETIME 		NULL, 		
CONSTRAINT fk_idadmin FOREIGN KEY (idadmin) REFERENCES colaboradores (idcolaborador),
CONSTRAINT fk_idmecanico FOREIGN KEY (idmecanico) REFERENCES colaboradores (idcolaborador),
CONSTRAINT fk_idcliente FOREIGN KEY (idcliente) REFERENCES clientes (idcliente),
CONSTRAINT fk_idvehiculo FOREIGN KEY (idvehiculo) REFERENCES vehiculos (idvehiculo)
)ENGINE = INNODB;

DROP TABLE IF EXISTS detalleordenservicios;
CREATE TABLE detalleordenservicios(

iddetorden 		INT 					PRIMARY KEY 	AUTO_INCREMENT,
idorden 			INT 					NOT NULL,
idservicio 		INT 					NOT NULL,
precio 			DECIMAL(10,2) 		NOT NULL,
CONSTRAINT fk_idorden FOREIGN KEY (idorden) REFERENCES ordenservicios (idorden),
CONSTRAINT fk_idservicio FOREIGN KEY (idservicio) REFERENCES servicios (idservicio)

)ENGINE = INNODB;

DROP TABLE IF EXISTS numseries;
CREATE TABLE numseries(

idnumserie 		INT 				PRIMARY KEY 		AUTO_INCREMENT,
iddetventa 		INT 				NOT NULL,
numserie 		VARCHAR(30) 	NOT NULL,
CONSTRAINT fk_iddetventa FOREIGN KEY (iddetventa) REFERENCES detalleventa (iddetventa)

)ENGINE = INNODB;

DROP TABLE IF EXISTS compras;
CREATE TABLE compras (

idcompra 		INT 				PRIMARY KEY 	AUTO_INCREMENT,
idproveedor 	INT 				NOT NULL,
idcolaborador 	INT 				NOT NULL,
fechacompra  	DATE 				NOT NULL,
fecharegistro  DATE 				DEFAULT 			CURRENT_TIMESTAMP,
tipocom 			ENUM('boleta', 'factura') 		NOT NULL,
numserie 		VARCHAR(10) 	NOT NULL,
numcom 			VARCHAR(10) 	NOT NULL,
moneda 			VARCHAR(20)		NOT NULL,
created_at  	TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  
updated_at  	TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
CONSTRAINT fk_idproveedor FOREIGN KEY (idproveedor) REFERENCES proveedores (idproveedor),
CONSTRAINT fk_idcolaborador_1 FOREIGN KEY (idcolaborador) REFERENCES colaboradores (idcolaborador),
CONSTRAINT uq_compra UNIQUE (idproveedor, tipocom, numserie, numcom)

)ENGINE = INNODB;

DROP TABLE IF EXISTS detallecompra;
CREATE TABLE detallecompra(

iddetcompra 	INT 				PRIMARY KEY 	AUTO_INCREMENT,
idcompra 		INT 				NOT NULL,
idproducto 		INT 				NOT NULL,
cantidad 		INT 				NOT NULL,
preciocompra   DECIMAL(7,2)	NOT NULL,
descuento 		DECIMAL(5,2)   DEFAULT 0,
created_at  	TIMESTAMP 		DEFAULT CURRENT_TIMESTAMP,  
updated_at  	TIMESTAMP 		DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
CONSTRAINT fk_idcompra FOREIGN KEY (idcompra) REFERENCES compras (idcompra),
CONSTRAINT fk_idproducto_2 FOREIGN KEY (idproducto) REFERENCES productos (idproducto),
CONSTRAINT chk_detallecompra CHECK (cantidad > 0 AND preciocompra > 0),
CONSTRAINT chk_descuento CHECK (descuento BETWEEN 0 AND 100)

)ENGINE = INNODB;

DROP TABLE IF EXISTS formapagos;
CREATE TABLE formapagos(

idformapago 	INT 				PRIMARY KEY AUTO_INCREMENT,
formapago 		VARCHAR(50) 	NOT NULL,
CONSTRAINT uq_formapago UNIQUE(formapago)

)ENGINE = INNODB;

DROP TABLE IF EXISTS amortizaciones;
CREATE TABLE amortizaciones(

idamortizacion 	INT 				PRIMARY KEY AUTO_INCREMENT,
idorden 				INT,
idventa				INT,
idformapago			INT 				NOT NULL,
amortizacion		DECIMAL(10,2)	NOT NULL,
saldo 				DECIMAL(10,2) 	NOT NULL DEFAULT 0,
numtransaccion 	VARCHAR(20)		NULL,
CONSTRAINT fk_idventa_1 FOREIGN KEY (idventa) REFERENCES ventas (idventa),
CONSTRAINT chk_amortizacion CHECK ((idorden IS NOT NULL AND idventa IS NULL) OR (idventa IS NOT NULL AND idorden IS NULL)),
CONSTRAINT fk_idformapago FOREIGN KEY (idformapago) REFERENCES formapagos (idformapago),
CONSTRAINT chk_amortizacion CHECK (amortizacion > 0)


)ENGINE = INNODB;

DROP TABLE IF EXISTS cotizaciones;
CREATE TABLE cotizaciones(
	
idcotizacion 		INT 			PRIMARY KEY AUTO_INCREMENT,
idcolaborador 		INT 			NOT NULL,
idcliente 			INT 			NULL,
fechahora 			TIMESTAMP 	DEFAULT CURRENT_TIMESTAMP,
vigenciadias 		INT 			NOT NULL,
CONSTRAINT fk_idcolaborador_2 FOREIGN KEY (idcolaborador) REFERENCES colaboradores (idcolaborador),
CONSTRAINT fk_idcliente_1 FOREIGN KEY (idcliente) REFERENCES clientes (idcliente)
	
)ENGINE = INNODB;

DROP TABLE IF EXISTS detallecotizacion;
CREATE TABLE detallecotizacion(

iddetcotizacion 		INT 				PRIMARY KEY 	AUTO_INCREMENT,
idcotizacion			INT 				NOT NULL,
idproducto				INT 				NOT NULL,
cantidad 				INT 				NOT NULL,
precio 					DECIMAL(7,2)	NOT NULL,
descuento 				DECIMAL(5,2)	DEFAULT 0,
CONSTRAINT fk_idcotizacion FOREIGN KEY (idcotizacion) REFERENCES cotizaciones (idcotizacion),
CONSTRAINT fk_idproducto_3 FOREIGN KEY (idproducto) REFERENCES productos (idproducto),
CONSTRAINT chk_cantidad CHECK (cantidad > 0 ),
CONSTRAINT chk_precio CHECK (precio > 0),
CONSTRAINT chk_descuento CHECK (descuento BETWEEN 0 AND 100)

)ENGINE = INNODB;

-- Tablas temporales relacionadas a servicios

DROP TABLE IF EXISTS servicios;
CREATE TABLE servicios(

idservicio			INT 					PRIMARY KEY 		AUTO_INCREMENT,
idsubcategoria 	INT 					NOT NULL,
descripcion 		VARCHAR(255)		NOT NULL,
CONSTRAINT fk_idsubcategoria_1 FOREIGN KEY (idsubcategoria) REFERENCES subcategorias (idsubcategoria)	

)ENGINE = INNODB;

DROP TABLE IF EXISTS componentes;
CREATE TABLE componentes(

idcomponente 	INT 			PRIMARY KEY 	AUTO_INCREMENT,
componente		VARCHAR(50)	NOT NULL,
CONSTRAINT uq_caracteristica UNIQUE(caracteristica)

)ENGINE = INNODB;

DROP TABLE IF EXISTS modelos;
CREATE TABLE modelos(

idmodelo 		INT 				PRIMARY KEY 		AUTO_INCREMENT,
modelo 			VARCHAR(100)	NOT NULL,
CONSTRAINT uq_modelo UNIQUE(modelo)

)ENGINE = INNODB;

DROP TABLE IF EXISTS tipovehiculos;
CREATE TABLE tipovehiculos(

idtipov 		INT 					PRIMARY KEY 		AUTO_INCREMENT,
tipov 		VARCHAR(100)		NOT NULL,
CONSTRAINT uq_tipov UNIQUE(tipov)	

)ENGINE = INNODB;

DROP TABLE IF EXISTS vehiculos;
CREATE TABLE vehiculos(

idvehiculo 		INT 					PRIMARY KEY 		AUTO_INCREMENT,
idmodelo 		INT 					NOT NULL,
idtipov			INT 					NOT NULL,
idmarca 			INT 					NOT NULL,
idcliente		INT 					NOT NULL,
placa 			CHAR(7)				NOT NULL,
anio				CHAR(4)				NOT NULL,
kilometraje 	DECIMAL(10,2)		NOT NULL,
numserie 		VARCHAR(20)			NOT NULL,
color 			VARCHAR(50)			NOT NULL,
CONSTRAINT fk_idmodelo FOREIGN KEY (idmodelo) REFERENCES modelos (idmodelo),
CONSTRAINT fk_idtipov FOREIGN KEY (idtipov) REFERENCES tipovehiculos (idtipov),
CONSTRAINT fk_idmarca_1 FOREIGN KEY (idmarca) REFERENCES marcas (idmarca),
CONSTRAINT fk_idcliente_3 FOREIGN KEY (idcliente) REFERENCES clientes (idcliente),
CONSTRAINT uq_numserie UNIQUE(numserie)

)ENGINE = INNODB;




DROP IF EXISTS observaciones;
CREATE TABLE observaciones(

idobservacion 		INT 					PRIMARY KEY 	AUTO_INCREMENT,
idcomponente		INT 					NOT NULL,
idorden 				INT 					NOT NULL,
estado	 			BOOLEAN				NOT NULL,
foto 					VARCHAR(255) 		NULL,
CONSTRAINT fk_idcomponente FOREIGN KEY (idcomponente) REFERENCES componentes (idcomponente),
CONSTRAINT fk_idorden FOREIGN KEY (idorden) REFERENCES ordenservicios (idorden)

)ENGINE = INNODB;

