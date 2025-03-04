/* TRIGGER PARA VALIDAR LAS FECHAS EN CONTRATOS*/
DELIMITER $$

CREATE TRIGGER tr_check_fechas_contrato
BEFORE INSERT ON contratos
FOR EACH ROW
BEGIN
    IF NEW.fechainicio >= NEW.fechafin THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: La fecha de inicio debe ser menor a la fecha de fin.';
    END IF;
END $$

DELIMITER ;

/*TRIGGER PARA EVITAR QUE STOCKMIN SEA MAYOR QUE STOCKMAX*/
CREATE TRIGGER trgBeforeInsertKardex
BEFORE INSERT ON kardex
FOR EACH ROW
BEGIN
    IF NEW.stockmin > NEW.stockmax THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'stockmin no puede ser mayor que stockmax';
    END IF;
END;


/* TRIGGER PARA EVITAR DUPLICADOS*/
DROP TRIGGER IF EXISTS tr_prevent_duplicate_tipomovimientos;
CREATE TRIGGER tr_prevent_duplicate_tipomovimientos
BEFORE INSERT ON tipomovimientos
FOR EACH ROW
BEGIN
    IF EXISTS (
        SELECT 1 FROM tipomovimientos 
        WHERE flujo = NEW.flujo AND tipomov = NEW.tipomov
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El tipo de movimiento ya existe con este flujo.';
    END IF;
END;
