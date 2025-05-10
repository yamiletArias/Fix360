USE dbfix360;

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

-- select * from componentes;
-- select * from observaciones;
-- Observaciones para idorden = 27
INSERT INTO observaciones (idcomponente, idorden, estado, foto) VALUES
  (1, 27, TRUE,  NULL),
  (2, 27, TRUE,  NULL),
  (3, 27, TRUE,  NULL),
  (4, 27, TRUE,  NULL);

-- Observaciones para idorden = 28
INSERT INTO observaciones (idcomponente, idorden, estado, foto) VALUES
  (1, 28, TRUE,  NULL),
  (2, 28, TRUE,  NULL),
  (3, 28, TRUE,  NULL),
  (4, 28, TRUE,  NULL);

-- Observaciones para idorden = 29
INSERT INTO observaciones (idcomponente, idorden, estado, foto) VALUES
  (1, 29, TRUE,  NULL),
  (2, 29, TRUE,  NULL),
  (3, 29, TRUE,  NULL),
  (4, 29, TRUE,  NULL);

-- Observaciones para idorden = 30
INSERT INTO observaciones (idcomponente, idorden, estado, foto) VALUES
  (1, 30, TRUE,  NULL),
  (2, 30, TRUE,  NULL),
  (3, 30, TRUE,  NULL),
  (4, 30, TRUE,  NULL);

  
