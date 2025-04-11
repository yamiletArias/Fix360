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
numruc 			char(11) 			null,
direccion 	 	VARCHAR(70)			NULL,
correo 		 	VARCHAR(100)		NULL,
telprincipal 	VARCHAR(20)			NULL,
telalternativo VARCHAR(20)   		NULL,
creado  			TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  
modificado  	TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
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
creado  			TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  
modificado  	TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
CONSTRAINT uq_ruc UNIQUE(ruc)

)ENGINE = INNODB;

DROP TABLE IF EXISTS roles;
CREATE TABLE roles(

idrol 			INT 				PRIMARY KEY 	AUTO_INCREMENT,
rol   			VARCHAR(30) 	NOT NULL,
CONSTRAINT uq_rol UNIQUE(rol)

)ENGINE = INNODB;

DROP TABLE IF EXISTS contactabilidad;
CREATE TABLE contactabilidad(

idcontactabilidad 	INT 		   PRIMARY KEY 		AUTO_INCREMENT,
contactabilidad		VARCHAR(20) NOT NULL,
CONSTRAINT uq_contac UNIQUE(contactabilidad)

)ENGINE = INNODB;

DROP TABLE IF EXISTS formapagos;
CREATE TABLE formapagos(

idformapago 	INT 				PRIMARY KEY AUTO_INCREMENT,
formapago 		VARCHAR(50) 	NOT NULL,
CONSTRAINT uq_formapago UNIQUE(formapago)

)ENGINE = INNODB;

DROP TABLE IF EXISTS tipovehiculos;
CREATE TABLE tipovehiculos(

idtipov 		INT 					PRIMARY KEY 		AUTO_INCREMENT,
tipov 		VARCHAR(100)		NOT NULL,
CONSTRAINT uq_tipov UNIQUE(tipov)	

)ENGINE = INNODB;

DROP TABLE IF EXISTS componentes;
CREATE TABLE componentes(

idcomponente 	INT 			PRIMARY KEY 	AUTO_INCREMENT,
componente		VARCHAR(50)	NOT NULL,
CONSTRAINT uq_componente UNIQUE(componente)

)ENGINE = INNODB;

DROP TABLE IF EXISTS marcas;
CREATE TABLE marcas(
idmarca 			INT 				PRIMARY KEY 	AUTO_INCREMENT,
nombre 			VARCHAR(50)		NOT NULL,
tipo				VARCHAR(50)		NOT NULL,
CONSTRAINT uq_ntipo UNIQUE(nombre,tipo)
)ENGINE = INNODB;

DROP TABLE IF EXISTS promociones;
CREATE TABLE promociones(

idpromocion 	INT 				PRIMARY KEY AUTO_INCREMENT,
promocion 		VARCHAR(100)	NOT NULL,
fechainicio 	DATETIME 		NOT NULL,
fechafin 		DATETIME 		NOT NULL,
cantidadmax 	INT 				NOT NULL,
creado  			TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  
modificado  	TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
CONSTRAINT uq_promocion UNIQUE(promocion)

)ENGINE = INNODB;

DROP TABLE IF EXISTS condiciones;
CREATE TABLE condiciones(

idcondicion 	INT 		PRIMARY KEY AUTO_INCREMENT,
idpromocion 	INT 		NOT NULL,
descripcion 	VARCHAR(255) NOT NULL,
CONSTRAINT fk_idpromocion_1 FOREIGN KEY (idpromocion) REFERENCES promociones (idpromocion),
CONSTRAINT uq_condiciones UNIQUE(idpromocion,descripcion)

)ENGINE = INNODB;

DROP TABLE IF EXISTS tipomovimientos;
CREATE TABLE tipomovimientos(
idtipomov 		INT 				PRIMARY KEY 	AUTO_INCREMENT,
flujo 			ENUM('entrada', 'salida') 		NOT NULL,
tipomov			VARCHAR(40)		NOT NULL,
CONSTRAINT uq_fltipomv UNIQUE(flujo,tipomov)
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

DROP TABLE IF EXISTS clientes;
CREATE TABLE clientes(

idcliente 			INT 				PRIMARY KEY 	AUTO_INCREMENT,
idempresa 			INT,
idpersona 			INT,
idcontactabilidad INT 				NOT NULL,
CONSTRAINT fk_idempresa_1 FOREIGN KEY (idempresa) REFERENCES empresas (idempresa),
CONSTRAINT fk_idpersona FOREIGN KEY (idpersona) REFERENCES personas (idpersona),
CONSTRAINT chk_cliente CHECK ((idempresa IS NOT NULL AND idpersona IS NULL) OR (idempresa IS NULL AND idpersona IS NOT NULL)),
CONSTRAINT fk_idcontactabilidad FOREIGN KEY (idcontactabilidad) REFERENCES contactabilidad(idcontactabilidad),
CONSTRAINT uq_emcontac UNIQUE (idempresa,idcontactabilidad),
CONSTRAINT uq_pecontac UNIQUE (idpersona,idcontactabilidad)
) ENGINE = INNODB;

DROP TABLE IF EXISTS contratos;
CREATE TABLE contratos(
idcontrato 		INT 				PRIMARY KEY 	AUTO_INCREMENT,
idrol 			INT 				NOT NULL,
idpersona 		INT 				NOT NULL,
fechainicio 	DATE 				NOT NULL,
fechafin 		DATE 				NOT NULL,
creado  			TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  
modificado  	TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
CONSTRAINT fk_idrol FOREIGN KEY (idrol) REFERENCES roles (idrol),
CONSTRAINT fk_idpersona_1 FOREIGN KEY (idpersona) REFERENCES personas (idpersona),
CONSTRAINT uq_contrato UNIQUE (idpersona,idrol,fechainicio)
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

DROP TABLE IF EXISTS modelos;
CREATE TABLE modelos(

idmodelo 		INT 				PRIMARY KEY 		AUTO_INCREMENT,
idtipov 		INT 			NOT NULL,
idmarca 		INT 			NOT NULL,
modelo 			VARCHAR(100)	NOT NULL,
CONSTRAINT uq_modelo UNIQUE(modelo),
CONSTRAINT fk_idtipov FOREIGN KEY (idtipov) REFERENCES tipovehiculos (idtipov),
CONSTRAINT fk_idmarca_2 FOREIGN KEY (idmarca) REFERENCES marcas (idmarca)
)ENGINE = INNODB;

DROP TABLE IF EXISTS vehiculos;
CREATE TABLE vehiculos(

idvehiculo 		INT 					PRIMARY KEY 		AUTO_INCREMENT,
idmodelo 		INT 					NOT NULL,
placa 			CHAR(7)				NOT NULL,
anio				CHAR(4)				NOT NULL,
numserie 		VARCHAR(20)			NOT NULL,
color 			VARCHAR(50)			NOT NULL,
tipocombustible VARCHAR(30) 		NOT NULL,
creado  			TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  
modificado  	TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
CONSTRAINT fk_idmodelo FOREIGN KEY (idmodelo) REFERENCES modelos (idmodelo),
CONSTRAINT uq_numserie UNIQUE(numserie,placa)

)ENGINE = INNODB;

DROP TABLE IF EXISTS propietarios;
CREATE TABLE propietarios(

idpropietario 		INT 			PRIMARY KEY  AUTO_INCREMENT,
idcliente 			INT 			NOT NULL,
idvehiculo 			INT 			NOT NULL,
fechainicio 		DATE 			DEFAULT CURRENT_TIMESTAMP,
fechafinal 			DATE 			NULL,
CONSTRAINT fk_idcliente_8 FOREIGN KEY (idcliente) REFERENCES clientes (idcliente),
CONSTRAINT fk_idvehiculo_8 FOREIGN KEY (idvehiculo) REFERENCES vehiculos (idvehiculo)

)ENGINE = INNODB;

DROP TABLE IF EXISTS ordenservicios;
CREATE TABLE ordenservicios(

idorden 			INT 				PRIMARY KEY 	AUTO_INCREMENT,
idadmin 			INT 				NOT NULL,
idmecanico		INT 				NOT NULL,
idpropietario  INT 				NOT NULL,
idcliente 		INT 				NOT NULL,
idvehiculo 		INT 				NOT NULL,
kilometraje 	DECIMAL(10,2)	NOT NULL,
observaciones 	VARCHAR(255)	NOT NULL,
ingresogrua 	BOOLEAN 			NOT NULL,
fechaingreso 	DATETIME 		DEFAULT 			CURRENT_TIMESTAMP,
fechasalida 	DATETIME 		NULL, 
fecharecordatorio DATE 			NULL,
notificado 			BOOLEAN     DEFAULT FALSE,
creado  			TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  
modificado  	TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,		
CONSTRAINT fk_idadmin FOREIGN KEY (idadmin) REFERENCES colaboradores (idcolaborador),
CONSTRAINT fk_idmecanico FOREIGN KEY (idmecanico) REFERENCES colaboradores (idcolaborador),
CONSTRAINT fk_idcliente FOREIGN KEY (idcliente) REFERENCES clientes (idcliente),
CONSTRAINT fk_idvehiculo FOREIGN KEY (idvehiculo) REFERENCES vehiculos (idvehiculo),
CONSTRAINT fk_idpropietario FOREIGN KEY (idpropietario) REFERENCES propietarios (idpropietario)
)ENGINE = INNODB;

DROP TABLE IF EXISTS agendas;
CREATE TABLE agendas(
idagenda 			INT 				PRIMARY KEY 	AUTO_INCREMENT,
idpropietario		INT 				NOT NULL,
fchproxvisita 		DATETIME 		NOT NULL,
comentario 			VARCHAR(255)	NOT NULL,
estado 				BOOLEAN 			NOT NULL,
CONSTRAINT fk_idpropietario_8 FOREIGN KEY (idpropietario) REFERENCES propietarios (idpropietario)
)ENGINE = INNODB;

DROP TABLE IF EXISTS observaciones;
CREATE TABLE observaciones(

idobservacion 		INT 					PRIMARY KEY 	AUTO_INCREMENT,
idcomponente		INT 					NOT NULL,
idorden 				INT 					NOT NULL,
estado	 			BOOLEAN				NOT NULL,
foto 					VARCHAR(255) 		NULL,
CONSTRAINT fk_idcomponente FOREIGN KEY (idcomponente) REFERENCES componentes (idcomponente),
CONSTRAINT fk_idorden FOREIGN KEY (idorden) REFERENCES ordenservicios (idorden)

)ENGINE = INNODB;

DROP TABLE IF EXISTS servicios;
CREATE TABLE servicios(

idservicio			INT 					PRIMARY KEY 		AUTO_INCREMENT,
idsubcategoria 	INT 					NOT NULL,
servicio 			VARCHAR(255)		NOT NULL,
CONSTRAINT fk_idsubcategoria_1 FOREIGN KEY (idsubcategoria) REFERENCES subcategorias (idsubcategoria)	

)ENGINE = INNODB;

DROP TABLE IF EXISTS detalleordenservicios;
CREATE TABLE detalleordenservicios(

iddetorden 		INT 					PRIMARY KEY 	AUTO_INCREMENT,
idorden 			INT 					NOT NULL,
idservicio 		INT 					NOT NULL,
precio 			DECIMAL(10,2) 		NOT NULL,
CONSTRAINT fk_idorden_7 FOREIGN KEY (idorden) REFERENCES ordenservicios (idorden),
CONSTRAINT fk_idservicio_7 FOREIGN KEY (idservicio) REFERENCES servicios (idservicio)

)ENGINE = INNODB;

DROP TABLE IF EXISTS proveedores;
CREATE TABLE proveedores(
idproveedor 	INT 				PRIMARY KEY 	AUTO_INCREMENT,
idempresa 		INT 				NOT NULL,
CONSTRAINT fk_idempresa FOREIGN KEY (idempresa) REFERENCES empresas(idempresa)

) ENGINE = INNODB;

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
creado  			TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  
modificado  	TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
CONSTRAINT fk_idproveedor FOREIGN KEY (idproveedor) REFERENCES proveedores (idproveedor),
CONSTRAINT fk_idcolaborador_1 FOREIGN KEY (idcolaborador) REFERENCES colaboradores (idcolaborador),
CONSTRAINT uq_compra UNIQUE (idproveedor, tipocom, numserie, numcom)

)ENGINE = INNODB;

DROP TABLE IF EXISTS cotizaciones;
CREATE TABLE cotizaciones(
	
idcotizacion 		INT 			PRIMARY KEY AUTO_INCREMENT,
idcolaborador 		INT 			NOT NULL,
idcliente 			INT 			NULL,
fechahora 			TIMESTAMP 	DEFAULT CURRENT_TIMESTAMP,
vigenciadias 		INT 			NOT NULL,
creado  			TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  
modificado  	TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
CONSTRAINT fk_idcolaborador_2 FOREIGN KEY (idcolaborador) REFERENCES colaboradores (idcolaborador),
CONSTRAINT fk_idcliente_1 FOREIGN KEY (idcliente) REFERENCES clientes (idcliente)
	
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
creado  			TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  
modificado  	TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
CONSTRAINT fk_idmarca FOREIGN KEY (idmarca) REFERENCES marcas (idmarca),
CONSTRAINT fk_subcategoria FOREIGN KEY (idsubcategoria) REFERENCES subcategorias (idsubcategoria),
CONSTRAINT chk_precio CHECK (precio >= 0),
CONSTRAINT chk_cantidad CHECK (cantidad > 0),
CONSTRAINT uq_descripcion UNIQUE (descripcion, idsubcategoria)
)ENGINE = INNODB;

DROP TABLE IF EXISTS paquetes;
CREATE TABLE paquetes(

idpaquete 		INT 				PRIMARY KEY AUTO_INCREMENT,
idpromocion 	INT 				NOT NULL,
idproducto 		INT 				NOT NULL,
cantidad 		INT 				NOT NULL,
precioferta  	DECIMAL(10,2) 	NOT NULL,
CONSTRAINT fk_idpromocion FOREIGN KEY (idpromocion) REFERENCES promociones (idpromocion),
CONSTRAINT fk_idproducto FOREIGN KEY (idproducto) REFERENCES productos (idproducto),
CONSTRAINT chk_cantidad_p CHECK (cantidad > 0),
CONSTRAINT chk_precio_p CHECK (precioferta >= 0)

)ENGINE = INNODB;


DROP TABLE IF EXISTS kardex;
CREATE TABLE kardex(
idkardex 		INT 				PRIMARY KEY 	AUTO_INCREMENT,
idproducto		INT 				NOT NULL,
fecha				DATE 				NOT NULL,
stockmin			INT 				NOT NULL,
stockmax			INT 				NULL,
CONSTRAINT fk_idproducto_7 FOREIGN KEY (idproducto) REFERENCES productos (idproducto),
CHECK (stockmin >= 0 AND (stockmax IS NULL OR stockmax >= 0))

)ENGINE = INNODB;

DROP TABLE IF EXISTS movimientos;
CREATE TABLE movimientos(

idmovimiento 	INT 				PRIMARY KEY 	AUTO_INCREMENT,
idkardex 		INT 				NOT NULL,
idtipomov 		INT 				NOT NULL,
fecha 			DATE 				DEFAULT CURRENT_TIMESTAMP,
cantidad 		INT 				NOT NULL,
saldorestante  INT 				NOT NULL,
creado  			TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  
modificado  	TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
CONSTRAINT fk_idkardex FOREIGN KEY (idkardex) REFERENCES kardex (idkardex),
CONSTRAINT fk_idtipomov FOREIGN KEY (idtipomov) REFERENCES tipomovimientos (idtipomov),
CONSTRAINT chk_saldorestante CHECK (cantidad > 0),
CONSTRAINT chk_movimientos_cantidad CHECK (saldorestante > 0)

 )ENGINE = INNODB;
 
 DROP TABLE IF EXISTS ventas;
CREATE TABLE ventas (

idventa 			INT 		PRIMARY KEY 	AUTO_INCREMENT,
idcliente 		INT 			NOT NULL,
idcolaborador 	INT 			NOT NULL,
tipocom 		ENUM('boleta', 'factura') 		NOT NULL,
fechahora 		DATETIME 		DEFAULT 		CURRENT_TIMESTAMP,
numserie 		VARCHAR(10) 	NOT NULL,
numcom 			VARCHAR(10) 	NOT NULL,
moneda 			VARCHAR(20)		NOT NULL,
creado  			TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  
modificado  	TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
CONSTRAINT fk_idcliente_6 FOREIGN KEY (idcliente) REFERENCES clientes (idcliente),
CONSTRAINT fk_idcolaborador_6 FOREIGN KEY (idcolaborador) REFERENCES colaboradores (idcolaborador),
CONSTRAINT uq_venta UNIQUE (idcliente, tipocom, numserie, numcom)

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


DROP TABLE IF EXISTS detalleventa;
CREATE TABLE detalleventa(

iddetventa 		INT 				PRIMARY KEY 	AUTO_INCREMENT,
idproducto 		INT 				NOT NULL,
idventa 			INT 				NOT NULL,
idorden 			INT  				NOT NULL,
idpromocion 	INT 				NOT NULL,
cantidad 		INT 				NOT NULL,
numserie 		JSON 				NOT NULL,
precioventa 	DECIMAL(7,2)	NOT NULL,
descuento 		DECIMAL(5,2)  	DEFAULT 0,
creado  			TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  
modificado  	TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
CONSTRAINT fk_idpromocion_4 FOREIGN KEY (idpromocion) REFERENCES promociones (idpromocion),
CONSTRAINT fk_idproducto_1 FOREIGN KEY (idproducto) REFERENCES productos (idproducto),
CONSTRAINT fk_idventa_2 FOREIGN KEY(idventa) REFERENCES ventas (idventa),
CONSTRAINT chk_detalleventa CHECK (cantidad > 0 AND precioventa > 0),
CONSTRAINT chk_descuento CHECK (descuento BETWEEN 0 AND 100),
CONSTRAINT fk_idorden_5 FOREIGN KEY (idorden) REFERENCES ordenservicios (idorden)

)ENGINE = INNODB;

DROP TABLE IF EXISTS detallecompra;
CREATE TABLE detallecompra(

iddetcompra 	INT 				PRIMARY KEY 	AUTO_INCREMENT,
idcompra 		INT 				NOT NULL,
idproducto 		INT 				NOT NULL,
cantidad 		INT 				NOT NULL,
preciocompra   DECIMAL(7,2)	NOT NULL,
descuento 		DECIMAL(5,2)   DEFAULT 0,
creado  			TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  
modificado  	TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
CONSTRAINT fk_idcompra FOREIGN KEY (idcompra) REFERENCES compras (idcompra),
CONSTRAINT fk_idproducto_2 FOREIGN KEY (idproducto) REFERENCES productos (idproducto),
CONSTRAINT chk_detallecompra CHECK (cantidad > 0 AND preciocompra > 0),
CONSTRAINT chk_descuento CHECK (descuento BETWEEN 0 AND 100)

)ENGINE = INNODB;


DROP TABLE IF EXISTS amortizaciones;
CREATE TABLE amortizaciones(

idamortizacion 	INT 				PRIMARY KEY AUTO_INCREMENT,
idventa				INT,
idformapago			INT 				NOT NULL,
amortizacion		DECIMAL(10,2)	NOT NULL,
saldo 				DECIMAL(10,2) 	NOT NULL DEFAULT 0,
numtransaccion 	VARCHAR(20)		NULL,
CONSTRAINT fk_idventa_1 FOREIGN KEY (idventa) REFERENCES ventas (idventa),
CONSTRAINT fk_idformapago FOREIGN KEY (idformapago) REFERENCES formapagos (idformapago),
CONSTRAINT chk_amortizacion CHECK (amortizacion > 0)

)ENGINE = INNODB;
