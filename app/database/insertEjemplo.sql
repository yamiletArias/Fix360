-- INSERT para la tabla 'personas'
INSERT INTO personas (nombres, apellidos, tipodoc, numdoc, direccion, correo, telprincipal, telalternativo)
VALUES 
('Juan', 'Pérez', 'DNI', '12345678', 'Av. Las Flores 123', 'juan.perez@email.com', '987654321', '998877665'),
('María', 'Gómez', 'DNI', '87654321', 'Calle Sol 456', 'maria.gomez@email.com', '912345678', '977665544');
INSERT INTO personas (nombres, apellidos, tipodoc, numdoc, direccion, correo, telprincipal, telalternativo)
VALUES 
('Deyanira', 'Arias', 'DNI', '91962913', 'Av. San Martin', 'josue.07.jadi@email.com', '999803156', null);
INSERT INTO personas (nombres, apellidos, tipodoc, numdoc, direccion, correo, telprincipal, telalternativo)
VALUES 
('Josue', 'Prieto', 'DNI', '52398748', 'Av. San Martin', 'deya.08.yami@email.com', '919629135', null);

-- INSERT para la tabla 'empresas'
INSERT INTO empresas (nomcomercial, razonsocial, telefono, correo, ruc)
VALUES 
('Autopartes S.A.', 'Autopartes Sociedad Anónima', '987654321', 'contacto@autopartes.com', '20123456789'),
('Repuestos Vehiculares S.A.C.', 'Repuestos Vehiculares Sociedad Comercial', '936541234', 'ventas@repuestosveh.com', '20123456780');

-- INSERT para la tabla 'roles'
INSERT INTO roles (rol)
VALUES ('Administrador'), ('Mecánico');

-- INSERT para la tabla 'contactabilidad'
INSERT INTO contactabilidad (contactabilidad)
VALUES ('Teléfono'), ('Email');

-- INSERT para la tabla 'formapagos'
INSERT INTO formapagos (formapago)
VALUES ('Efectivo'), ('Transferencia Bancaria');

-- INSERT para la tabla 'tipovehiculos'
INSERT INTO tipovehiculos (tipov)
VALUES ('Sedán'), ('Camioneta');

-- INSERT para la tabla 'componentes'
INSERT INTO componentes (componente)
VALUES ('Motor'), ('Transmisión');

-- INSERT para la tabla 'marcas'
INSERT INTO marcas (nombre, tipo)
VALUES 
('Toyota', 'Automóvil'),
('Ford', 'Camioneta');

-- INSERT para la tabla 'promociones'
INSERT INTO promociones (promocion, fechainicio, fechafin, cantidadmax)
VALUES 
('Descuento 10%', '2025-01-01 00:00:00', '2025-01-31 23:59:59', 100),
('Repuestos Gratis', '2025-02-01 00:00:00', '2025-02-28 23:59:59', 50);

-- INSERT para la tabla 'condiciones'
INSERT INTO condiciones (idpromocion, descripcion)
VALUES 
(1, 'Descuento aplicable en compras superiores a $100'),
(2, 'Promoción válida solo para productos seleccionados');

-- INSERT para la tabla 'tipomovimientos'
INSERT INTO tipomovimientos (flujo, tipomov)
VALUES 
('entrada', 'Compra'),
('salida', 'Venta');

-- INSERT para la tabla 'categorias'
INSERT INTO categorias (categoria)
VALUES 
('Motor'),
('Transmisión'),
('Accesorios'),
('Herramientas'),
('Piezas de motor'),
('Suspensión');

-- INSERT para la tabla 'subcategorias'
INSERT INTO subcategorias (idcategoria, subcategoria)
VALUES 
(1, 'Motores de 4 cilindros'), 
(2, 'Transmisiones automáticas'),
(1, 'Accesorios para interiores'),
(1, 'Accesorios para exteriores'),
(2, 'Herramientas eléctricas'),
(2, 'Herramientas manuales'),
(3, 'Piezas de motor para sedanes'),
(3, 'Piezas de motor para camionetas'),
(4, 'Componentes de suspensión trasera'),
(4, 'Componentes de suspensión delantera');


-- COMPRA
INSERT INTO proveedores (idempresa)
VALUES 
(1),
(2);  
INSERT INTO compras (idproveedor, idcolaborador, fechacompra, tipocom, numserie, numcom, moneda)
VALUES 
(1, 1, '2025-03-10', 'boleta', 'B001', '0001', 'Soles'),
(2, 2, '2025-03-11', 'factura', 'F001', '0002', 'Dólares');

-- INSERT para la tabla 'detallecompra' con 'preciocompra' para todos los productos
INSERT INTO detallecompra (idcompra, idproducto, cantidad, preciocompra, descuento)
VALUES 
(1, 1, 5, 50.00, 5.00),  -- Filtro de aire para motor de 4 cilindros
(1, 2, 3, 120.00, 10.00), -- Bujías para motor Toyota Corolla
(2, 3, 2, 200.00, 0.00),  -- Amortiguador trasero para Ford Ranger
(2, 5, 4, 40.00, 2.00),   -- Aceite para motor 5W-30
(1, 6, 3, 150.00, 15.00), -- Pastillas de freno para vehículos de carga
(1, 7, 5, 300.00, 25.00), -- Transmisión automática para Ford Ranger
(2, 8, 4, 250.00, 20.00), -- Freno de disco delantero para Toyota Corolla
(2, 9, 2, 150.00, 10.00), -- Filtro de aceite para motor de sedán
(1, 10, 6, 350.00, 30.00);

-- INSERT para la tabla 'clientes'
INSERT INTO clientes (idempresa, idpersona, idcontactabilidad)
VALUES (1, NULL, 1), (2, NULL, 2), (NULL, 1, 1), (NULL, 2, 2);
INSERT INTO clientes (idempresa, idpersona, idcontactabilidad)
VALUES (null, 3, 1);
INSERT INTO clientes (idempresa, idpersona, idcontactabilidad)
VALUES (null, 4, 1);

-- INSERT para la tabla 'contratos'
INSERT INTO contratos (idrol, idpersona, fechainicio, fechafin)
VALUES 
(1, 1, '2025-01-01', '2025-12-31'), 
(2, 2, '2025-02-01', '2025-11-30');

-- INSERT para la tabla 'colaboradores'
INSERT INTO colaboradores (idcontrato, namuser, passuser, estado)
VALUES 
(1, 'admin_user', 'admin_pass', TRUE), 
(2, 'mechanic_user', 'mechanic_pass', TRUE);

-- INSERT para la tabla 'modelos'
INSERT INTO modelos (idtipov, idmarca, modelo)
VALUES 
(1, 1, 'Corolla'), 
(2, 2, 'Ranger');

-- Insert for 'vehiculos' table
INSERT INTO vehiculos (idmodelo, placa, anio, numserie, color, tipocombustible)
VALUES
(1, 'ABC123', '2020', 'XYZ123456789', 'Blanco', 'Gasolina'),
(2, 'DEF456', '2021', 'XYZ987654321', 'Azul', 'Diésel');

-- Luego, insertar los propietarios
INSERT INTO propietarios (idcliente, idvehiculo, fechainicio, fechafinal)
VALUES 
(1, 1, '2025-01-01', NULL), 
(2, 2, '2025-02-01', NULL);

-- INSERT para la tabla 'ordenservicios'
INSERT INTO ordenservicios (idadmin, idmecanico, idpropietario, idcliente, idvehiculo, kilometraje, observaciones, ingresogrua, fechaingreso)
VALUES 
(1, 2, 1, 1, 1, 15000, 'Cambio de aceite', TRUE, '2025-03-01 10:00:00'),
(2, 1, 2, 2, 2, 20000, 'Revisión general', FALSE, '2025-03-02 11:00:00');

-- INSERT para 'observaciones'
INSERT INTO observaciones (idcomponente, idorden, estado, foto)
VALUES 
(1, 1, TRUE, 'motor_foto.jpg'),
(2, 2, TRUE, 'transmision_foto.jpg');

-- INSERT para 'ventas'
INSERT INTO ventas (idcliente, idcolaborador, tipocom, fechahora, numserie, numcom, moneda)
VALUES 
(1, 1, 'boleta', NOW(), 'B001', '0001', 'Soles'),
(2, 2, 'factura', NOW(), 'F001', '0002', 'Dolares');

-- INSERT para 'productos'
INSERT INTO productos (idmarca, idsubcategoria, descripcion, precio, presentacion, undmedida, cantidad)
VALUES
(1, 1, 'Filtro de aire para motor de 4 cilindros', 50.00, 'Caja', 'Unidad', 100),
(1, 1, 'Bujías para motor Toyota Corolla', 120.00, 'Paquete', 'Unidad', 80),
(2, 2, 'Amortiguador trasero para camioneta Ford Ranger', 200.00, 'Unidad', 'Pieza', 60),
(2, 1, 'Pastillas de freno para vehículos de carga', 90.00, 'Paquete', 'Unidad', 40),
(1, 1, 'Aceite para motor 5W-30', 40.00, 'Caja', 'Litro', 150),
(2, 2, 'Transmisión automática para Ford Ranger', 1500.00, 'Unidad', 'Pieza', 10),
(1, 2, 'Freno de disco delantero para Toyota Corolla', 250.00, 'Unidad', 'Pieza', 25),
(1, 1, 'Filtro de aceite para motor de sedán', 30.00, 'Paquete', 'Unidad', 120),
(2, 1, 'Correa de distribución para Ford Ranger', 150.00, 'Caja', 'Unidad', 30),
(1, 2, 'Eje delantero para Toyota Corolla', 500.00, 'Unidad', 'Pieza', 15);

-- INSERT para 'detalleventa'
INSERT INTO detalleventa (idproducto, idventa, idorden, idpromocion, cantidad, numserie, precioventa, descuento)
VALUES
(1, 1, 1, 1, 2, '{"seriales": ["A123"]}', 60.00, 10.00),  -- Filtro de aire para motor de 4 cilindros
(2, 1, 2, 2, 1, '{"seriales": ["B234"]}', 140.00, 5.00),          -- Bujías para motor Toyota Corolla
(3, 2, 2, 2, 1, '{"seriales": ["C345"]}', 250.00, 0.00),          -- Amortiguador trasero para camioneta Ford Ranger
(4, 1, 1, 2, 1, '{"seriales": ["D456"]}', 110.00, 0.00),          -- Pastillas de freno para vehículos de carga
(5, 1, 1, 1, 1, '{"seriales": ["E567"]}', 50.00, 0.00),           -- Aceite para motor 5W-30
(6, 2, 2, 2, 1, '{"seriales": ["F678"]}', 1700.00, 0.00),         -- Transmisión automática para Ford Ranger
(7, 1, 1, 2, 1, '{"seriales": ["G789"]}', 300.00, 5.00),          -- Freno de disco delantero para Toyota Corolla
(8, 2, 2, 1, 1, '{"seriales": ["H890"]}', 40.00, 10.00),           -- Filtro de aceite para motor de sedán
(9, 1, 1, 2, 1, '{"seriales": ["I901"]}', 170.00, 0.00),          -- Correa de distribución para Ford Ranger
(10, 2, 2, 1, 1, '{"seriales": ["J012"]}', 600.00, 0.00);   

INSERT INTO cotizaciones (idcolaborador, idcliente, vigenciadias, moneda)
VALUES 
(1, 1, 15, 'Soles'),
(1, 2, 15, 'Dólares');

INSERT INTO detallecotizacion (idcotizacion, idproducto, cantidad, precio, descuento)
VALUES 
-- Cotización 1
(1, 1, 2, 55.00, 5.00),    -- Filtro de aire
(1, 5, 1, 45.00, 0.00),    -- Aceite 5W-30
(1, 4, 1, 95.00, 5.00),    -- Pastillas de freno
-- Cotización 2
(2, 3, 1, 210.00, 0.00),   -- Amortiguador
(2, 6, 1, 1600.00, 3.00),  -- Transmisión
(2, 8, 2, 35.00, 2.00);    -- Filtro de aceite


-- Consultas de ejemplo
SELECT * FROM empresas WHERE idempresa IN (1, 2);
SELECT * FROM personas WHERE idpersona IN (1, 2);
SELECT * FROM contactabilidad WHERE idcontactabilidad IN (1, 2);
SELECT * FROM detalleventa;
SELECT * FROM ventas;
SELECT * FROM detallecompra;
SELECT * FROM compras;
SELECT * FROM clientes WHERE idcliente = 2;

SELECT * FROM empresas;
SELECT * FROM personas;
SELECT * FROM productos;
SELECT * FROM clientes;
SELECT * FROM promociones WHERE idpromocion IN (1, 2, 3);

SELECT idcolaborador FROM colaboradores WHERE idcolaborador IN (1, 2);
SELECT * FROM productos WHERE idproducto IN (1, 2, 3);

-- Mostrar la estructura de la tabla 'clientes' y 'productos'
SHOW CREATE TABLE clientes;
DESCRIBE productos;
SELECT * FROM personas WHERE numdoc = '12345678';

