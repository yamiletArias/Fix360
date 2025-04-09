
ALTER TABLE compras MODIFY idcolaborador INT NULL;

-- registrar compras
DELIMITER $$
CREATE PROCEDURE spuRegisterCompra (
  IN _fechacompra DATE,
  IN _tipocom VARCHAR(50),
  IN _numserie VARCHAR(10),
  IN _numcom VARCHAR(10),
  IN _moneda VARCHAR(20),
  IN _idproveedor INT
)
BEGIN
  INSERT INTO compras (
    idproveedor,
    fechacompra,
    tipocom,
    numserie,
    numcom,
    moneda
  )
  VALUES (
    _idproveedor,
    _fechacompra,
    _tipocom,
    _numserie,
    _numcom,
    _moneda
  );
  SELECT LAST_INSERT_ID() AS idcompra;
END $$
DELIMITER ;
-- fin registrar compras

-- registrar detalle compra
DELIMITER $$
CREATE PROCEDURE spuInsertDetalleCompra (
  IN _idcompra INT,
  IN _idproducto INT,
  IN _cantidad INT,
  IN _preciocompra DECIMAL(7,2),
  IN _descuento DECIMAL(5,2)
)
BEGIN
  INSERT INTO detallecompra (
    idproducto,
    idcompra,
    cantidad,
    preciocompra,
    descuento
  )
  VALUES (
    _idproducto,
    _idcompra,
    _cantidad,
    _preciocompra,
    _descuento
  );
END $$
DELIMITER ;
-- fin registrar detalle compra

-- Proveedor
DELIMITER $$
CREATE PROCEDURE spuGetProveedores()
BEGIN
  SELECT DISTINCT 
    p.idproveedor,
    e.nomcomercial AS nombre_empresa
  FROM compras c
  INNER JOIN proveedores p ON c.idproveedor = p.idproveedor
  INNER JOIN empresas e ON p.idempresa = e.idempresa;
END $$
DELIMITER ;

CALL spuGetProveedores();

-- Buscar producto
DELIMITER $$
CREATE PROCEDURE buscar_producto_compras(IN termino_busqueda VARCHAR(255))
BEGIN
    SELECT 
        P.idproducto,
        CONCAT(S.subcategoria, ' ', P.descripcion) AS subcategoria_producto,
        DV.preciocompra
    FROM productos P
    INNER JOIN subcategorias S ON P.idsubcategoria = S.idsubcategoria
    LEFT JOIN detallecompra DV ON P.idproducto = DV.idproducto
    WHERE 
        (S.subcategoria LIKE CONCAT('%', termino_busqueda, '%') OR P.descripcion LIKE CONCAT('%', termino_busqueda, '%'))
    LIMIT 10;
END $$
DELIMITER ;
-- Fin Buscar producto

CALL buscar_producto_compras('ace');

SELECT * FROM detallecompra;
SELECT * FROM compras;