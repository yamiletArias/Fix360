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