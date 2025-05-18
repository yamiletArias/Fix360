-- Test movements SQL for dbfix360
USE dbfix360;


DELIMITER $$
-- select * from kardex;
-- select * from movimientos;
-- Stored procedure to generate test kardex and movimientos for all products
CREATE PROCEDURE test_movimientos()
BEGIN
  DECLARE done INT DEFAULT FALSE;
  DECLARE pid INT;
  DECLARE tk INT;
  DECLARE tm_compra INT;
  DECLARE tm_venta INT;
  DECLARE tm_devol INT;
  DECLARE curs CURSOR FOR SELECT idproducto FROM productos;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

  -- Get movement type IDs
  SELECT idtipomov INTO tm_compra FROM tipomovimientos WHERE flujo='entrada' AND tipomov='compra' LIMIT 1;
  SELECT idtipomov INTO tm_venta  FROM tipomovimientos WHERE flujo='salida'  AND tipomov='venta' LIMIT 1;
  SELECT idtipomov INTO tm_devol  FROM tipomovimientos WHERE flujo='entrada' AND tipomov='devolucion' LIMIT 1;

  OPEN curs;
  read_loop: LOOP
    FETCH curs INTO pid;
    IF done THEN LEAVE read_loop; END IF;

    -- Create kardex entry
    INSERT INTO kardex (idproducto, fecha, stockmin, stockmax)
    VALUES (pid, '2025-05-01', 0, 100);
    SET tk = LAST_INSERT_ID();

    -- Insert three movements: purchase, sale, purchase
    INSERT INTO movimientos (idkardex, idtipomov, fecha, cantidad, saldorestante)
      VALUES (tk, tm_compra, '2025-05-02', 20, 20);
    INSERT INTO movimientos (idkardex, idtipomov, fecha, cantidad, saldorestante)
      VALUES (tk, tm_venta,  '2025-05-03',  5, 15);
    INSERT INTO movimientos (idkardex, idtipomov, fecha, cantidad, saldorestante)
      VALUES (tk, tm_compra, '2025-05-04', 10, 25);
  END LOOP;
  CLOSE curs;
END$$

DELIMITER ;
-- select * from movimientos
-- Call the procedure and then drop it
CALL test_movimientos();
DROP PROCEDURE IF EXISTS test_movimientos;

SELECT * FROM movimientos;



CREATE OR REPLACE VIEW v_stock_actual AS
SELECT
  k.idproducto,
  COALESCE(
    (
      SELECT m.saldorestante
      FROM movimientos AS m
      WHERE m.idkardex = k.idkardex
      ORDER BY m.idmovimiento DESC
      LIMIT 1
    ),
    k.stockmin
  ) AS stock_actual
FROM kardex AS k;

SELECT * FROM v_stock_actual;



DELIMITER $$
-- select * from movimientos where idkardex = 52
-- select * from kardex where idproducto = 52
-- call spStockActualPorProducto(52)
-- 2) SP para traer el stock actual de un producto
/*
delimiter $$
CREATE PROCEDURE spStockActualPorProducto(
  IN _idproducto INT
)
BEGIN
  DECLARE _idkardex INT;

  -- Buscamos la fila de kardex correspondiente
  SELECT idkardex
    INTO _idkardex
    FROM kardex
   WHERE idproducto = _idproducto
   LIMIT 1;

  IF _idkardex IS NULL THEN
    -- Si no existe kardex para ese producto
    SELECT NULL AS stock_actual;
  ELSE
    -- Último saldo de movimientos o, si no hay, stockmin
    SELECT COALESCE(
      ( SELECT m.saldorestante
          FROM movimientos AS m
         WHERE m.idkardex = _idkardex
         ORDER BY m.idmovimiento DESC
         LIMIT 1
      ),
      ( SELECT k2.stockmin
          FROM kardex AS k2
         WHERE k2.idkardex = _idkardex
      )
    ) AS stock_actual;
  END IF;
END$$
*/
-- select * from movimientos where idmovimiento = 113;
-- CALL spMovimientosPorProducto(42);
-- select * from productos where idproducto = 42
-- CALL spStockActualPorProducto(42);

-- select * from kardex where idproducto = 1;
-- select * from productos;


DROP PROCEDURE IF EXISTS spStockActualPorProducto;
DELIMITER $$
CREATE PROCEDURE spStockActualPorProducto(
  IN _idproducto INT
)
BEGIN
  DECLARE _idkardex INT;

  -- 1) Buscamos el kardex asociado a ese producto
  SELECT idkardex
    INTO _idkardex
    FROM kardex
   WHERE idproducto = _idproducto
   LIMIT 1;

  -- 2) Si no hay kardex, devolvemos NULLs en los tres campos
  IF _idkardex IS NULL THEN
    SELECT 
      NULL AS stock_actual,
      NULL AS stockmin,
      NULL AS stockmax;
  ELSE
    -- 3) Si existe kardex, devolvemos stockmin/stockmax y calculamos stock_actual
    SELECT
      COALESCE(
        ( SELECT m.saldorestante
            FROM movimientos AS m
           WHERE m.idkardex = _idkardex
           ORDER BY m.idmovimiento DESC
           LIMIT 1
        ),
        k.stockmin     -- si no hay movimientos, usamos stockmin
      )                  AS stock_actual,
      k.stockmin,
      k.stockmax
    FROM kardex AS k
    WHERE k.idkardex = _idkardex;
  END IF;
END$$

CALL buscar_producto('s')
DELIMITER $$

DROP PROCEDURE IF EXISTS buscar_producto $$
CREATE PROCEDURE buscar_producto(
    IN in_termino_busqueda VARCHAR(255)
)
BEGIN
    SELECT
        P.idproducto,
        CONCAT(S.subcategoria, ' ', P.descripcion) AS subcategoria_producto,
        P.precio,
        (
            SELECT COALESCE(
                SUM(
                    CASE 
                        WHEN tm.flujo = 'entrada' THEN m.cantidad
                        WHEN tm.flujo = 'salida'  THEN -m.cantidad
                        ELSE 0
                    END
                ), 
                0
            )
            FROM movimientos AS m
            JOIN tipomovimientos AS tm ON m.idtipomov = tm.idtipomov
            WHERE m.idkardex = k.idkardex
        ) AS stock
    FROM productos     AS P
    JOIN subcategorias AS S ON P.idsubcategoria = S.idsubcategoria
    JOIN kardex        AS k ON P.idproducto     = k.idproducto
    WHERE S.subcategoria LIKE CONCAT('%', in_termino_busqueda, '%')
       OR P.descripcion   LIKE CONCAT('%', in_termino_busqueda, '%')
    LIMIT 10;
END $$

DELIMITER ;



