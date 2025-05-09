-- TRIGGER PARA VENTAS

DELIMITER $$
DROP TRIGGER IF EXISTS tr_restar_stock_venta$$
CREATE TRIGGER tr_restar_stock_venta
  AFTER INSERT ON detalleventa
  FOR EACH ROW
BEGIN
  UPDATE productos
    SET cantidad = cantidad - NEW.cantidad
    WHERE idproducto = NEW.idproducto;
END$$
DELIMITER ;

DELIMITER $$
DROP TRIGGER IF EXISTS tr_recuperar_stock_venta_header$$
CREATE TRIGGER tr_recuperar_stock_venta_header
  AFTER UPDATE ON ventas
  FOR EACH ROW
BEGIN
  IF OLD.estado = TRUE AND NEW.estado = FALSE THEN
    UPDATE productos p
    JOIN detalleventa dv ON p.idproducto = dv.idproducto
    SET p.cantidad = p.cantidad + dv.cantidad
    WHERE dv.idventa = NEW.idventa;
  END IF;
END$$
DELIMITER ;

-- TRIGGER PARA COMPRAS

DELIMITER $$

DROP TRIGGER IF EXISTS tr_sumar_stock_compra$$
CREATE TRIGGER tr_sumar_stock_compra
  AFTER INSERT ON detallecompra
  FOR EACH ROW
BEGIN
  UPDATE productos
    SET cantidad = cantidad + NEW.cantidad
    WHERE idproducto = NEW.idproducto;
END$$

DELIMITER ;

DELIMITER $$

DROP TRIGGER IF EXISTS tr_recuperar_stock_compra_header$$
CREATE TRIGGER tr_recuperar_stock_compra_header
AFTER UPDATE ON compras
FOR EACH ROW
BEGIN
  -- Verificamos si la compra estaba activa y fue anulada
  IF OLD.estado = TRUE AND NEW.estado = FALSE THEN
    UPDATE productos p
    JOIN detallecompra dc ON p.idproducto = dc.idproducto
    SET p.cantidad = p.cantidad - dc.cantidad
    WHERE dc.idcompra = NEW.idcompra;
  END IF;
END$$

DELIMITER ;


-- POR EL MOMENTO
INSERT INTO formapagos (formapago) VALUES 
  ('Efectivo'),
  ('Tarjeta'),
  ('Transferencia');
-- PRUEBA PARA EL MODAL DE AMORTIZACION
ALTER TABLE ventas
  ADD COLUMN total DECIMAL(10,2) NOT NULL DEFAULT 0,
  ADD COLUMN pagado BOOLEAN NOT NULL DEFAULT FALSE;

DELIMITER $$

CREATE TRIGGER actualiza_pagado_after_ins
AFTER INSERT ON amortizaciones
FOR EACH ROW
BEGIN
  DECLARE suma DECIMAL(10,2);
  SELECT COALESCE(SUM(amortizacion),0)
    INTO suma
    FROM amortizaciones
   WHERE idventa = NEW.idventa;
  UPDATE ventas
     SET pagado = (suma >= total)
   WHERE idventa = NEW.idventa;
END$$

CREATE TRIGGER actualiza_pagado_after_upd
AFTER UPDATE ON amortizaciones
FOR EACH ROW
BEGIN
  DECLARE suma DECIMAL(10,2);
  SELECT COALESCE(SUM(amortizacion),0)
    INTO suma
    FROM amortizaciones
   WHERE idventa = NEW.idventa;
  UPDATE ventas
     SET pagado = (suma >= total)
   WHERE idventa = NEW.idventa;
END$$

DELIMITER ;
-- FIN PRUEBA AMORTIZACIONES


-- AMORTIZACIONES 
DELIMITER $$

DROP TRIGGER IF EXISTS tr_actualizar_saldo_amortizacion$$
CREATE TRIGGER tr_actualizar_saldo_amortizacion
BEFORE INSERT ON amortizaciones
FOR EACH ROW
BEGIN
  DECLARE totalventa DECIMAL(10,2);

  SELECT total INTO totalventa FROM ventas WHERE idventa = NEW.idventa;

  SET NEW.saldo = totalventa - NEW.amortizacion;
END$$

DELIMITER ;