-- INSERT para la tabla 'personas'
INSERT INTO personas (nombres, apellidos, tipodoc, numdoc, direccion, correo, telprincipal, telalternativo)
VALUES 
('Juan', 'Pérez', 'DNI', '12345678', 'Av. Las Flores 123', 'juan.perez@email.com', '987654321', '998877665'),
('María', 'Gómez', 'DNI', '87654321', 'Calle Sol 456', 'maria.gomez@email.com', '912345678', '977665544');

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
VALUES ('Motor'), ('Transmisión');

-- INSERT para la tabla 'subcategorias'
INSERT INTO subcategorias (idcategoria, subcategoria)
VALUES (1, 'Motores de 4 cilindros'), (2, 'Transmisiones automáticas');

-- INSERT para la tabla 'clientes'
INSERT INTO clientes (idempresa, idpersona, idcontactabilidad)
VALUES (1, NULL, 1), (2, NULL, 2), (NULL, 1, 1), (NULL, 2, 2);

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
INSERT INTO vehiculos (idmodelo, placa, anio, kilometraje, numserie, color, tipocombustible)
VALUES
(1, 'ABC123', '2020', 15000.00, 'XYZ123456789', 'Blanco', 'Gasolina'),
(2, 'DEF456', '2021', 12000.00, 'XYZ987654321', 'Azul', 'Diésel');

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
(1, 1, 'Descripción del Producto A', 100.00, 'Caja', 'Unidad', 50),
(2, 2, 'Descripción del Producto B', 200.00, 'Paquete', 'Unidad', 30),
(1, 1, 'Descripción del Producto C', 150.00, 'Caja', 'Unidad', 20);
-- INSERT para 'detalleventa'
INSERT INTO detalleventa (idproducto, idventa, idorden, idpromocion, cantidad, numserie, precioventa, descuento)
VALUES
(1, 1, 1, 1, 2, '{"seriales": ["A123", "A124"]}', 150.00, 10.00),
(2, 1, 2, 2, 1, '{"seriales": ["B234"]}', 200.00, 5.00),
(3, 2, 2, 2, 3, '{"seriales": ["C345", "C346", "C347"]}', 120.00, 0.00);


-- Consultas de ejemplo
SELECT * FROM empresas WHERE idempresa IN (1, 2);
SELECT * FROM personas WHERE idpersona IN (1, 2);
SELECT * FROM contactabilidad WHERE idcontactabilidad IN (1, 2);
SELECT * FROM ventas;
SELECT * FROM empresas;
SELECT * FROM productos;
SELECT * FROM clientes;
SELECT * FROM promociones WHERE idpromocion IN (1, 2, 3);

SELECT idcolaborador FROM colaboradores WHERE idcolaborador IN (1, 2);
SELECT * FROM productos WHERE idproducto IN (1, 2, 3);

-- Mostrar la estructura de la tabla 'clientes' y 'productos'
SHOW CREATE TABLE clientes;
DESCRIBE productos;
SELECT * FROM personas WHERE numdoc = '12345678';

