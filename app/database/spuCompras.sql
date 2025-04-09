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
