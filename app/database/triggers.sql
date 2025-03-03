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
