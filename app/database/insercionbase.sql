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
('Maria Elena', 'Castila Hernandez', 'DNI', '75849320', '20123456789', 'Av. Los Pinos 123', 'elenafix360@gmail.com', '987654321', NULL);
*/
CALL spRegisterColaborador('ElenaCastilla','fix3602025',1,CURDATE(),NULL,'Maria Elena', 'Castila Hernandez', 'DNI', '75849320', 'Av. Los Pinos 123', 'elenafix360@gmail.com', '987654321');

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


	