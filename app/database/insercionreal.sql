USE dbfix360;

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
(1,'ABRAZADERAS'),
(2,'ACEITE DE MOTOR'),
(3,'ARANDELA'),
(4,'BOBINA DE ENCENDIDO'),
(5,'BUJIAS'),
(6,'CINTILLOS'),
(7,'DISCOS'),
(8,'EMPAQUE'),
(9,'FAJA'),
(10,'FILTRO DE ACEITE'),
(10,'FILTRO DE AIRE'),
(10,'FILTRO DE AIRE ACONDICIONADO'),
(10,'FILTRO DE CABINA'),
(10,'FILTRO DE GASOLINA'),
(10,'FILTRO DE PETROLEO'),
(11,'FOCOS LED'),
(12,'GRASA'),
(13,'JEBES'),
(14,'JUEGO DE PASTILLAS'),
(14,'JUEGO DE ZAPATAS'),
(14,'JUEGO DE PISOS'),
(15,'KIT DE EMPAQUE DE MOTOR'),
(15,'KIT DE ACCESORIOS'),
(16,'LIQUIDO DE FRENO'),
(17,'LLANTA PARA AUTOS'),
(18,'LUNA DE ESPEJO IZQUIERDO'),
(19,'MANGUERA DE BRIDA'),
(19,'MANGUERA DE AGUA'),
(19,'MANGUERA DE RADIADOR DE REVOSE'),
(20,'ORRING'),
(21,'PEGAMENTO'),
(22,'PONCHO'),
(22,'PONCHO DE PALIER'),
(23,'RADIADOR'),
(24,'RECTIFICADORA'),
(25,'REFRIGERANTE PRE MEZCLADO'),
(25,'REFRIGERANTE ANTIFREEZE'),
(26,'REGULADOR DE ZAPATAS'),
(27,'RESORTES'),
(28,'RETEN'),
(29,'SILICONA'),
(30,'SOPORTE DE CAJA'),
(31,'SUPLEX'),
(32,'TEMPLADOR'),
(33,'TRICETA'),
(34,'VALVULA PCV VALVE');

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

INSERT INTO productos (idmarca, idsubcategoria, descripcion, precio, presentacion, undmedida, cantidad, img) VALUES
(16, 2, 'Aceite multigrado SHELL 20W50', 48.50, 'botella', 'Litros', 4.00, 'img/aceite_shell.jpg'),
(17, 2, 'Aceite sintético CHEVRON 10W30', 55.00, 'botella', 'Litros', 4.00, 'img/aceite_chevron.jpg'),
(18, 2, 'Aceite mineral CASTROL GTX 20W50', 42.90, 'botella', 'Litros', 4.00, 'img/aceite_castrol.jpg'),
(19, 2, 'Aceite MOTUL 300V Competition', 79.90, 'botella', 'Litros', 2.00, 'img/aceite_motul.jpg'),
(20, 2, 'Aceite WILLIAMS Premium 15W40', 35.00, 'botella', 'Litros', 4.00, 'img/aceite_williams.jpg'),
(21, 2, 'Aceite REPSOL Elite 5W40', 65.00, 'botella', 'Litros', 4.00, 'img/aceite_repsol.jpg'),
(22, 2, 'Aceite BARDHAL B1 Plus', 33.00, 'botella', 'Litros', 4.00, 'img/aceite_bardhal.jpg'),
(26, 25, 'Refrigerante VISTONY Premix -37C', 22.50, 'botella', 'Litros', 1.00, 'img/refrigerante_vistony.jpg'),
(42, 25, 'Refrigerante PRESTONE Antifreeze', 25.90, 'botella', 'Litros', 1.00, 'img/refrigerante_prestone.jpg'),
(24, 5, 'Bujía NGK Iridium IX', 18.90, 'caja', 'Unidades', 4.00, 'img/bujia_ngk.jpg'),
(25, 5, 'Bujía BOSCH Platinum', 19.90, 'caja', 'Unidades', 4.00, 'img/bujia_bosch.jpg'),
(31, 14, 'Juego de zapatas MR POSTERIORES', 75.00, 'juego', 'Juegos', 1.00, 'img/zapatas_mr.jpg'),
(32, 14, 'Juego de pastillas MR DELANTEROS', 82.00, 'juego', 'Juegos', 1.00, 'img/pastillas_mr.jpg'),
(28, 16, 'Líquido de freno NIBK DOT 4', 14.00, 'botella', 'Litros', 0.50, 'img/freno_nibk.jpg'),
(41, 16, 'Líquido de freno PRESTONE DOT 3', 11.50, 'botella', 'Litros', 0.50, 'img/freno_prestone.jpg'),
(35, 17, 'Llanta GOOD YEARS Eagle 185/60 R15', 215.00, 'unidad', 'Unidades', 1.00, 'img/llanta_goodyears.jpg'),
(23, 10, 'Filtro de aceite AIR FILTER AF-01', 13.90, 'unidad', 'Unidades', 1.00, 'img/filtro_aire_af01.jpg'),
(20, 10, 'Filtro de gasolina OIL FILTER GF-3', 16.90, 'unidad', 'Unidades', 1.00, 'img/filtro_gasolina_gf3.jpg'),
(17, 9, 'Faja de distribución CHEVRON FD-100', 32.00, 'unidad', 'Unidades', 1.00, 'img/faja_chevron.jpg'),
(40, 12, 'Grasa MEGA GREY para rodamientos', 9.50, 'pote', 'Gramos', 250.00, 'img/grasa_mega.jpg'),
(43, 28, 'Retén JBG trasero motor 45mm', 12.90, 'unidad', 'Unidades', 1.00, 'img/reten_jbg.jpg'),
(39, 14, 'Juego de pisos ALMEYDA universal', 50.00, 'juego', 'Juegos', 1.00, 'img/pisos_almeyda.jpg'),
(33, 22, 'Poncho de palier AUTOLAND reforzado', 18.00, 'unidad', 'Unidades', 1.00, 'img/poncho_autoland.jpg'),
(44, 8, 'Empaque de culata RKD EC-09', 22.00, 'unidad', 'Unidades', 1.00, 'img/empaque_rkd.jpg'),
(30, 26, 'Regulador de zapatas DE FRENO SDE MANO', 34.00, 'unidad', 'Unidades', 1.00, 'img/regulador_freno.jpg'),
(27, 25, 'Refrigerante VISTONY Antifreeze -25C', 21.00, 'botella', 'Litros', 1.00, 'img/refrigerante_antifreeze.jpg'),
(34, 22, 'Poncho de palier TOTO flexible', 16.50, 'unidad', 'Unidades', 1.00, 'img/poncho_toto.jpg'),
(45, 13, 'Jebe para puerta JBG JP-200', 5.90, 'unidad', 'Unidades', 1.00, 'img/jebe_jbg.jpg'),
(15, 20, 'Orring 3PK anillo sellador', 3.20, 'unidad', 'Unidades', 1.00, 'img/orring_3pk.jpg'),
(38, 14, 'Juego de pisos KIA de lujo', 70.00, 'juego', 'Juegos', 1.00, 'img/pisos_kia.jpg'),
(13, 19, 'Manguera de agua CHERY MA-100', 9.90, 'unidad', 'Unidades', 1.00, 'img/manguera_chery.jpg'),
(10, 18, 'Luna de espejo AUDI lado izquierdo', 39.90, 'unidad', 'Unidades', 1.00, 'img/luna_audi.jpg'),
(44, 4, 'Bobina de encendido RKD BK-11', 75.00, 'unidad', 'Unidades', 1.00, 'img/bobina_rkd.jpg'),
(29, 16, 'Líquido de freno DE FRENO S/M 0.5L', 10.00, 'botella', 'Litros', 0.50, 'img/freno_sm.jpg'),
(36, 17, 'Llanta GOOD YEARS EfficientGrip 195/65 R15', 220.00, 'unidad', 'Unidades', 1.00, 'img/llanta_gy2.jpg'),
(22, 10, 'Filtro de cabina KUSA K-99', 15.90, 'unidad', 'Unidades', 1.00, 'img/filtro_kusa.jpg'),
(18, 10, 'Filtro de aire BLS AirMax', 14.50, 'unidad', 'Unidades', 1.00, 'img/filtro_bls.jpg'),
(37, 10, 'Filtro de petróleo MOBIS MP-202', 18.80, 'unidad', 'Unidades', 1.00, 'img/filtro_mobis.jpg'),
(30, 14, 'Juego de pastillas MR DELANTEROS para Tucson', 86.00, 'juego', 'Juegos', 1.00, 'img/pastillas_mr2.jpg'),
(12, 6, 'Cintillo BRILIANCE ajustable 15cm', 1.50, 'unidad', 'Unidades', 1.00, 'img/cintillo_brilliance.jpg'),
(39, 15, 'Kit de accesorios ALMEYDA motor completo', 120.00, 'juego', 'Juegos', 1.00, 'img/kit_almeyda.jpg'),
(35, 17, 'Llanta GOOD YEARS Wrangler SUV', 340.00, 'unidad', 'Unidades', 1.00, 'img/llanta_gy3.jpg'),
(5, 23, 'Radiador CHEVROLET Aveo 1.6L', 185.00, 'unidad', 'Unidades', 1.00, 'img/radiador_aveo.jpg'),
(21, 10, 'Filtro de gasolina WANDER WF-56', 16.00, 'unidad', 'Unidades', 1.00, 'img/filtro_wander.jpg'),
(3, 33, 'Triceta FORD Fiesta 22 estrías', 115.00, 'unidad', 'Unidades', 1.00, 'img/triceta_ford.jpg'),
(11, 34, 'Válvula PCV Valve BAIC modelo universal', 18.00, 'unidad', 'Unidades', 1.00, 'img/valvula_baic.jpg'),
(7, 31, 'Suplex BMW delantero izquierdo', 145.00, 'unidad', 'Unidades', 1.00, 'img/suplex_bmw.jpg'),
(4, 32, 'Templador HYUNDAI I10 motor 1.1L', 59.90, 'unidad', 'Unidades', 1.00, 'img/templador_i10.jpg'),
(9, 30, 'Soporte de caja BYD F3 izquierdo', 39.00, 'unidad', 'Unidades', 1.00, 'img/soporte_byd.jpg'),
(6, 7, 'Disco de freno NISSAN Sentra B13', 89.90, 'unidad', 'Unidades', 1.00, 'img/disco_nissan.jpg');

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

-- select * from tipovehiculos;
-- select * from modelos;
-- select * from vehiculos;

-- este insert into hace todas las combinaciones posibles de tipo de vehiculo y marca

INSERT INTO modelos (idtipov, idmarca, modelo)
SELECT 
    tv.idtipov, 
    m.idmarca, 
    CONCAT(m.nombre, ' ', tv.tipov) AS modelo
FROM marcas m
CROSS JOIN tipovehiculos tv;

INSERT INTO contactabilidad (contactabilidad)
VALUES
  ('Redes sociales'),
  ('Folletos'),
  ('Campaña publicitaria'),
  ('Recomendacion');
  
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
    
INSERT INTO vehiculos (idmodelo,placa,anio,numserie,color,idtcombustible)
VALUES
(1,'98654','2020','asda5s46d54as6d5','rojo',1),
(2,'321qwe','2021','a65das4d65a4','azul',2),
(3,'fgh987','2022','987vas98das7','blanco',3),
(4,'321vbn','2023','asvda65c4wq','negro',4),
(5,'64n87a','2024','sacd4a65c14','gris',5),
(6,'s1lt6r','2025','8465c32a132za','amarillo',6),
(7,'aw4bq4','2018','as9c3x21a3z','verde',7);

-- select * from productos;

INSERT INTO personas (nombres, apellidos, tipodoc, numdoc, numruc, direccion, correo, telprincipal, telalternativo) VALUES
('Carlos', 'Ramírez', 'DNI', '75849320', '20123456789', 'Av. Los Pinos 123', 'carlos.ramirez@mail.com', '987654321', NULL),
('Lucía', 'Fernández', 'DNI', '84920315', NULL, 'Jr. Lima 456', NULL, '912345678', '900123456'),
('Juan', 'Gómez', 'Pasaporte', 'PA1234567', '20654321876', NULL, 'juan.gomez@mail.com', NULL, '989898989'),
('María', 'Quispe', 'Carnet Extranjería', 'CE998877', NULL, NULL, NULL, '955667788', NULL),
('Pedro', 'Vargas', 'DNI', '78541236', NULL, 'Av. Bolívar 678', 'pedro.vargas@mail.com', '901112233', '922334455'),
('Ana', 'Torres', 'DNI', '80321459', '20987654321', NULL, 'ana.torres@mail.com', NULL, NULL),
('Jorge', 'Lopez', 'DNI', '71239845', NULL, NULL, NULL, NULL, NULL),
('Carmen', 'Rojas', 'DNI', '75488933', '20765432109', 'Calle 13 #56', 'carmen.rojas@mail.com', '998877665', '988776655'),
('Luis', 'Huamán', 'DNI', '70123456', NULL, 'Av. Grau 999', NULL, '934567890', NULL),
('Elena', 'Salas', 'Pasaporte', 'PA7654321', '20876543210', NULL, NULL, NULL, NULL);

INSERT INTO empresas (nomcomercial,razonsocial,telefono,correo,ruc) VALUES
('Tech SAC','SAC','945612387','tech@gmail,com','20154632145'),
('Empresa IRL','IRL','945665432','irl@irl.com','20321654978'),
('Empresa SA','SA','998765446','sa@sa.com','20321321460'),
('Nombre SAC','SAC','965465413','nombre@sac.com','20301321201'),
('AyR SA','SA','956633983','ayr@ayr.com','20111111113'),
('Exit IRL','IRL','998765420','exit@no.com','20333333331');

INSERT INTO clientes (idpersona,idcontactabilidad) VALUES
(1,1),
(2,2),
(3,3),
(4,4),
(5,1);

INSERT INTO clientes (idempresa,idcontactabilidad) VALUES
(1,1),
(2,2),
(3,3),
(4,4),
(5,1);

INSERT INTO propietarios (idcliente, idvehiculo)
VALUES
(1,1),
(2,2),
(3,3),
(4,4),
(5,5),
(6,6),
(7,7);

INSERT INTO categorias (categoria)
 VALUES ('servicio');
 
 INSERT INTO subcategorias (idcategoria,subcategoria)
 VALUES (35,'Direccion y suspencion'),
  (35,'Mecanica general'),
  (35,'Lubricacion'),
 (35,'Otros servicios');
 -- 50 : otros servicios
 -- ,49: Lubricacion
 -- ,48: Mecanica general
 -- ,47: Direccion y suspencion
 -- Subcategoría 50: Otros servicios
 
 
 INSERT INTO servicios (idsubcategoria, servicio) VALUES
 (50, 'Inspección general del vehículo'),
 (50, 'Revisión pre-ITV'),
 (50, 'Lavado y detallado de auto'),
 (50, 'Instalación de accesorios'),
 (50, 'Diagnóstico computarizado');
 
 -- Subcategoría 49: Lubricación
 INSERT INTO servicios (idsubcategoria, servicio) VALUES
 (49, 'Cambio de aceite de motor'),
 (49, 'Cambio de filtro de aceite'),
 (49, 'Engrase de suspensión y dirección'),
 (49, 'Cambio de aceite de caja automática'),
 (49, 'Cambio de aceite de diferencial');
 
 -- Subcategoría 48: Mecánica general
 INSERT INTO servicios (idsubcategoria, servicio) VALUES
 (48, 'Reparación de motor'),
 (48, 'Cambio de correa de distribución'),
 (48, 'Cambio de bujías'),
 (48, 'Ajuste de válvulas'),
 (48, 'Reparación de sistema de escape');
 
 -- Subcategoría 47: Dirección y suspensión
 INSERT INTO servicios (idsubcategoria, servicio) VALUES
 (47, 'Alineación y balanceo'),
 (47, 'Cambio de amortiguadores'),
 (47, 'Revisión de rótulas y terminales'),
 (47, 'Cambio de brazos de suspensión'),
 (47, 'Revisión y reparación de dirección hidráulica');
 
 -- Insertar personas
 INSERT INTO Personas (nombres, apellidos, tipodoc, numdoc, direccion, correo, telprincipal, telalternativo) VALUES
 ('Carlos', 'Mendoza', 'DNI', '12345678', 'Av. Siempre Viva 123', 'carlos.m@example.com', '987654321', '912345678'),
 ('Lucía', 'Ramírez', 'DNI', '87654321', 'Calle Falsa 456', 'lucia.r@example.com', '976543210', '998877665'),
 ('Jorge', 'Pérez', 'DNI', '11223344', 'Jr. Los Olivos 789', 'jorge.p@example.com', '965432198', '987123456');
 
-- select * from personas order by idpersona desc;
 
 INSERT INTO roles (rol) VALUES ('mecanico');
 
 -- Insertar contratos con rol de mecánico (idrol = 2, por ejemplo)
 INSERT INTO Contratos ( idpersona, idrol, fechainicio, fechafin) VALUES
 (1, 1, '2023-01-01', '2924-02-01'),
 (2, 1, '2023-06-15', '2924-02-01'),
 (3, 1, '2024-02-01', '2924-02-01');
 
 -- select * from personas;
 -- select * from contratos;
 -- Insertar colaboradores
 INSERT INTO Colaboradores (idcontrato, namuser, passuser) VALUES
 (1, 'cmendoza', 'pass123'),
 (2, 'lramirez', 'pass123'),
 (3, 'jperez', 'pass123');
 
-- select * from colaboradores;

INSERT INTO componentes (componente) VALUES
('Bloque del motor'),
('Pistones y bielas'),
('Árbol de levas'),
('Culata'),
('Válvulas'),
('Sistema de lubricación'),
('Caja de cambios'),
('Embrague'),
('convertidor de par'),
('Eje de transmisión'),
('Diferencial'),
('Tanque de gasolina'),
('sistema de inyección'),
('Inyectores'),
('Bomba de combustible'),
('Filtro de combustible'),
('Bomba de agua'),
('Termostato'),
('Ventilador'),
('Colector de escape'),
('Catalizador'),
('Silenciador'),
('Tubo de escape'),
('Amortiguadores'),
('resortes'),
('Barra estabilizadora'),
('Sistema de dirección'),
('pastillas de freno'),
('Discos de freno'),
('Tambor'),
('Sistema hidráulico'),
('Batería'),
('Alternador'),
('Sistema de encendido'),
('Sensores y centralita'),
('Iluminación y tablero de instrumentos'),
('Chasis'),
('Carrocería'),
('Sistema de cierre y seguridad'),
('Radiador');

INSERT INTO formapagos (formapago) VALUES 
('Yape'),
('Plin'),
('Efectivo'),
('Deposito');
-- select * from agendas;

INSERT INTO agendas (idpropietario,fchproxvisita,comentario,estado) VALUES
(5,NOW(),'cambio de aceite','P'),
(4,NOW(),'cambio de llanta','P'),
(2,NOW(),'nivelacion','P'),
(4,NOW(),'lavado','P'),
(5,NOW(),'ajuste de bujias','P');