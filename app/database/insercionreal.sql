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
('KIA', 'VEHÍCULOS'),
('ADEX', 'OTROS'),
('ALMEYDA', 'OTROS'),
('PRESTONE', 'LÍQUIDOS'),
('MEGA GREY', 'OTROS'),
('GMB', 'OTROS'),
('RKD', 'OTROS'),
('JBG', 'OTROS');

INSERT INTO categorias (nombre) VALUES
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

INSERT INTO subcategorias (idcategoria, nombre) VALUES
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
  
  
INSERT INTO vehiculos (idmodelo,placa,anio,numserie,color,tipocombustible)
VALUES
(1,'98654','2020','asda5s46d54as6d5','rojo','allinol'),
(2,'321qwe','2021','a65das4d65a4','azul','GNV'),
(3,'fgh987','2022','987vas98das7','blanco','GLP'),
(4,'321vbn','2023','asvda65c4wq','negro','Gas'),
(5,'64n87a','2024','sacd4a65c14','gris','GNV'),
(6,'s1lt6r','2025','8465c32a132za','amarillo','GNV'),
(7,'aw4bq4','2018','as9c3x21a3z','verde','GLP');

-- select * from propietarios;

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

