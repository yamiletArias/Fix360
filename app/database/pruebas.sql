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

select * from movimientos;



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

select * from v_stock_actual;



DELIMITER $$

-- 1) SP para traer todos los movimientos de un producto
delimiter $$
CREATE PROCEDURE spMovimientosPorProducto(
  IN _idproducto INT
)
BEGIN
  SELECT
    m.idmovimiento,
    k.idproducto,
    m.fecha,
    m.cantidad,
    m.saldorestante
  FROM movimientos AS m
  JOIN kardex     AS k ON m.idkardex = k.idkardex
  WHERE k.idproducto = _idproducto
  ORDER BY m.fecha DESC, m.idmovimiento DESC;
END$$

-- 2) SP para traer el stock actual de un producto
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
-- select * from movimientos where idmovimiento = 113;
CALL spMovimientosPorProducto(42);

CALL spStockActualPorProducto(42);

