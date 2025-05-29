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
 
  INSERT INTO roles (rol) VALUES ('administrador'),('Jefe Mecanico'),('mecanico'),('Marketing');
  
  INSERT INTO formapagos (formapago) VALUES 
('Deposito'),
('Visa'),
('Plin'),
('Yape'),
('Efectivo');

INSERT INTO tipomovimientos (flujo,tipomov) 
VALUES ('entrada','compra'),('salida','venta'),('entrada','devolucion'),('salida', 'devolucion'),('entrada', 'stock inicial');

INSERT INTO personas (nombres, apellidos, tipodoc, numdoc, numruc, direccion, correo, telprincipal, telalternativo) VALUES
('Maria Elena', 'Castila Hernandez', 'DNI', '75849320', '20123456789', 'Av. Los Pinos 123', 'elenafix360@gmail.com', '987654321', NULL);

 INSERT INTO Contratos ( idpersona, idrol, fechainicio, fechafin) VALUES
 (1, 1, '2023-01-01', NULL);
  CALL spRegisterColaborador('ElenaCastilla','fix3602025',1)