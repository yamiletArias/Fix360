USE dbfix360;

INSERT INTO tipovehiculos (tipov) VALUES 
('Sedan'),
('Hatchback'),
('SUV'),
('Deportivo'),
('Camioneta'),
('Van'),
('Pick up'),
('Convertible'),
('Compacto');

INSERT INTO contactabilidad (contactabilidad)
VALUES
  ('Facebook'),
  ('Instagram'),
  ('tiktok'),
  ('Folletos'),
  ('Campaña publicitaria'),
  ('Recomendacion');
  
  INSERT INTO categorias (categoria)
 VALUES ('servicio');
 
  INSERT INTO subcategorias (idcategoria,subcategoria)
 VALUES (1,'Direccion y suspencion'),
  (1,'Mecanica general'),
  (1,'Lubricacion'),
 (1,'Otros servicios');
 
  INSERT INTO roles (rol) VALUES ('administrador'),('Jefe Mecanico'),('mecanico'),('Marketing');
  
  INSERT INTO formapagos (formapago) VALUES 
('Deposito'),
('Visa'),
('Plin'),
('Yape'),
('Efectivo');

INSERT INTO tipomovimientos (flujo,tipomov) 
VALUES ('entrada','compra'),('salida','venta'),('entrada','devolucion'),('salida', 'devolucion'),('entrada', 'stock inicial');

/*
INSERT INTO personas (nombres, apellidos, tipodoc, numdoc, numruc, direccion, correo, telprincipal, telalternativo) VALUES
('Maria Elena', 'Castila Herrera', 'DNI', '75849320', '20123456789', 'Av. Los Pinos 123', 'elenafix360@gmail.com', '987654321', NULL);
*/
CALL spRegisterColaborador('ElenaCastilla','fix3602025',1,CURDATE(),NULL,'Elena', 'Castilla', 'DNI', '75849320', 'Av. Los Pinos 123', 'elenafix360@gmail.com', '987654321');

-- select * from rolvistas  
-- select * from vistas  

INSERT INTO vistas (nombre,ruta) VALUES 
('vistaprincipal','listar-movdiario.php'), -- 1
('listayregistroagenda','listar-agendas.php'), -- 2
-- VENTAS
('listaventas','listar-ventas.php'),
('ventas','registrar-ventas-orden.php'),
-- CAJA
('arqueocaja','listar-arqueo-caja.php'),
-- COMPRAS
('listacompras','listar-compras.php'),
('registrocompras','registrar-compras.php'),
-- CLIENTE
('listarclientes','listar-cliente.php'),
('registrarclientes','registrar-cliente.php'),
('editarclientes','editar-cliente.php'),
-- COLABORADORES
('listacolaboradores','listar-colaborador.php'),
('registrocolaboradores','registrar-colaborador.php'),
('editarcolaboradores','editar-colaborador.php'),
-- GRAFICOS
('contactabilidad','listar-graficos.php'),
-- COTIZACIONES
('listarcotizaciones','listar-cotizacion.php'),
('registrarcotizaciones','registrar-cotizacion.php'),
-- EGRESOS
('listarvistaegresos','listar-egresos.php'),
('registrarvistaegresos','registrar-egresos.php'),
-- KARDEX
('vistakardex','listar-kardex.php'),
-- ORDEN DE SERVICIO
('editarobservacionordenservicios','editar-observacion-ordenes.php'),
('editarordenservicios','editar-ordenes.php'),
('listarobservacionordenservicios','listar-observacion-orden.php'),
('listarobservacionordenservicios2','listar-observacion-orden2.php'),
('listarordenservicios','listar-ordenes.php'),
('listarserviciosordenservicios','listar-serviciosbrindados.php'),
('registrarordenservicios','registrar-observacion-ordenes.php'),
('ordenservicios','registrar-ordenes2.php'),
-- PRODUCTOS
('listarproductos','listar-producto.php'),
('registrarproductos','registrar-productos.php'),
('productos','editar-productos.php'),
-- PROMOCIONES
('listarpromociones','listar-promociones.php'),
('registrarpromociones','registrar-promociones.php'),
('editarpromociones','editar-promociones.php'),
-- VEHICULOS
('listarvehiculos','listar-vehiculos.php'),
('vehiculoscliente','vehiculo-cliente.php'),
('historialvehiculos','historial-vehiculos-prueba.php'),
('registrarvehiculos','registrar-vehiculos.php'),
('observacionvehiculos','observacion-vehiculos.php'),
('editarvehiculo','editar-vehiculos.php');
-- select * from vistas
-- select * from roles
INSERT INTO rolvistas (idrol,idvista) VALUES
(1,1),
(1,2),
(1,3),
(1,4),
(1,5),
(1,6),
(1,7),
(1,8),
(1,9),
(1,10),
(1,11),
(1,12),
(1,13),
(1,14),
(1,15),
(1,16),
(1,17),
(1,18),
(1,19),
(1,20),
(1,21),
(1,22),
(1,23),
(1,24),
(1,25),
(1,26),
(1,27),
(1,28),
(1,29),
(1,30),
(1,31),
(1,32),
(1,33),
(1,34),
(1,35),
(1,36),
(1,37),
(1,38),
(1,39);
-- select * from roles
-- select * from vistas
-- PERMISOS PARA LOS JEFE MECANICOS
INSERT INTO rolvistas (idrol,idvista) VALUES
(2,1),
(2,22),
(2,24);
-- PERMISOS PARA LOS MECANICOS
INSERT INTO rolvistas (idrol,idvista) VALUES
(3,1),
(3,22),
(3,24);
-- select * from contratos
-- PERMISO PARA MARKETING
INSERT INTO rolvistas (idrol,idvista) VALUES
(4,1),
(4,14);

-- select * from ordenservicios
-- select * from colaboradores


	INSERT INTO marcas (nombre, tipo) VALUES
('S/M', 'S/M'),
('SHELL HELIX', 'ACEITE'),
('CHEVRON', 'ACEITE'),
('CASTROL', 'ACEITE'),
('MOTUL', 'ACEITE'),
('WILLIAMS', 'ACEITE'),
('REPSOL', 'ACEITE'),
('BARDHAL', 'ACEITE'),
('ALTERNATIVO', 'GENÉRICO'),
('NGK', 'BUJÍAS'),
('BOSCH', 'BUJÍAS'),
('NYLON', 'OTROS'),
('KASUKI', 'OTROS'),
('ALTERNO', 'OTROS'),
('3PK', 'OTROS'),
('LYS', 'FILTROS'),
('MOBIS', 'FILTROS'),
('BLS', 'FILTROS'),
('REDFIL', 'FILTROS'),
('OIL FILTER', 'FILTROS'),
('WANDER', 'FILTROS'),
('KUSA', 'FILTROS'),
('AIR FILTER', 'FILTROS'),
('IHP FILTER', 'FILTROS'),
('IHC', 'OTROS'),
('S6 PLUS', 'OTROS'),
('VISTONY', 'LUBRICANTES'),
('ISHIKAWA', 'FRENOS'),
('NIBK', 'FRENOS'),
('MR POSTERIORES', 'FRENOS'),
('MR DELANTEROS', 'FRENOS'),
('DE FRENO S/M', 'S/M'),
('AUTOLAND', 'OTROS'),
('TOTO', 'OTROS'),
('DE FRENO SDE MANO', 'FRENOS'),
('GOOD YEARS', 'LLANTAS'),
('HYUNDAI I10', 'VEHÍCULOS'),
('ADEX', 'OTROS'),
('ALMEYDA', 'OTROS'),
('PRESTONE', 'LÍQUIDOS'),
('MEGA GREY', 'OTROS'),
('GMB', 'OTROS'),
('RKD', 'OTROS'),
('JBG', 'OTROS');


INSERT INTO categorias (categoria) VALUES
('ABRAZADERAS'),
('ACEITE'),
('ARANDELA'),
('BOBINA'),
('BUJIAS'),
('CINTILLOS'),
('DISCO'),
('EMPAQUE'),
('FAJA'),
('FILTRO'),
('FOCOS'),
('GRASA'),
('JEBE'),
('JUEGO'),
('KIT'),
('LIQUIDO'),
('LLANTA'),
('ESPEJO'),
('MANGUERA'),
('ORRING'),
('PEGAMENTO'),
('PONCHO'),
('RADIADOR'),
('RECTIFICADORA'),
('REFRIGERANTE'),
('REGULADOR DE ZAPATAS'),
('RESORTES'),
('RETEN'),
('SILICONA'),
('SOPORTE DE CAJA'),
('SUPLEX'),
('TEMPLADOR'),
('TRICETA'),
('VALVULA PCV VALVE');

INSERT INTO subcategorias (idcategoria, subcategoria) VALUES
(2,'ABRAZADERAS'),
(3,'ACEITE DE MOTOR'),
(4,'ARANDELA'),
(5,'BOBINA DE ENCENDIDO'),
(6,'BUJIAS'),
(7,'CINTILLOS'),
(8,'DISCOS'),
(9,'EMPAQUE'),
(10,'FAJA'),
(11,'FILTRO DE ACEITE'),
(11,'FILTRO DE AIRE'),
(11,'FILTRO DE AIRE ACONDICIONADO'),
(11,'FILTRO DE CABINA'),
(11,'FILTRO DE GASOLINA'),
(11,'FILTRO DE PETROLEO'),
(12,'FOCOS LED'),
(13,'GRASA'),
(14,'JEBES'),
(15,'JUEGO DE PASTILLAS'),
(15,'JUEGO DE ZAPATAS'),
(15,'JUEGO DE PISOS'),
(16,'KIT DE EMPAQUE DE MOTOR'),
(16,'KIT DE ACCESORIOS'),
(17,'LIQUIDO DE FRENO'),
(18,'LLANTA PARA AUTOS'),
(19,'LUNA DE ESPEJO IZQUIERDO'),
(20,'MANGUERA DE BRIDA'),
(20,'MANGUERA DE AGUA'),
(20,'MANGUERA DE RADIADOR DE REVOSE'),
(21,'ORRING'),
(22,'PEGAMENTO'),
(23,'PONCHO'),
(23,'PONCHO DE PALIER'),
(24,'RADIADOR'),
(25,'RECTIFICADORA'),
(26,'REFRIGERANTE PRE MEZCLADO'),
(27,'REFRIGERANTE ANTIFREEZE'),
(28,'REGULADOR DE ZAPATAS'),
(28,'RESORTES'),
(29,'RETEN'),
(30,'SILICONA'),
(31,'SOPORTE DE CAJA'),
(32,'SUPLEX'),
(33,'TEMPLADOR'),
(34,'TRICETA'),
(35,'VALVULA PCV VALVE');

INSERT INTO marcas (nombre, tipo) VALUES
('Toyota','vehiculo'),
('Honda','vehiculo'),
('Ford','vehiculo'),
('Hyundai','vehiculo'),
('Chevrolet','vehiculo'),
('Nissan','vehiculo'),
('BMW','vehiculo'),
('Kia','vehiculo'),
('BYD','vehiculo'),
('Audi','vehiculo'),
('BAIC','vehiculo'),
('Briliance','vehiculo'),
('Changan','vehiculo'),
('Chery','vehiculo'),
('Daihatsu','vehiculo');


SET @idproducto = 0;

-- Registro de productos usando spRegisterProducto:
-- Paréntesis: (_idsubcategoria, _idmarca, _descripcion, _precioc, _preciov, _presentacion, _undmedida, _cantidad, _img, _codigobarra, _stockInicial, _stockmin, _stockmax, OUT _idproducto)

CALL spRegisterProducto(6, 16, 'Aceite multigrado SHELL 20W50',      34.04, 48.50, 'botella', 'Litros',   4.00, '', '',  4, 1, 100, @idproducto);
CALL spRegisterProducto(6, 17, 'Aceite sintético CHEVRON 10W30',     38.50, 55.00, 'botella', 'Litros',   4.00, '', '',  4, 1, 100, @idproducto);
CALL spRegisterProducto(6, 18, 'Aceite mineral CASTROL GTX 20W50',   30.53, 42.90, 'botella', 'Litros',   4.00, '', '',  4, 1, 100, @idproducto);
CALL spRegisterProducto(6, 19, 'Aceite MOTUL 300V Competition',      56.11, 79.90, 'botella', 'Litros',   2.00, '', '',  2, 1, 100, @idproducto);
CALL spRegisterProducto(6, 20, 'Aceite WILLIAMS Premium 15W40',      26.50, 35.00, 'botella', 'Litros',   4.00, '', '',  4, 1, 100, @idproducto);
CALL spRegisterProducto(6, 21, 'Aceite REPSOL Elite 5W40',           57.37, 65.00, 'botella', 'Litros',   4.00, '', '',  4, 1, 100, @idproducto);
CALL spRegisterProducto(6, 22, 'Aceite BARDHAL B1 Plus',             29.70, 33.00, 'botella', 'Litros',   4.00, '', '',  4, 1, 100, @idproducto);

CALL spRegisterProducto(40, 26, 'Refrigerante VISTONY Premix -37C', 16.90, 22.50, 'botella', 'Litros',   1.00, '', '',  1, 1, 100, @idproducto);
CALL spRegisterProducto(41, 42, 'Refrigerante PRESTONE Antifreeze', 22.52, 25.90, 'botella', 'Litros',   1.00, '', '',  1, 1, 100, @idproducto);

CALL spRegisterProducto(9, 24, 'Bujía NGK Iridium IX',              15.22, 18.90, 'caja',    'Unidades', 4.00, '', '',  4, 1, 100, @idproducto);
CALL spRegisterProducto(9, 25, 'Bujía BOSCH Platinum',              17.65, 19.90, 'caja',    'Unidades', 4.00, '', '',  4, 1, 100, @idproducto);

CALL spRegisterProducto(24, 31, 'Juego de zapatas MR POSTERIORES',  57.09, 75.00, 'juego',   'Juegos',   1.00, '', '',  1, 1, 100, @idproducto);
CALL spRegisterProducto(23, 32, 'Juego de pastillas MR DELANTEROS', 68.17, 82.00, 'juego',   'Juegos',   1.00, '', '',  1, 1, 100, @idproducto);
CALL spRegisterProducto(28, 28, 'Líquido de freno NIBK DOT 4',      12.59, 14.00, 'botella', 'Litros',   0.50, '', '',  1, 1, 100, @idproducto);
CALL spRegisterProducto(28, 41, 'Líquido de freno PRESTONE DOT 3',   8.19, 11.50, 'botella', 'Litros',   0.50, '', '',  1, 1, 100, @idproducto);

CALL spRegisterProducto(29, 35, 'Llanta GOOD YEARS Eagle 185/60 R15',172.11,215.00,'unidad','Unidades', 1.00, '', '',  1, 1, 100, @idproducto);

CALL spRegisterProducto(14, 23, 'Filtro de aceite AIR FILTER AF-01',11.12,13.90,'unidad','Unidades',   1.00, '', '',  1, 1, 100, @idproducto);
CALL spRegisterProducto(18, 20, 'Filtro de gasolina OIL FILTER GF-3',13.52,16.90,'unidad','Unidades',   1.00, '', '',  1, 1, 100, @idproducto);
CALL spRegisterProducto(17, 22, 'Filtro de cabina KUSA K-99',        12.72,15.90,'unidad','Unidades',   1.00, '', '',  1, 1, 100, @idproducto);
CALL spRegisterProducto(15, 18, 'Filtro de aire BLS AirMax',         11.60,14.50,'unidad','Unidades',   1.00, '', '',  1, 1, 100, @idproducto);
CALL spRegisterProducto(19, 37, 'Filtro de petróleo MOBIS MP-202',   15.04,18.80,'unidad','Unidades',   1.00, '', '',  1, 1, 100, @idproducto);

CALL spRegisterProducto(13, 17, 'Faja de distribución CHEVRON FD-100',24.14,32.00,'unidad','Unidades',   1.00, '', '',  1, 1, 100, @idproducto);
CALL spRegisterProducto(21, 40, 'Grasa MEGA GREY para rodamientos',   7.60, 9.50, 'pote',   'Gramos',   250.00,'','',250, 1, 100, @idproducto);
CALL spRegisterProducto(44, 43, 'Retén JBG trasero motor 45mm',       9.18,12.90,'unidad','Unidades',   1.00, '', '',  1, 1, 100, @idproducto);
CALL spRegisterProducto(25, 39, 'Juego de pisos ALMEYDA universal',   40.00,50.00,'juego','Juegos',     1.00, '', '',  1, 1, 100, @idproducto);
CALL spRegisterProducto(37, 33, 'Poncho de palier AUTOLAND reforzado',14.40,18.00,'unidad','Unidades',   1.00, '', '',  1, 1, 100, @idproducto);
CALL spRegisterProducto(12, 44, 'Empaque de culata RKD EC-09',        17.60,22.00,'unidad','Unidades',   1.00, '', '',  1, 1, 100, @idproducto);
CALL spRegisterProducto(42, 30, 'Regulador de zapatas DE FRENO SDE MANO',27.20,34.00,'unidad','Unidades',1.00, '', '',  1, 1, 100, @idproducto);
CALL spRegisterProducto(41, 27, 'Refrigerante VISTONY Antifreeze -25C',16.80,21.00,'botella','Litros',   1.00, '', '',  1, 1, 100, @idproducto);
CALL spRegisterProducto(36, 34, 'Poncho de palier TOTO flexible',     13.20,16.50,'unidad','Unidades',   1.00, '', '',  1, 1, 100, @idproducto);
CALL spRegisterProducto(22, 45, 'Jebe para puerta JBG JP-200',        4.72, 5.90,'unidad','Unidades',    1.00, '', '',  1, 1, 100, @idproducto);
CALL spRegisterProducto(34, 15, 'Orring 3PK anillo sellador',         2.56, 3.20,'unidad','Unidades',    1.00, '', '',  1, 1, 100, @idproducto);
CALL spRegisterProducto(25, 38, 'Juego de pisos KIA de lujo',         56.00,70.00,'juego','Juegos',     1.00, '', '',  1, 1, 100, @idproducto);
CALL spRegisterProducto(32, 13, 'Manguera de agua CHERY MA-100',        7.92, 9.90,'unidad','Unidades',   1.00, '', '',  1, 1, 100, @idproducto);
CALL spRegisterProducto(30, 10, 'Luna de espejo AUDI lado izquierdo',  31.92,39.90,'unidad','Unidades',   1.00, '', '',  1, 1, 100, @idproducto);
CALL spRegisterProducto(8,  44, 'Bobina de encendido RKD BK-11',      60.00,75.00,'unidad','Unidades',   1.00, '', '',  1, 1, 100, @idproducto);
CALL spRegisterProducto(28, 29, 'Líquido de freno DE FRENO S/M 0.5L',  8.00,10.00,'botella','Litros',     1.00, '', '',  1, 1, 100, @idproducto);
CALL spRegisterProducto(29, 36, 'Llanta GOOD YEARS EfficientGrip 195/65 R15',176.00,220.00,'unidad','Unidades',1.00,'','',1,1,100,@idproducto);
CALL spRegisterProducto(23, 30, 'Juego de pastillas MR DELANTEROS para Tucson',68.80,86.00,'juego','Juegos',1.00,'','',1,1,100,@idproducto);
CALL spRegisterProducto(10, 12, 'Cintillo BRILIANCE ajustable 15cm',    1.20, 1.50,'unidad','Unidades',    1.00, '', '',  1, 1, 100, @idproducto);
CALL spRegisterProducto(27, 39, 'Kit de accesorios ALMEYDA motor completo',96.00,120.00,'juego','Juegos',1.00,'','',1,1,100,@idproducto);
CALL spRegisterProducto(29, 35, 'Llanta GOOD YEARS Wrangler SUV',      272.00,340.00,'unidad','Unidades', 1.00, '', '',  1, 1, 100, @idproducto);
CALL spRegisterProducto(38,  5, 'Radiador CHEVROLET Aveo 1.6L',       148.00,185.00,'unidad','Unidades', 1.00, '', '',  1, 1, 100, @idproducto);
CALL spRegisterProducto(18, 21, 'Filtro de gasolina WANDER WF-56',    12.80,16.00,'unidad','Unidades',    1.00, '', '',  1, 1, 100, @idproducto);
CALL spRegisterProducto(49,  3, 'Triceta FORD Fiesta 22 estrías',     92.00,115.00,'unidad','Unidades',    1.00, '', '',  1, 1, 100, @idproducto);
CALL spRegisterProducto(50, 11, 'Válvula PCV Valve BAIC modelo universal',14.40,18.00,'unidad','Unidades',1.00,'','',1,1,100,@idproducto);
CALL spRegisterProducto(47,  7, 'Suplex BMW delantero izquierdo',     116.00,145.00,'unidad','Unidades',   1.00, '', '',  1, 1, 100, @idproducto);
CALL spRegisterProducto(48,  4, 'Templador HYUNDAI I10 motor 1.1L',    47.92,59.90,'unidad','Unidades',     1.00, '', '',  1, 1, 100, @idproducto);
CALL spRegisterProducto(46,  9, 'Soporte de caja BYD F3 izquierdo',    31.20,39.00,'unidad','Unidades',     1.00, '', '',  1, 1, 100, @idproducto);
CALL spRegisterProducto(11,  6, 'Disco de freno NISSAN Sentra B13',    71.92,89.90,'unidad','Unidades',     1.00, '', '',  1, 1, 100, @idproducto);

 INSERT INTO servicios (idsubcategoria, servicio) VALUES
 (4, 'Inspección general del vehículo'),
 (4, 'Revisión pre-ITV'),
 (4, 'Lavado y detallado de auto'),
 (4, 'Instalación de accesorios'),
 (4, 'Diagnóstico computarizado');
 
 -- Subcategoría 49: Lubricación
 INSERT INTO servicios (idsubcategoria, servicio) VALUES
 (3, 'Cambio de aceite de motor'),
 (3, 'Cambio de filtro de aceite'),
 (3, 'Engrase de suspensión y dirección'),
 (3, 'Cambio de aceite de caja automática'),
 (3, 'Cambio de aceite de diferencial');
 
 -- Subcategoría 48: Mecánica general
 INSERT INTO servicios (idsubcategoria, servicio) VALUES
 (2, 'Reparación de motor'),
 (2, 'Cambio de correa de distribución'),
 (2, 'Cambio de bujías'),
 (2, 'Ajuste de válvulas'),
 (2, 'Reparación de sistema de escape');
 
 -- Subcategoría 47: Dirección y suspensión
 INSERT INTO servicios (idsubcategoria, servicio) VALUES
 (1, 'Alineación y balanceo'),
 (1, 'Cambio de amortiguadores'),
 (1, 'Revisión de rótulas y terminales'),
 (1, 'Cambio de brazos de suspensión'),
 (1, 'Revisión y reparación de dirección hidráulica');

  INSERT INTO tipocombustibles (tcombustible) VALUES
  ('Gasolina'),
  ('Diesel'),
  ('GNV'),
  ('GLP'),
  ('Biodiesel'),
  ('Etanol'),
  ('Allinol'),
  ('Electricidad'),
  ('Hidrogeno'),
  ('Biocombustible');
  
  INSERT INTO personas (nombres, apellidos, tipodoc, numdoc, numruc, direccion, correo, telprincipal, telalternativo) VALUES
('Carlos', 'Ramírez', 'DNI', '70849320', '20123456789', 'Av. Los Pinos 123', 'carlos.ramirez@mail.com', '987654321', NULL),
('Lucía', 'Fernández', 'DNI', '80920315', NULL, 'Jr. Lima 456', NULL, '912345678', '900123456');



INSERT INTO Contratos ( idpersona, idrol, fechainicio, fechafin) VALUES
 (2, 3, '2023-01-01', NULL),
 (3, 3, '2023-06-15', NULL);
 
  INSERT INTO Colaboradores (idcontrato, namuser, passuser) VALUES
 (2, 'cmendoza', 'pass123'),
 (3, 'lramirez', 'pass123');
 
 -- RUC: 20608506986
 
