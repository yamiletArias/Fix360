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
  
  
INSERT INTO vehiculos (idmodelo, placa, anio, numserie, color, tipocombustible) VALUES
(1, 'ABC1234', '2018', 'SER12345', 'Rojo', 'Gasolina'),
(2, 'DEF5678', '2020', 'SER23456', 'Negro', 'Gasolina'),
(3, 'GHI9012', '2019', NULL, 'Blanco', 'Diésel'),
(4, 'JKL3456', '2017', 'SER45678', 'Gris', 'Gasolina'),
(5, 'MNO7890', '2021', NULL, 'Azul', 'Eléctrico'),
(1, 'PQR1234', '2016', 'SER56789', 'Verde', 'Gasolina'),
(2, 'STU5678', '2022', NULL, 'Amarillo', 'Gas'),
(3, 'VWX9012', '2015', 'SER67890', 'Plomo', 'Gasolina'),
(4, 'YZA3456', '2023', NULL, 'Negro', 'Eléctrico'),
(5, 'BCD7890', '2024', 'SER78901', 'Rojo', 'Híbrido');


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

INSERT INTO empresas (nomcomercial, razonsocial, telefono, correo, ruc) VALUES
('Tech Solutions', 'Tech Solutions S.A.C.', '987654321', 'contacto@techsolutions.com', '20111111111'),
('InnovaSoft', 'InnovaSoft EIRL', '912345678', 'ventas@innovasoft.pe', '20222222222'),
('Green Corp', 'Green Corporation S.A.', '901234567', 'info@greencorp.com', '20333333333'),
('Constructiva', 'Constructiva S.A.C.', '945612378', 'contacto@constructiva.pe', '20444444444'),
('ModaFlex', 'ModaFlex EIRL', '923456789', 'modaflex@tienda.com', '20555555555'),
('BioLife', 'BioLife Perú S.A.C.', '987123456', 'biolife@salud.pe', '20666666666'),
('ServiRed', 'Servicios Integrales Red S.A.C.', '999888777', 'soporte@servired.com', '20777777777'),
('Educativa360', 'Educativa360 S.A.C.', '934567890', 'admin@educativa360.com', '20888888888'),
('AutoPerú', 'AutoPerú Automotriz S.A.', '922334455', 'autoperu@vehiculos.com', '20999999999'),
('ViajesExpress', 'Viajes Express S.R.L.', '955667788', 'reservas@viajesexpress.pe', '20100000001');


INSERT INTO propietarios (idcliente, idvehiculo)
VALUES
(1,1),
(2,2),
(1,3),
(2,4),
(1,5),
(2,6),
(1,7);
-- select * from propietarios;

INSERT INTO clientes (idempresa, idpersona, idcontactabilidad) VALUES
(1, NULL, 1),
(2, NULL, 2),
(NULL, 1, 3),
(NULL, 2, 4),
(3, NULL, 1),
(NULL, 3, 2),
(4, NULL, 3),
(NULL, 4, 4),
(5, NULL, 1),
(NULL, 5, 2);

INSERT INTO propietarios (idcliente, idvehiculo, fechainicio, fechafinal) VALUES
(1, 1, '2022-01-10', '2023-02-15'),
(2, 2, '2021-05-20', NULL),
(3, 3, '2023-07-01', NULL),
(4, 4, '2020-11-30', '2022-11-30'),
(5, 5, '2024-03-12', NULL),
(6, 6, '2022-08-09', '2023-09-01'),
(7, 7, '2023-04-25', NULL),
(8, 8, '2021-12-12', '2022-12-12'),
(9, 9, '2023-09-19', NULL),
(10, 10, '2024-01-01', NULL);



