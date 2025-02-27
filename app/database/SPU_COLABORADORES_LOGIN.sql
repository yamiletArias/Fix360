USE dbfix360;

DROP PROCEDURE IF EXISTS `spu_colaboradores_login`;
DELIMITER //
CREATE PROCEDURE spu_colaboradores_login(IN _namuser VARCHAR(50))
BEGIN
    SELECT 
        C.idcolaborador,
        C.namuser,
        C.passuser,
        P.nombres,
        P.apellidos,
        R.rol
    FROM colaboradores C
    INNER JOIN contratos CO ON CO.idcontrato = C.idcontrato
    INNER JOIN personas P ON P.idpersona = CO.idpersona
    INNER JOIN roles R ON R.idrol = CO.idrol
    WHERE C.namuser = _namuser 
    AND C.estado = TRUE;
END //
DELIMITER ;

CALL spu_colaboradores_login('juan.perez'); -- Admin
CALL spu_colaboradores_login('carlos.diaz'); -- Gerente
CALL spu_colaboradores_login('luis.rodriguez'); -- Mecanico
