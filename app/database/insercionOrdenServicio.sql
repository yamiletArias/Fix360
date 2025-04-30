USE dbfix360;

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE amortizaciones;
TRUNCATE TABLE detallecompra;
TRUNCATE TABLE detalleventa;
TRUNCATE TABLE detallecotizacion;
TRUNCATE TABLE detalleordenservicios;
TRUNCATE TABLE observaciones;
TRUNCATE TABLE agendas;
TRUNCATE TABLE ordenservicios;
TRUNCATE TABLE propietarios;
TRUNCATE TABLE movimientos;
TRUNCATE TABLE kardex;
TRUNCATE TABLE roles;
TRUNCATE TABLE paquetes;
TRUNCATE TABLE productos;
TRUNCATE TABLE cotizaciones;
TRUNCATE TABLE compras;
TRUNCATE TABLE proveedores;
TRUNCATE TABLE servicios;
TRUNCATE TABLE subcategorias;
TRUNCATE TABLE categorias;
TRUNCATE TABLE condiciones;
TRUNCATE TABLE promociones;
TRUNCATE TABLE marcas;
TRUNCATE TABLE componentes;
TRUNCATE TABLE modelos;
TRUNCATE TABLE vehiculos;
TRUNCATE TABLE colaboradores;
TRUNCATE TABLE contratos;
TRUNCATE TABLE clientes;
TRUNCATE TABLE contactabilidad;
TRUNCATE TABLE formapagos;
TRUNCATE TABLE tipocombustibles;
TRUNCATE TABLE tipovehiculos;
TRUNCATE TABLE tipomovimientos;
TRUNCATE TABLE empresas;
TRUNCATE TABLE personas;
SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO roles (rol) VALUES
('Administrador'),
('Mecánico'),
('Recepcionista'),
('Gerente');

INSERT INTO personas (nombres, apellidos, tipodoc, numdoc, numruc, direccion, correo, telprincipal, telalternativo) VALUES
('Juan Carlos', 'Perez Gomez', 'DNI', '76543210', NULL, 'Av. Los Girasoles 123', 'juan.perez@email.com', '987654321', NULL),
('Maria Luisa', 'Ramirez Soto', 'DNI', '12345678', NULL, 'Calle Las Begonias 456', 'maria.ramirez@email.com', '912345678', '998877665'),
('Pedro Antonio', 'Castillo Flores', 'DNI', '87654321', NULL, 'Jr. Los Alamos 789', 'pedro.castillo@email.com', '955555555', NULL),
('Ana Sofia', 'Vargas Mendoza', 'DNI', '11223344', NULL, 'Psj. Las Orquideas 101', 'ana.vargas@email.com', '944444444', NULL);

INSERT INTO contactabilidad (contactabilidad) VALUES
('Alta'),
('Media'),
('Baja'),
('No Contactar');

INSERT INTO tipocombustibles (tcombustible) VALUES
('Gasolina 90'),
('Gasolina 95'),
('Gasolina 97'),
('Diesel'),
('GLP'),
('GNV'),
('Eléctrico'),
('Híbrido');

INSERT INTO tipovehiculos (tipov) VALUES
('Sedan'),
('Hatchback'),
('SUV'),
('Camioneta Pickup'),
('Furgoneta'),
('Motocicleta');

INSERT INTO marcas (nombre, tipo) VALUES
('Toyota', 'Vehículo'),
('Nissan', 'Vehículo'),
('Hyundai', 'Vehículo'),
('Kia', 'Vehículo'),
('Mazda', 'Vehículo'),
('Bosch', 'Componente/Producto'),
('Mobil', 'Lubricante'),
('Michelin', 'Neumático');

INSERT INTO categorias (categoria) VALUES
('Mantenimiento Preventivo'),
('Reparación Mecánica'),
('Sistema Eléctrico'),
('Frenos'),
('Suspensión y Dirección'),
('Neumáticos y Alineamiento'),
('Lubricantes y Fluidos');

INSERT INTO componentes (componente) VALUES
('Motor'),
('Transmisión'),
('Sistema de Frenos'),
('Suspensión Delantera'),
('Suspensión Trasera'),
('Sistema de Escape'),
('Neumáticos'),
('Luces'),
('Carrocería');

INSERT INTO clientes (idpersona, idcontactabilidad) VALUES
(2, 1);

INSERT INTO contratos (idrol, idpersona, fechainicio, fechafin) VALUES
(1, 1, '2024-01-15', NULL),
(2, 3, '2024-02-01', NULL);

INSERT INTO modelos (idtipov, idmarca, modelo) VALUES
(1, 1, 'Corolla'),
(3, 2, 'Qashqai'),
(4, 1, 'Hilux');

INSERT INTO subcategorias (idcategoria, subcategoria) VALUES
(1, 'Cambio de Aceite y Filtro'),
(1, 'Afinamiento Básico'),
(4, 'Cambio de Pastillas de Freno'),
(4, 'Rectificación de Discos');

INSERT INTO colaboradores (idcontrato, namuser, passuser, estado) VALUES
(1, 'jperez', 'hash_de_pass123', TRUE),
(2, 'pcastillo', 'hash_de_pass456', TRUE);

INSERT INTO vehiculos (idmodelo, idtcombustible, placa, anio, numserie, color, vin, numchasis) VALUES
(1, 1, 'ABC-123', '2020', 'SERIE12345COROLLA', 'Rojo Metálico', 'VIN123456789ABCDE', 'CHASIS987654321XYZ');

INSERT INTO servicios (idsubcategoria, servicio) VALUES
(1, 'Cambio de Aceite Sintético 5W-30 y Filtro'),
(3, 'Cambio de Pastillas Delanteras Cerámicas');

INSERT INTO propietarios (idcliente, idvehiculo, fechainicio, fechafinal) VALUES
(1, 1, '2023-05-20', NULL);

INSERT INTO ordenservicios (idadmin, idpropietario, idcliente, idvehiculo, kilometraje, observaciones, ingresogrua, fechaingreso, fechasalida, estado, notificado) VALUES
(1, 1, 1, 1, 45250.75, 'Cliente reporta ruido al frenar. Realizar revisión general de mantenimiento.', FALSE, NOW(), NULL, 'A', FALSE),
(1, 1, 1, 1, 46050.10, 'Mantenimiento preventivo 45k km.', FALSE, '2025-04-01 09:15:00', NULL, 'A', FALSE),
(1, 1, 1, 1, 46120.50, 'Revisión de frenos, ruido leve al frenar.', FALSE, '2025-04-02 10:30:00', NULL, 'A', FALSE),
(1, 1, 1, 1, 46180.00, 'Check engine encendido intermitentemente.', FALSE, '2025-04-03 08:00:00', NULL, 'A', FALSE),
(1, 1, 1, 1, 46255.80, 'Rotación de neumáticos y balanceo.', FALSE, '2025-04-04 14:05:00', NULL, 'A', FALSE),
(1, 1, 1, 1, 46310.20, 'Cambio de aceite y filtro solicitado.', FALSE, '2025-04-05 11:10:00', NULL, 'A', FALSE),
(1, 1, 1, 1, 46390.00, 'Vehículo no arranca, posible problema batería.', TRUE, '2025-04-06 09:45:00', NULL, 'A', FALSE),
(1, 1, 1, 1, 46450.90, 'Mantenimiento general.', FALSE, '2025-04-07 15:00:00', NULL, 'A', FALSE),
(1, 1, 1, 1, 46515.30, 'Revisar nivel de refrigerante, baja rápido.', FALSE, '2025-04-08 10:00:00', NULL, 'A', FALSE),
(1, 1, 1, 1, 46580.00, 'Cambio pastillas de freno traseras.', FALSE, '2025-04-09 11:30:00', NULL, 'A', FALSE),
(1, 1, 1, 1, 46640.70, 'Afinamiento básico.', FALSE, '2025-04-10 09:00:00', NULL, 'A', FALSE),
(1, 1, 1, 1, 46705.10, 'Revisión pre-viaje largo.', FALSE, '2025-04-11 14:50:00', NULL, 'A', FALSE),
(1, 1, 1, 1, 46770.00, 'Ruido en suspensión delantera derecha.', FALSE, '2025-04-12 08:30:00', NULL, 'A', FALSE),
(1, 1, 1, 1, 46830.50, 'Cambio de aceite.', FALSE, '2025-04-13 10:15:00', NULL, 'A', FALSE),
(1, 1, 1, 1, 46899.90, 'Revisión sistema eléctrico, luces bajas no encienden.', FALSE, '2025-04-14 16:00:00', NULL, 'A', FALSE),
(1, 1, 1, 1, 46950.00, 'Mantenimiento 50k km (adelantado).', FALSE, '2025-04-15 09:20:00', NULL, 'A', FALSE),
(1, 1, 1, 1, 47010.20, 'Fuga de aceite leve detectada en revisión anterior.', FALSE, '2025-04-16 11:00:00', NULL, 'A', FALSE),
(1, 1, 1, 1, 47075.00, 'Cambio de pastillas de freno delanteras.', FALSE, '2025-04-17 14:30:00', NULL, 'A', FALSE),
(1, 1, 1, 1, 47130.80, 'Revisión general solicitada.', FALSE, '2025-04-18 08:45:00', NULL, 'A', FALSE),
(1, 1, 1, 1, 47190.10, 'Vibración al alcanzar 80 km/h.', FALSE, '2025-04-19 10:55:00', NULL, 'A', FALSE),
(1, 1, 1, 1, 47245.50, 'Cambio de aceite y revisión de niveles.', FALSE, '2025-04-20 09:05:00', NULL, 'A', FALSE),
(1, 1, 1, 1, 47300.00, 'Aire acondicionado no enfría.', FALSE, '2025-04-21 13:00:00', NULL, 'A', FALSE),
(1, 1, 1, 1, 47365.70, 'Mantenimiento completo.', FALSE, '2025-04-22 08:10:00', NULL, 'A', FALSE),
(1, 1, 1, 1, 47420.00, 'Cambio de neumáticos delanteros.', FALSE, '2025-04-23 10:40:00', NULL, 'A', FALSE),
(1, 1, 1, 1, 47488.30, 'Revisión sistema de escape, suena más fuerte.', FALSE, '2025-04-24 15:25:00', NULL, 'A', FALSE),
(1, 1, 1, 1, 47540.90, 'Cambio de aceite y filtro.', FALSE, '2025-04-25 09:50:00', NULL, 'A', FALSE),
(1, 1, 1, 1, 47600.00, 'Limpieza de inyectores.', FALSE, '2025-04-26 11:20:00', NULL, 'A', FALSE),
(1, 1, 1, 1, 47665.20, 'Revisión de frenos completa.', FALSE, '2025-04-27 08:05:00', NULL, 'A', FALSE),
(1, 1, 1, 1, 47720.00, 'Cambio de plumillas limpiaparabrisas.', FALSE, '2025-04-28 16:30:00', NULL, 'A', FALSE),
(1, 1, 1, 1, 47780.50, 'Mantenimiento antes de venta.', FALSE, '2025-04-29 10:00:00', NULL, 'A', FALSE),
(1, 1, 1, 1, 47835.00, 'Preparación para viaje largo, revisión fluidos.', FALSE, '2025-04-30 14:00:00', NULL, 'A', FALSE);

INSERT INTO detalleordenservicios (idorden, idservicio, idmecanico, precio, estado) VALUES
(1, 1, 2, 180.50, 'A'),
(1, 2, 2, 250.00, 'A'),
(2, 1, 2, 185.00, 'A'),
(3, 2, 2, 255.00, 'A'),
(4, 1, 2, 188.00, 'A'),
(5, 1, 2, 50.00, 'A'),
(6, 1, 2, 185.50, 'A'),
(7, 1, 2, 60.00, 'A'),
(8, 1, 2, 186.00, 'A'),
(8, 2, 2, 258.00, 'A'),
(9, 1, 2, 45.00, 'A'),
(10, 2, 2, 210.00, 'A'),
(11, 1, 2, 350.00, 'A'),
(12, 1, 2, 187.00, 'A'),
(12, 2, 2, 100.00, 'A'),
(13, 1, 2, 70.00, 'A'),
(14, 1, 2, 187.50, 'A'),
(15, 1, 2, 80.00, 'A'),
(16, 1, 2, 188.00, 'A'),
(16, 2, 2, 260.00, 'A'),
(17, 1, 2, 65.00, 'A'),
(18, 2, 2, 259.00, 'A'),
(19, 1, 2, 188.50, 'A'),
(20, 1, 2, 90.00, 'A'),
(21, 1, 2, 189.00, 'A'),
(22, 1, 2, 120.00, 'A'),
(23, 1, 2, 189.50, 'A'),
(23, 2, 2, 262.00, 'A'),
(24, 1, 2, 100.00, 'A'),
(25, 1, 2, 55.00, 'A'),
(26, 1, 2, 190.00, 'A'),
(27, 1, 2, 250.00, 'A'),
(28, 2, 2, 265.00, 'A'),
(29, 1, 2, 30.00, 'A'),
(30, 1, 2, 191.00, 'A'),
(30, 2, 2, 268.00, 'A'),
(31, 1, 2, 191.50, 'A');