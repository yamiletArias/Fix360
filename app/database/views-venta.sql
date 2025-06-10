-- ************************* VISTA DE DETALLE DE VENTA *************************

-- VISTA DE VENTAS CON ORDEN DE SERVICIO
-- ESTE ES EL QUE FUNCIONA (SIN DUPLICADOS)
DROP VIEW IF EXISTS vista_detalle_venta;
CREATE VIEW vista_detalle_venta AS

-- 1) Parte de PRODUCTOS (LEFT JOIN para que no se excluya la venta si el detalle falta)
SELECT
  v.idventa,
  v.fechahora,

  COALESCE(
    CASE 
      WHEN propc.idempresa IS NOT NULL THEN epc.nomcomercial
      WHEN propc.idpersona IS NOT NULL THEN CONCAT(ppc.nombres,' ',ppc.apellidos)
    END,
    'Sin propietario'
  ) AS propietario,

  COALESCE(
    CONCAT(p.apellidos,' ',p.nombres),
    e.nomcomercial
  ) AS cliente,

  v.kilometraje,
  CONCAT(tv.tipov,' ',ma.nombre,' ',vh.color,' (',vh.placa,')') AS vehiculo,

  CONCAT(su.subcategoria,' ', pr.descripcion) AS producto,
  dv.cantidad,
  dv.precioventa AS precio,
  dv.descuento,
  ROUND((dv.precioventa - dv.descuento) * dv.cantidad, 2) AS total_producto,

  NULL AS tiposervicio,
  NULL AS nombreservicio,
  NULL AS mecanico,
  NULL AS precio_servicio,
  'producto' AS registro_tipo

FROM ventas v
  LEFT JOIN clientes     propc ON v.idpropietario = propc.idcliente
  LEFT JOIN empresas     epc   ON propc.idempresa  = epc.idempresa
  LEFT JOIN personas     ppc   ON propc.idpersona  = ppc.idpersona

  LEFT JOIN clientes     cte   ON v.idcliente      = cte.idcliente
  LEFT JOIN personas     p     ON cte.idpersona    = p.idpersona
  LEFT JOIN empresas     e     ON cte.idempresa    = e.idempresa

  LEFT JOIN vehiculos      vh  ON v.idvehiculo     = vh.idvehiculo
  LEFT JOIN modelos        m   ON vh.idmodelo      = m.idmodelo
  LEFT JOIN tipovehiculos tv  ON m.idtipov        = tv.idtipov
  LEFT JOIN marcas         ma  ON m.idmarca        = ma.idmarca

  LEFT JOIN detalleventa   dv  ON v.idventa        = dv.idventa
  LEFT JOIN productos      pr  ON dv.idproducto    = pr.idproducto
  LEFT JOIN subcategorias  su  ON pr.idsubcategoria = su.idsubcategoria

WHERE v.estado = TRUE
  AND dv.idventa IS NOT NULL   -- sólo filas que realmente tienen producto

UNION ALL

-- 2) Parte de SERVICIOS (igual que antes, sólo filas con servicio)
SELECT
  v.idventa,
  v.fechahora,

  COALESCE(
    CASE 
      WHEN propc.idempresa IS NOT NULL THEN epc.nomcomercial
      WHEN propc.idpersona IS NOT NULL THEN CONCAT(ppc.nombres,' ',ppc.apellidos)
    END,
    'Sin propietario'
  ) AS propietario,

  COALESCE(
    CONCAT(p.apellidos,' ',p.nombres),
    e.nomcomercial
  ) AS cliente,

  v.kilometraje,
  CONCAT(tv.tipov,' ',ma.nombre,' ',vh.color,' (',vh.placa,')') AS vehiculo,

  NULL AS producto,
  NULL AS cantidad,
  NULL AS precio,
  NULL AS descuento,
  NULL AS total_producto,

  sc.subcategoria   AS tiposervicio,
  se.servicio       AS nombreservicio,
  COALESCE(col.namuser, CONCAT(percol.nombres, ' ', percol.apellidos)) AS mecanico,
  dos.precio        AS precio_servicio,
  'servicio'        AS registro_tipo

FROM ventas v
  LEFT JOIN clientes     propc ON v.idpropietario = propc.idcliente
  LEFT JOIN empresas     epc   ON propc.idempresa  = epc.idempresa
  LEFT JOIN personas     ppc   ON propc.idpersona  = ppc.idpersona

  LEFT JOIN clientes     cte   ON v.idcliente      = cte.idcliente
  LEFT JOIN personas     p     ON cte.idpersona    = p.idpersona
  LEFT JOIN empresas     e     ON cte.idempresa    = e.idempresa

  LEFT JOIN vehiculos      vh  ON v.idvehiculo     = vh.idvehiculo
  LEFT JOIN modelos        m   ON vh.idmodelo      = m.idmodelo
  LEFT JOIN tipovehiculos tv  ON m.idtipov         = tv.idtipov
  LEFT JOIN marcas         ma  ON m.idmarca        = ma.idmarca

  LEFT JOIN ordenservicios         os    ON v.idexpediente_ot = os.idorden
  LEFT JOIN detalleordenservicios  dos   ON os.idorden        = dos.idorden
  LEFT JOIN servicios              se    ON dos.idservicio    = se.idservicio
  LEFT JOIN subcategorias          sc    ON se.idsubcategoria = sc.idsubcategoria
  LEFT JOIN colaboradores          col   ON dos.idmecanico    = col.idcolaborador
  LEFT JOIN contratos              con   ON col.idcontrato    = con.idcontrato
  LEFT JOIN personas               percol ON con.idpersona    = percol.idpersona

WHERE v.estado = TRUE
  AND dos.idorden IS NOT NULL;   -- sólo filas que realmente tienen servicio

/*
DROP VIEW IF EXISTS vista_detalle_venta;
CREATE VIEW vista_detalle_venta AS

-- 1) Parte de PRODUCTOS
SELECT
  v.idventa,
  v.fechahora,

  COALESCE(
    CASE 
      WHEN propc.idempresa IS NOT NULL THEN epc.nomcomercial
      WHEN propc.idpersona IS NOT NULL THEN CONCAT(ppc.nombres,' ',ppc.apellidos)
    END,
    'Sin propietario'
  ) AS propietario,

  COALESCE(
    CONCAT(p.apellidos,' ',p.nombres),
    e.nomcomercial
  ) AS cliente,

  v.kilometraje,
  CONCAT(tv.tipov,' ',ma.nombre,' ',vh.color,' (',vh.placa,')') AS vehiculo,

  CONCAT(su.subcategoria,' ', pr.descripcion) AS producto,
  dv.cantidad,
  dv.precioventa AS precio,
  dv.descuento,
  ROUND((dv.precioventa - dv.descuento) * dv.cantidad, 2) AS total_producto,

  NULL AS tiposervicio,
  NULL AS nombreservicio,
  NULL AS mecanico,
  NULL AS precio_servicio,
  'producto' AS registro_tipo

FROM ventas v
  LEFT JOIN clientes     propc ON v.idpropietario = propc.idcliente
  LEFT JOIN empresas     epc   ON propc.idempresa  = epc.idempresa
  LEFT JOIN personas     ppc   ON propc.idpersona  = ppc.idpersona

  LEFT JOIN clientes     cte   ON v.idcliente      = cte.idcliente
  LEFT JOIN personas     p     ON cte.idpersona    = p.idpersona
  LEFT JOIN empresas     e     ON cte.idempresa    = e.idempresa

  LEFT JOIN vehiculos      vh  ON v.idvehiculo     = vh.idvehiculo
  LEFT JOIN modelos        m   ON vh.idmodelo      = m.idmodelo
  LEFT JOIN tipovehiculos tv  ON m.idtipov        = tv.idtipov
  LEFT JOIN marcas         ma  ON m.idmarca        = ma.idmarca

  JOIN detalleventa       dv   ON v.idventa        = dv.idventa
  JOIN productos          pr   ON dv.idproducto    = pr.idproducto
  JOIN subcategorias      su   ON pr.idsubcategoria = su.idsubcategoria

WHERE v.estado = TRUE

UNION ALL

-- 2) Parte de SERVICIOS
SELECT
  v.idventa,
  v.fechahora,

  COALESCE(
    CASE 
      WHEN propc.idempresa IS NOT NULL THEN epc.nomcomercial
      WHEN propc.idpersona IS NOT NULL THEN CONCAT(ppc.nombres,' ',ppc.apellidos)
    END,
    'Sin propietario'
  ) AS propietario,

  COALESCE(
    CONCAT(p.apellidos,' ',p.nombres),
    e.nomcomercial
  ) AS cliente,

  v.kilometraje,
  CONCAT(tv.tipov,' ',ma.nombre,' ',vh.color,' (',vh.placa,')') AS vehiculo,

  NULL AS producto,
  NULL AS cantidad,
  NULL AS precio,
  NULL AS descuento,
  NULL AS total_producto,

  sc.subcategoria   AS tiposervicio,
  se.servicio       AS nombreservicio,

  -- Aquí usamos COALESCE para mostrar el nombre completo si namuser está vacío
  COALESCE(col.namuser, CONCAT(percol.nombres, ' ', percol.apellidos)) AS mecanico,

  dos.precio        AS precio_servicio,
  'servicio'        AS registro_tipo

FROM ventas v
  LEFT JOIN clientes     propc ON v.idpropietario = propc.idcliente
  LEFT JOIN empresas     epc   ON propc.idempresa  = epc.idempresa
  LEFT JOIN personas     ppc   ON propc.idpersona  = ppc.idpersona

  LEFT JOIN clientes     cte   ON v.idcliente      = cte.idcliente
  LEFT JOIN personas     p     ON cte.idpersona    = p.idpersona
  LEFT JOIN empresas     e     ON cte.idempresa    = e.idempresa

  LEFT JOIN vehiculos      vh  ON v.idvehiculo     = vh.idvehiculo
  LEFT JOIN modelos        m   ON vh.idmodelo      = m.idmodelo
  LEFT JOIN tipovehiculos tv  ON m.idtipov         = tv.idtipov
  LEFT JOIN marcas         ma  ON m.idmarca        = ma.idmarca

  LEFT JOIN ordenservicios         os   ON v.idexpediente_ot = os.idorden
  LEFT JOIN detalleordenservicios  dos  ON os.idorden        = dos.idorden
  LEFT JOIN servicios              se   ON dos.idservicio    = se.idservicio
  LEFT JOIN subcategorias          sc   ON se.idsubcategoria = sc.idsubcategoria
  LEFT JOIN colaboradores          col  ON dos.idmecanico    = col.idcolaborador
  LEFT JOIN contratos              con  ON col.idcontrato    = con.idcontrato
  LEFT JOIN personas              percol ON con.idpersona    = percol.idpersona

WHERE v.estado = TRUE
  AND dos.idorden IS NOT NULL;
*/

/*
-- MECANICO NO SALE:
DROP VIEW IF EXISTS vista_detalle_venta;
CREATE VIEW vista_detalle_venta AS
-- 1) Parte de PRODUCTOS
SELECT
  v.idventa,
  v.fechahora,

  -- Propietario (si es empresa o persona)
  COALESCE(
    CASE 
      WHEN propc.idempresa IS NOT NULL THEN epc.nomcomercial
      WHEN propc.idpersona IS NOT NULL THEN CONCAT(ppc.nombres,' ',ppc.apellidos)
    END,
    'Sin propietario'
  ) AS propietario,

  -- Cliente (si es persona ó empresa)
  COALESCE(
    CONCAT(p.apellidos,' ',p.nombres),
    e.nomcomercial
  ) AS cliente,

  v.kilometraje,
  CONCAT(tv.tipov,' ',ma.nombre,' ',vh.color,' (',vh.placa,')') AS vehiculo,

  -- Campos de PRODUCTO
  CONCAT(su.subcategoria,' ', pr.descripcion) AS producto,
  dv.cantidad,
  dv.precioventa AS precio,
  dv.descuento,
  ROUND((dv.precioventa - dv.descuento) * dv.cantidad, 2) AS total_producto,

  -- Campos de SERVICIO = NULL aquí
  NULL AS tiposervicio,
  NULL AS nombreservicio,
  NULL AS mecanico,
  NULL AS precio_servicio,
  'producto' AS registro_tipo

FROM ventas v
  LEFT JOIN clientes     propc ON v.idpropietario = propc.idcliente
  LEFT JOIN empresas     epc   ON propc.idempresa  = epc.idempresa
  LEFT JOIN personas     ppc   ON propc.idpersona  = ppc.idpersona

  LEFT JOIN clientes     cte   ON v.idcliente      = cte.idcliente
  LEFT JOIN personas     p     ON cte.idpersona    = p.idpersona
  LEFT JOIN empresas     e     ON cte.idempresa    = e.idempresa

  LEFT JOIN vehiculos      vh  ON v.idvehiculo     = vh.idvehiculo
  LEFT JOIN modelos        m   ON vh.idmodelo      = m.idmodelo
  LEFT JOIN tipovehiculos tv  ON m.idtipov        = tv.idtipov
  LEFT JOIN marcas         ma  ON m.idmarca        = ma.idmarca

  JOIN detalleventa       dv   ON v.idventa        = dv.idventa
  JOIN productos          pr   ON dv.idproducto    = pr.idproducto
  JOIN subcategorias      su   ON pr.idsubcategoria = su.idsubcategoria

WHERE v.estado = TRUE

UNION ALL

-- 2) Parte de SERVICIOS
SELECT
  v.idventa,
  v.fechahora,

  COALESCE(
    CASE 
      WHEN propc.idempresa IS NOT NULL THEN epc.nomcomercial
      WHEN propc.idpersona IS NOT NULL THEN CONCAT(ppc.nombres,' ',ppc.apellidos)
    END,
    'Sin propietario'
  ) AS propietario,

  COALESCE(
    CONCAT(p.apellidos,' ',p.nombres),
    e.nomcomercial
  ) AS cliente,

  v.kilometraje,
  CONCAT(tv.tipov,' ',ma.nombre,' ',vh.color,' (',vh.placa,')') AS vehiculo,

  -- Campos de PRODUCTO = NULL aquí
  NULL AS producto,
  NULL AS cantidad,
  NULL AS precio,
  NULL AS descuento,
  NULL AS total_producto,

  -- Campos de SERVICIO (ahora sí llenos)
  sc.subcategoria   AS tiposervicio,
  se.servicio       AS nombreservicio,
  col.namuser       AS mecanico,
  dos.precio        AS precio_servicio,
  'servicio'        AS registro_tipo

FROM ventas v
  LEFT JOIN clientes     propc ON v.idpropietario = propc.idcliente
  LEFT JOIN empresas     epc   ON propc.idempresa  = epc.idempresa
  LEFT JOIN personas     ppc   ON propc.idpersona  = ppc.idpersona

  LEFT JOIN clientes     cte   ON v.idcliente      = cte.idcliente
  LEFT JOIN personas     p     ON cte.idpersona    = p.idpersona
  LEFT JOIN empresas     e     ON cte.idempresa    = e.idempresa

  LEFT JOIN vehiculos      vh  ON v.idvehiculo     = vh.idvehiculo
  LEFT JOIN modelos        m   ON vh.idmodelo      = m.idmodelo
  LEFT JOIN tipovehiculos tv  ON m.idtipov        = tv.idtipov
  LEFT JOIN marcas         ma  ON m.idmarca        = ma.idmarca

  -- Aquí está la corrección: unimos por el id de orden real
  LEFT JOIN ordenservicios         os   ON v.idexpediente_ot = os.idorden
  LEFT JOIN detalleordenservicios  dos  ON os.idorden        = dos.idorden
  LEFT JOIN servicios              se   ON dos.idservicio    = se.idservicio
  LEFT JOIN subcategorias          sc   ON se.idsubcategoria = sc.idsubcategoria
  LEFT JOIN colaboradores          col  ON dos.idmecanico    = col.idcolaborador

WHERE v.estado = TRUE
  AND dos.idorden IS NOT NULL;
*/

/*
-- CON ESTA VISTA SE ESTABA DUPLICANDO
DROP VIEW IF EXISTS vista_detalle_venta;
CREATE VIEW vista_detalle_venta AS
SELECT
  v.idventa,
  v.fechahora,
  -- Propietario (directo desde clientes)
  COALESCE(
    CASE 
      WHEN propc.idempresa IS NOT NULL 
        THEN epc.nomcomercial
      WHEN propc.idpersona IS NOT NULL 
        THEN CONCAT(ppc.nombres,' ',ppc.apellidos)
    END,
    'Sin propietario'
  ) AS propietario,

  -- Cliente (vendedor u otro cliente)
  COALESCE(
    CONCAT(p.apellidos,' ',p.nombres),
    e.nomcomercial
  ) AS cliente,

  v.kilometraje,
  CONCAT(tv.tipov,' ',ma.nombre,' ',vh.color,' (',vh.placa,')') AS vehiculo,

  -- Campos de producto
  CONCAT(su.subcategoria,' ', pr.descripcion) AS producto,
  dv.cantidad,
  dv.precioventa AS precio,
  dv.descuento,
  ROUND((dv.precioventa - dv.descuento) * dv.cantidad, 2) AS total_producto,

  -- Campos de servicio = NULL en esta parte
  NULL AS tiposervicio,
  NULL AS nombreservicio,
  NULL AS mecanico,
  NULL AS precio_servicio,
  'producto' AS registro_tipo

FROM ventas v
  LEFT JOIN clientes     propc ON v.idpropietario = propc.idcliente
  LEFT JOIN empresas     epc   ON propc.idempresa  = epc.idempresa
  LEFT JOIN personas     ppc   ON propc.idpersona  = ppc.idpersona

  LEFT JOIN clientes     cte   ON v.idcliente      = cte.idcliente
  LEFT JOIN personas     p     ON cte.idpersona    = p.idpersona
  LEFT JOIN empresas     e     ON cte.idempresa    = e.idempresa

  LEFT JOIN vehiculos      vh  ON v.idvehiculo     = vh.idvehiculo
  LEFT JOIN modelos        m   ON vh.idmodelo      = m.idmodelo
  LEFT JOIN tipovehiculos tv  ON m.idtipov        = tv.idtipov
  LEFT JOIN marcas         ma  ON m.idmarca        = ma.idmarca

  JOIN detalleventa       dv   ON v.idventa        = dv.idventa
  JOIN productos          pr   ON dv.idproducto    = pr.idproducto
  JOIN subcategorias      su   ON pr.idsubcategoria = su.idsubcategoria

WHERE v.estado = TRUE

UNION ALL

SELECT
  v.idventa,
  v.fechahora,
  COALESCE(
    CASE 
      WHEN propc.idempresa IS NOT NULL 
        THEN epc.nomcomercial
      WHEN propc.idpersona IS NOT NULL 
        THEN CONCAT(ppc.nombres,' ',ppc.apellidos)
    END,
    'Sin propietario'
  ) AS propietario,

  COALESCE(
    CONCAT(p.apellidos,' ',p.nombres),
    e.nomcomercial
  ) AS cliente,

  v.kilometraje,
  CONCAT(tv.tipov,' ',ma.nombre,' ',vh.color,' (',vh.placa,')') AS vehiculo,

  NULL AS producto,
  NULL AS cantidad,
  NULL AS precio,
  NULL AS descuento,
  NULL AS total_producto,

  sc.subcategoria AS tiposervicio,
  se.servicio      AS nombreservicio,
  col.namuser      AS mecanico,
  dos.precio       AS precio_servicio,
  'servicio' AS registro_tipo

FROM ventas v
  LEFT JOIN clientes     propc ON v.idpropietario = propc.idcliente
  LEFT JOIN empresas     epc   ON propc.idempresa  = epc.idempresa
  LEFT JOIN personas     ppc   ON propc.idpersona  = ppc.idpersona

  LEFT JOIN clientes     cte   ON v.idcliente      = cte.idcliente
  LEFT JOIN personas     p     ON cte.idpersona    = p.idpersona
  LEFT JOIN empresas     e     ON cte.idempresa    = e.idempresa

  LEFT JOIN vehiculos      vh  ON v.idvehiculo     = vh.idvehiculo
  LEFT JOIN modelos        m   ON vh.idmodelo      = m.idmodelo
  LEFT JOIN tipovehiculos tv  ON m.idtipov        = tv.idtipov
  LEFT JOIN marcas         ma  ON m.idmarca        = ma.idmarca

  LEFT JOIN ordenservicios         os   ON v.idpropietario = os.idpropietario
                                     AND DATE(v.fechahora) = DATE(os.fechaingreso)
  LEFT JOIN detalleordenservicios  dos  ON os.idorden     = dos.idorden
  LEFT JOIN servicios              se   ON dos.idservicio    = se.idservicio
  LEFT JOIN subcategorias          sc   ON se.idsubcategoria = sc.idsubcategoria
  LEFT JOIN colaboradores          col  ON dos.idmecanico    = col.idcolaborador

WHERE v.estado = TRUE
  AND dos.idorden IS NOT NULL;
*/

/* REAL no fake
DROP VIEW IF EXISTS vista_detalle_venta;
CREATE VIEW vista_detalle_venta AS
-- filas de productos
SELECT
  v.idventa,
  v.fechahora,
  COALESCE(CONCAT(p.apellidos,' ',p.nombres), e.nomcomercial) AS cliente,
  v.kilometraje,
  CONCAT(tv.tipov,' ',ma.nombre,' ',vh.color,' (',vh.placa,')') AS vehiculo,
  -- columnas de producto
  CONCAT(su.subcategoria,' ', pr.descripcion) AS producto,
  dv.cantidad,
  dv.precioventa AS precio,
  dv.descuento,
  ROUND((dv.precioventa - dv.descuento) * dv.cantidad, 2) AS total_producto,
  -- campos de servicio nulos
  NULL AS tiposervicio,
  NULL AS nombreservicio,
  NULL AS mecanico,
  NULL AS precio_servicio,
  'producto' AS registro_tipo
FROM ventas v
LEFT JOIN clientes cte        ON v.idcliente = cte.idcliente
LEFT JOIN personas p     ON cte.idpersona = p.idpersona
LEFT JOIN empresas e     ON cte.idempresa = e.idempresa
LEFT JOIN vehiculos vh   ON v.idvehiculo = vh.idvehiculo
LEFT JOIN modelos m      ON vh.idmodelo  = m.idmodelo
LEFT JOIN tipovehiculos tv ON m.idtipov  = tv.idtipov
LEFT JOIN marcas ma      ON m.idmarca    = ma.idmarca
JOIN detalleventa dv     ON v.idventa    = dv.idventa
JOIN productos pr        ON dv.idproducto= pr.idproducto
JOIN subcategorias su    ON pr.idsubcategoria = su.idsubcategoria
WHERE v.estado = TRUE
UNION ALL
-- filas de servicios
SELECT
  v.idventa,
  v.fechahora,
  COALESCE(CONCAT(p.apellidos,' ',p.nombres), e.nomcomercial) AS cliente,
  v.kilometraje,
  CONCAT(tv.tipov,' ',ma.nombre,' ',vh.color,' (',vh.placa,')') AS vehiculo,
  -- campos de producto nulos
  NULL AS producto,
  NULL AS cantidad,
  NULL AS precio,
  NULL AS descuento,
  NULL AS total_producto,
  -- columnas de servicio
  sc.subcategoria AS tiposervicio,
  se.servicio      AS nombreservicio,
  col.namuser      AS mecanico,
  dos.precio       AS precio_servicio,
  'servicio' AS registro_tipo
FROM ventas v
LEFT JOIN clientes cte        ON v.idcliente = cte.idcliente
LEFT JOIN personas p     ON cte.idpersona = p.idpersona
LEFT JOIN empresas e     ON cte.idempresa = e.idempresa
LEFT JOIN vehiculos vh   ON v.idvehiculo = vh.idvehiculo
LEFT JOIN modelos m      ON vh.idmodelo  = m.idmodelo
LEFT JOIN tipovehiculos tv ON m.idtipov  = tv.idtipov
LEFT JOIN marcas ma      ON m.idmarca    = ma.idmarca
LEFT JOIN ordenservicios os ON v.idpropietario   = os.idpropietario AND DATE(v.fechahora) = DATE(os.fechaingreso)
LEFT JOIN detalleordenservicios dos ON os.idorden = dos.idorden
LEFT JOIN servicios se            ON dos.idservicio    = se.idservicio
LEFT JOIN subcategorias sc        ON se.idsubcategoria = sc.idsubcategoria
LEFT JOIN colaboradores col       ON dos.idmecanico    = col.idcolaborador
WHERE v.estado = TRUE;*/

-- VISTA DE VENTAS PDF
DROP VIEW IF EXISTS vista_detalle_venta_pdf;
CREATE VIEW vista_detalle_venta_pdf AS

-- === 1) PRODUCTOS ===
SELECT
  v.idventa,
  v.tipocom,
  v.numcom    AS numcomp,
  COALESCE(
    CASE 
      WHEN propc.idempresa IS NOT NULL THEN epc.nomcomercial
      WHEN propc.idpersona IS NOT NULL THEN CONCAT(ppc.nombres,' ',ppc.apellidos)
    END,
    'Sin propietario'
  ) AS propietario,
  COALESCE(
    CASE 
      WHEN cte.idpersona IS NOT NULL THEN CONCAT(p.apellidos,' ',p.nombres)
      WHEN cte.idempresa IS NOT NULL THEN e.nomcomercial
    END,
    'Cliente anónimo'
  ) AS cliente,
  v.fechahora AS fecha,
  v.kilometraje,
  CONCAT(tv.tipov,' ',ma.nombre,' ',vh.color,' (',vh.placa,')') AS vehiculo,
  CONCAT(su.subcategoria,' ',pr.descripcion) AS producto,
  dv.cantidad,
  dv.precioventa AS precio,
  dv.descuento,
  ROUND((dv.precioventa - dv.descuento)*dv.cantidad, 2) AS total_producto,
  NULL AS tiposervicio,
  NULL AS nombreservicio,
  NULL AS mecanico,
  NULL AS precio_servicio,
  'producto' AS registro_tipo

FROM ventas v
  LEFT JOIN clientes     propc ON v.idpropietario = propc.idcliente
  LEFT JOIN empresas     epc   ON propc.idempresa  = epc.idempresa
  LEFT JOIN personas     ppc   ON propc.idpersona  = ppc.idpersona
  LEFT JOIN clientes     cte   ON v.idcliente      = cte.idcliente
  LEFT JOIN personas     p     ON cte.idpersona    = p.idpersona
  LEFT JOIN empresas     e     ON cte.idempresa    = e.idempresa
  LEFT JOIN vehiculos    vh    ON v.idvehiculo     = vh.idvehiculo
  LEFT JOIN modelos      m     ON vh.idmodelo      = m.idmodelo
  LEFT JOIN tipovehiculos tv   ON m.idtipov        = tv.idtipov
  LEFT JOIN marcas       ma    ON m.idmarca        = ma.idmarca
  JOIN detalleventa      dv    ON dv.idventa       = v.idventa
  JOIN productos         pr    ON pr.idproducto    = dv.idproducto
  JOIN subcategorias     su    ON su.idsubcategoria = pr.idsubcategoria

WHERE v.estado = TRUE

UNION ALL

-- === 2) SERVICIOS ===
SELECT
  v.idventa,
  v.tipocom,
  v.numcom    AS numcomp,
  COALESCE(
    CASE 
      WHEN propc.idempresa IS NOT NULL THEN epc.nomcomercial
      WHEN propc.idpersona IS NOT NULL THEN CONCAT(ppc.nombres,' ',ppc.apellidos)
    END,
    'Sin propietario'
  ) AS propietario,
  COALESCE(
    CASE 
      WHEN cte.idpersona IS NOT NULL THEN CONCAT(p.apellidos,' ',p.nombres)
      WHEN cte.idempresa IS NOT NULL THEN e.nomcomercial
    END,
    'Cliente anónimo'
  ) AS cliente,
  v.fechahora AS fecha,
  v.kilometraje,
  CONCAT(tv.tipov,' ',ma.nombre,' ',vh.color,' (',vh.placa,')') AS vehiculo,
  NULL AS producto,
  NULL AS cantidad,
  NULL AS precio,
  NULL AS descuento,
  NULL AS total_producto,
  sc.subcategoria   AS tiposervicio,
  se.servicio       AS nombreservicio,
  -- Ahora usamos COALESCE para mostrar col.namuser o, si es NULL, el nombre completo de percol
  COALESCE(col.namuser, CONCAT(percol.nombres, ' ', percol.apellidos)) AS mecanico,
  dos.precio        AS precio_servicio,
  'servicio'        AS registro_tipo

FROM ventas v
  LEFT JOIN clientes     propc ON v.idpropietario = propc.idcliente
  LEFT JOIN empresas     epc   ON propc.idempresa  = epc.idempresa
  LEFT JOIN personas     ppc   ON propc.idpersona  = ppc.idpersona
  LEFT JOIN clientes     cte   ON v.idcliente      = cte.idcliente
  LEFT JOIN personas     p     ON cte.idpersona    = p.idpersona
  LEFT JOIN empresas     e     ON cte.idempresa    = e.idempresa
  LEFT JOIN vehiculos    vh    ON v.idvehiculo     = vh.idvehiculo
  LEFT JOIN modelos      m     ON vh.idmodelo      = m.idmodelo
  LEFT JOIN tipovehiculos tv   ON m.idtipov        = tv.idtipov
  LEFT JOIN marcas       ma    ON m.idmarca        = ma.idmarca

  LEFT JOIN ordenservicios         os   ON v.idexpediente_ot = os.idorden
  LEFT JOIN detalleordenservicios  dos  ON dos.idorden       = os.idorden
  LEFT JOIN servicios              se   ON dos.idservicio    = se.idservicio
  LEFT JOIN subcategorias          sc   ON sc.idsubcategoria = se.idsubcategoria
  LEFT JOIN colaboradores          col  ON col.idcolaborador = dos.idmecanico
  LEFT JOIN contratos              con  ON col.idcontrato    = con.idcontrato
  LEFT JOIN personas               percol ON con.idpersona   = percol.idpersona

WHERE v.estado = TRUE
  AND dos.idorden IS NOT NULL;

/*
DROP VIEW IF EXISTS vista_detalle_venta_pdf;
CREATE VIEW vista_detalle_venta_pdf AS

-- 1) PRODUCTOS
SELECT
  v.idventa,
  v.tipocom,
  v.numcom    AS numcomp,

  -- Propietario (reemplazo JOIN directo sobre clientes)
  COALESCE(
    CASE 
      WHEN propc.idempresa IS NOT NULL THEN epc.nomcomercial
      WHEN propc.idpersona IS NOT NULL THEN CONCAT(ppc.nombres,' ',ppc.apellidos)
    END,
    'Sin propietario'
  ) AS propietario,

  -- Cliente (aquí ya lo tenías correcto)
  COALESCE(
    CASE 
      WHEN cte.idpersona IS NOT NULL THEN CONCAT(p.apellidos,' ',p.nombres)
      WHEN cte.idempresa IS NOT NULL THEN e.nomcomercial
    END,
    'Cliente anónimo'
  ) AS cliente,

  v.fechahora AS fecha,
  v.kilometraje,
  CONCAT(tv.tipov,' ',ma.nombre,' ',vh.color,' (',vh.placa,')') AS vehiculo,
  
  -- DETALLE DE PRODUCTOS …
  CONCAT(su.subcategoria,' ',pr.descripcion) AS producto,
  dv.cantidad,
  dv.precioventa AS precio,
  dv.descuento,
  ROUND((dv.precioventa - dv.descuento)*dv.cantidad, 2) AS total_producto,

  NULL AS tiposervicio,
  NULL AS nombreservicio,
  NULL AS mecanico,
  NULL AS precio_servicio,
  'producto' AS registro_tipo

FROM ventas v
  -- JOIN directo a clientes para PROPIETARIO
  LEFT JOIN clientes propc          ON v.idpropietario = propc.idcliente
  LEFT JOIN empresas epc            ON propc.idempresa  = epc.idempresa
  LEFT JOIN personas ppc            ON propc.idpersona  = ppc.idpersona

  -- JOIN a clientes para CLIENTE
  LEFT JOIN clientes cte            ON v.idcliente      = cte.idcliente
  LEFT JOIN personas p              ON cte.idpersona    = p.idpersona
  LEFT JOIN empresas e              ON cte.idempresa    = e.idempresa

  -- JOIN Vehículo, Modelo, Marca, etc.
  LEFT JOIN vehiculos      vh  ON v.idvehiculo     = vh.idvehiculo
  LEFT JOIN modelos        m   ON vh.idmodelo      = m.idmodelo
  LEFT JOIN tipovehiculos tv  ON m.idtipov        = tv.idtipov
  LEFT JOIN marcas         ma  ON m.idmarca        = ma.idmarca

  -- Detalle de productos
  JOIN detalleventa       dv   ON dv.idventa       = v.idventa
  JOIN productos          pr   ON pr.idproducto    = dv.idproducto
  JOIN subcategorias      su   ON su.idsubcategoria= pr.idsubcategoria

WHERE v.estado = TRUE

UNION ALL

-- 2) SERVICIOS
SELECT
  v.idventa,
  v.tipocom,
  v.numcom    AS numcomp,

  COALESCE(
    CASE 
      WHEN propc.idempresa IS NOT NULL THEN epc.nomcomercial
      WHEN propc.idpersona IS NOT NULL THEN CONCAT(ppc.nombres,' ',ppc.apellidos)
    END,
    'Sin propietario'
  ) AS propietario,

  COALESCE(
    CASE 
      WHEN cte.idpersona IS NOT NULL THEN CONCAT(p.apellidos,' ',p.nombres)
      WHEN cte.idempresa IS NOT NULL THEN e.nomcomercial
    END,
    'Cliente anónimo'
  ) AS cliente,

  v.fechahora AS fecha,
  v.kilometraje,
  CONCAT(tv.tipov,' ',ma.nombre,' ',vh.color,' (',vh.placa,')') AS vehiculo,

  NULL AS producto,
  NULL AS cantidad,
  NULL AS precio,
  NULL AS descuento,
  NULL AS total_producto,

  sc.subcategoria   AS tiposervicio,
  se.servicio       AS nombreservicio,
  col.namuser       AS mecanico,
  dos.precio        AS precio_servicio,
  'servicio'        AS registro_tipo

FROM ventas v
  -- JOIN directo a clientes para PROPIETARIO (igual que arriba)
  LEFT JOIN clientes propc          ON v.idpropietario = propc.idcliente
  LEFT JOIN empresas epc            ON propc.idempresa  = epc.idempresa
  LEFT JOIN personas ppc            ON propc.idpersona  = ppc.idpersona

  -- JOIN a clientes para CLIENTE
  LEFT JOIN clientes cte            ON v.idcliente      = cte.idcliente
  LEFT JOIN personas p              ON cte.idpersona    = p.idpersona
  LEFT JOIN empresas e              ON cte.idempresa    = e.idempresa

  -- JOIN Vehículo, Modelo, Marca, etc.
  LEFT JOIN vehiculos      vh  ON v.idvehiculo     = vh.idvehiculo
  LEFT JOIN modelos        m   ON vh.idmodelo      = m.idmodelo
  LEFT JOIN tipovehiculos tv  ON m.idtipov        = tv.idtipov
  LEFT JOIN marcas         ma  ON m.idmarca        = ma.idmarca

  -- JOIN servicios por expediente/de orden de servicios
  LEFT JOIN ordenservicios       os   ON v.idexpediente_ot = os.idorden
  LEFT JOIN detalleordenservicios dos  ON dos.idorden       = os.idorden
  LEFT JOIN servicios            se   ON se.idservicio     = dos.idservicio
  LEFT JOIN subcategorias        sc   ON sc.idsubcategoria  = se.idsubcategoria
  LEFT JOIN colaboradores        col  ON col.idcolaborador = dos.idmecanico

WHERE v.estado = TRUE
  AND dos.idorden IS NOT NULL;
*/
  
/*
DROP VIEW IF EXISTS vista_detalle_venta_pdf;
CREATE VIEW vista_detalle_venta_pdf AS
-- 1) Productos
SELECT
  v.idventa,
  v.tipocom,
  v.numcom    AS numcomp,
  COALESCE(
    CASE 
      WHEN propc.idempresa IS NOT NULL THEN epc.nomcomercial
      WHEN propc.idpersona IS NOT NULL THEN CONCAT(ppc.nombres,' ',ppc.apellidos)
    END,
    'Sin propietario'
  ) AS propietario,
  COALESCE(
    CASE
      WHEN cte.idpersona IS NOT NULL THEN CONCAT(p.apellidos,' ',p.nombres)
      WHEN cte.idempresa IS NOT NULL THEN e.nomcomercial
    END,
    'Cliente anónimo'
  ) AS cliente,
  v.fechahora AS fecha,
  v.kilometraje,
  CONCAT(tv.tipov,' ',ma.nombre,' ',vh.color,' (',vh.placa,')') AS vehiculo,
  CONCAT(su.subcategoria,' ',pr.descripcion) AS producto,
  dv.cantidad,
  dv.precioventa AS precio,
  dv.descuento,
  ROUND((dv.precioventa - dv.descuento) * dv.cantidad, 2) AS total_producto,
  NULL AS tiposervicio,
  NULL AS nombreservicio,
  NULL AS mecanico,
  NULL AS precio_servicio,
  'producto' AS registro_tipo
FROM ventas v
LEFT JOIN propietarios prop       ON v.idpropietario = prop.idpropietario
LEFT JOIN clientes propc          ON prop.idcliente   = propc.idcliente
LEFT JOIN empresas epc            ON propc.idempresa  = epc.idempresa
LEFT JOIN personas ppc            ON propc.idpersona  = ppc.idpersona
LEFT JOIN clientes cte            ON v.idcliente      = cte.idcliente
LEFT JOIN personas p              ON cte.idpersona    = p.idpersona
LEFT JOIN empresas e              ON cte.idempresa    = e.idempresa
LEFT JOIN vehiculos vh            ON v.idvehiculo     = vh.idvehiculo
LEFT JOIN modelos m               ON vh.idmodelo      = m.idmodelo
LEFT JOIN tipovehiculos tv        ON m.idtipov        = tv.idtipov
LEFT JOIN marcas ma               ON m.idmarca        = ma.idmarca
JOIN detalleventa dv              ON dv.idventa       = v.idventa
JOIN productos pr                 ON pr.idproducto    = dv.idproducto
JOIN subcategorias su             ON su.idsubcategoria= pr.idsubcategoria
WHERE v.estado = TRUE
UNION ALL
-- 2) Servicios
SELECT
  v.idventa,
  v.tipocom,
  v.numcom    AS numcomp,
  COALESCE(
    CASE 
      WHEN propc.idempresa IS NOT NULL THEN epc.nomcomercial
      WHEN propc.idpersona IS NOT NULL THEN CONCAT(ppc.nombres,' ',ppc.apellidos)
    END,
    'Sin propietario'
  ) AS propietario,
  COALESCE(
    CASE
      WHEN cte.idpersona IS NOT NULL THEN CONCAT(p.apellidos,' ',p.nombres)
      WHEN cte.idempresa IS NOT NULL THEN e.nomcomercial
    END,
    'Cliente anónimo'
  ) AS cliente,
  v.fechahora AS fecha,
  v.kilometraje,
  CONCAT(tv.tipov,' ',ma.nombre,' ',vh.color,' (',vh.placa,')') AS vehiculo,
  NULL AS producto,
  NULL AS cantidad,
  NULL AS precio,
  NULL AS descuento,
  NULL AS total_producto,
  sc.subcategoria      AS tiposervicio,
  se.servicio          AS nombreservicio,
  col.namuser          AS mecanico,
  dos.precio           AS precio_servicio,
  'servicio'           AS registro_tipo
FROM ventas v
LEFT JOIN propietarios prop       ON v.idpropietario = prop.idpropietario
LEFT JOIN clientes propc          ON prop.idcliente   = propc.idcliente
LEFT JOIN empresas epc            ON propc.idempresa  = epc.idempresa
LEFT JOIN personas ppc            ON propc.idpersona  = ppc.idpersona
LEFT JOIN clientes cte            ON v.idcliente      = cte.idcliente
LEFT JOIN personas p              ON cte.idpersona    = p.idpersona
LEFT JOIN empresas e              ON cte.idempresa    = e.idempresa
LEFT JOIN vehiculos vh            ON v.idvehiculo     = vh.idvehiculo
LEFT JOIN modelos m               ON vh.idmodelo      = m.idmodelo
LEFT JOIN tipovehiculos tv        ON m.idtipov        = tv.idtipov
LEFT JOIN marcas ma               ON m.idmarca        = ma.idmarca
LEFT JOIN ordenservicios os       ON v.idcliente      = os.idcliente
AND DATE(v.fechahora) = DATE(os.fechaingreso)
LEFT JOIN detalleordenservicios dos ON dos.idorden     = os.idorden
LEFT JOIN servicios se            ON se.idservicio    = dos.idservicio
LEFT JOIN subcategorias sc        ON sc.idsubcategoria= se.idsubcategoria
LEFT JOIN colaboradores col       ON col.idcolaborador= dos.idmecanico
WHERE v.estado = TRUE;
*/

-- ************************* VISTA DE AMORTIZACIÓN *************************
-- 1) VISTA PARA VER EL TOTAL DE LAS VENTAS (ID)
DROP VIEW IF EXISTS vista_total_por_venta;
CREATE VIEW vista_total_por_venta AS
SELECT
  idventa,
  ROUND(
    SUM(total_producto)
    + SUM(COALESCE(precio_servicio,0))
  , 2) AS total
FROM vista_detalle_venta
GROUP BY idventa;

-- 1) VISTA PARA VER EL TOTAL DE LAS COMPRAS (ID)
DROP VIEW IF EXISTS vista_total_por_compra;
CREATE VIEW vista_total_por_compra AS
SELECT
  dc.idcompra,
  ROUND(SUM((dc.preciocompra - dc.descuento) * dc.cantidad), 2) AS total
FROM detallecompra AS dc
GROUP BY dc.idcompra;

-- 2) VISTA PARA VER LAS AMORTIZACIONES DE CADA VENTA (ID)
DROP VIEW IF EXISTS vista_amortizaciones_por_venta;
CREATE VIEW vista_amortizaciones_por_venta AS
SELECT
  idventa,
  SUM(amortizacion) AS total_amortizado
FROM amortizaciones
GROUP BY idventa;

-- 2) VISTA PARA VER LAS AMORTIZACIONES DE CADA COMPRA (ID)
DROP VIEW IF EXISTS vista_amortizaciones_por_compra;
CREATE VIEW vista_amortizaciones_por_compra AS
SELECT
  idcompra,
  SUM(amortizacion) AS total_amortizado
FROM amortizaciones
WHERE idcompra IS NOT NULL
GROUP BY idcompra;

-- 3) VISTA PARA TRAER: (TOTAL VENTA) - (TOTAL PAGADO) - (PENDIENTE)
DROP VIEW IF EXISTS vista_saldos_por_venta;
CREATE VIEW vista_saldos_por_venta AS
SELECT
  v.idventa,
  COALESCE(CONCAT(p.nombres, ' ', p.apellidos), e.nomcomercial) AS cliente,
  vt.total AS total_original,
  COALESCE(a.total_amortizado, 0) AS total_pagado,
  vt.total - COALESCE(a.total_amortizado, 0) AS total_pendiente
FROM ventas AS v
LEFT JOIN clientes AS c ON v.idcliente = c.idcliente
LEFT JOIN personas AS p ON c.idpersona = p.idpersona
LEFT JOIN empresas AS e ON c.idempresa = e.idempresa
JOIN vista_total_por_venta AS vt ON v.idventa = vt.idventa
LEFT JOIN vista_amortizaciones_por_venta AS a ON v.idventa = a.idventa;

-- 3) VISTA PARA TRAER: (TOTAL COMPRA) - (TOTAL PAGADO) - (PENDIENTE)
DROP VIEW IF EXISTS vista_saldos_por_compra;
CREATE VIEW vista_saldos_por_compra AS
SELECT
  c.idcompra,
  e.nomcomercial AS proveedor,
  tc.total AS total_original,
  COALESCE(a.total_amortizado, 0) AS total_pagado,
  tc.total - COALESCE(a.total_amortizado, 0) AS total_pendiente
FROM compras AS c
JOIN proveedores AS pr ON c.idproveedor = pr.idproveedor
JOIN empresas AS e ON pr.idempresa = e.idempresa
JOIN vista_total_por_compra AS tc ON c.idcompra = tc.idcompra
LEFT JOIN vista_amortizaciones_por_compra AS a ON c.idcompra = a.idcompra;

-- 4) VISTA PARA OBTENER FORMA DE PAGO (idventa - idcompra)
DROP VIEW IF EXISTS vista_amortizaciones_con_formapago;
CREATE VIEW vista_amortizaciones_con_formapago AS
SELECT
  a.idamortizacion,
  a.idventa,
  a.idcompra,
  a.numtransaccion,
  a.amortizacion,
  a.saldo,
  a.creado,
  f.formapago
FROM amortizaciones AS a
LEFT JOIN formapagos AS f ON a.idformapago = f.idformapago;

-- ************************* VISTA DE VENTAS *************************

-- 1) VISTA DE VENTAS PARA LISTAR-VENTAS
/*
DROP VIEW IF EXISTS vs_ventas;
CREATE VIEW vs_ventas AS
SELECT
  v.idventa AS id,
  CASE
    WHEN c.idempresa IS NOT NULL THEN e.nomcomercial
    WHEN c.idpersona IS NOT NULL THEN CONCAT(p.nombres, ' ', p.apellidos)
  END AS cliente,
  CASE
    WHEN pc.idempresa IS NOT NULL THEN ep.nomcomercial
    WHEN pc.idpersona IS NOT NULL THEN CONCAT(pp.nombres, ' ', pp.apellidos)
  END AS propietario,
  v.tipocom,
  v.numcom,
  vt.total_pendiente
FROM ventas v
LEFT JOIN clientes c ON v.idcliente = c.idcliente
LEFT JOIN empresas e ON c.idempresa = e.idempresa
LEFT JOIN personas p ON c.idpersona = p.idpersona
LEFT JOIN vista_saldos_por_venta vt ON v.idventa = vt.idventa
LEFT JOIN propietarios prop ON v.idpropietario = prop.idpropietario
LEFT JOIN clientes pc ON prop.idcliente = pc.idcliente
LEFT JOIN empresas ep ON pc.idempresa = ep.idempresa
LEFT JOIN personas pp ON pc.idpersona = pp.idpersona
WHERE v.estado = TRUE;*/

DROP VIEW IF EXISTS vs_ventas;
CREATE VIEW vs_ventas AS
SELECT
  v.idventa AS id,
CASE
    WHEN c.idempresa IS NOT NULL THEN e.nomcomercial
    WHEN c.idpersona IS NOT NULL THEN CONCAT(p.nombres, ' ', p.apellidos)
  END AS cliente,
  CASE
    WHEN pc.idempresa IS NOT NULL THEN ep.nomcomercial
    WHEN pc.idpersona IS NOT NULL THEN CONCAT(pp.nombres, ' ', pp.apellidos)
  END AS propietario,
  v.tipocom,
  v.numcom,
  vt.total_pendiente,
  CASE
    WHEN vt.total_pendiente = 0 THEN 'pagado'
    ELSE 'pendiente'
  END AS estado_pago
FROM ventas v
LEFT JOIN clientes c ON v.idcliente = c.idcliente
LEFT JOIN empresas e ON c.idempresa = e.idempresa
LEFT JOIN personas p ON c.idpersona = p.idpersona
LEFT JOIN vista_saldos_por_venta vt ON v.idventa = vt.idventa
LEFT JOIN propietarios prop ON v.idpropietario = prop.idpropietario
LEFT JOIN clientes pc ON prop.idcliente = pc.idcliente
LEFT JOIN empresas ep ON pc.idempresa = ep.idempresa
LEFT JOIN personas pp ON pc.idpersona = pp.idpersona
WHERE v.estado = TRUE;

-- ************************* VISTA DE COMPRAS *************************

-- DETALLE DE COMPRA PARA EL MODAL POR CADA IDCOMPRA
DROP VIEW IF EXISTS vista_detalle_compra;
CREATE VIEW vista_detalle_compra AS
SELECT 
  c.idcompra,
  c.fechacompra,
  e.nomcomercial AS proveedor,
  CONCAT(s.subcategoria, ' ', pr.descripcion) AS producto,
  dc.preciocompra AS precio,
  dc.descuento,
  dc.cantidad,
  ROUND((dc.preciocompra - dc.descuento) * dc.cantidad, 2) AS total_producto
FROM compras c
JOIN proveedores prov ON c.idproveedor = prov.idproveedor
JOIN empresas e ON prov.idempresa = e.idempresa
JOIN detallecompra dc ON c.idcompra = dc.idcompra
JOIN productos pr ON dc.idproducto = pr.idproducto
JOIN subcategorias s ON pr.idsubcategoria = s.idsubcategoria
WHERE c.estado = TRUE;

-- VISTA DE COMPRAS PARA LISTAR-COMPRAS
DROP VIEW IF EXISTS vs_compras;
CREATE VIEW vs_compras AS
SELECT 
  C.idcompra AS id,
  C.tipocom,
  C.numserie,
  C.numcom,
  E.nomcomercial AS proveedores,
  VSPC.total_pendiente,
  CASE
    WHEN VSPC.total_pendiente = 0 THEN 'pagado'
    ELSE 'pendiente'
  END AS estado_pago
FROM compras C
JOIN proveedores P ON C.idproveedor = P.idproveedor
JOIN empresas E ON P.idempresa = E.idempresa
LEFT JOIN vista_saldos_por_compra VSPC ON C.idcompra = VSPC.idcompra
WHERE C.estado = TRUE;

-- ************************* VISTA DE COTIZACIÓN *************************

-- VISTA DE COTIZACIONES PARA LISTAR-COTIZACION
DROP VIEW IF EXISTS vs_cotizaciones;
CREATE VIEW vs_cotizaciones AS
SELECT 
  c.idcotizacion,
  CASE
    WHEN cli.idempresa IS NOT NULL THEN e.nomcomercial
    WHEN cli.idpersona IS NOT NULL THEN CONCAT(p.nombres, ' ', p.apellidos)
  END AS cliente,
  dc.precio,
  c.vigenciadias AS vigencia,
  c.fechahora    AS fechahora
FROM cotizaciones c
LEFT JOIN clientes cli ON c.idcliente = cli.idcliente
LEFT JOIN empresas e ON cli.idempresa = e.idempresa
LEFT JOIN personas p ON cli.idpersona = p.idpersona
JOIN detallecotizacion dc ON c.idcotizacion = dc.idcotizacion
WHERE c.estado = TRUE;

-- DETALLE DE COTIZACION PARA EL MODAL POR CADA IDCOTIZACIONDROP VIEW IF EXISTS vista_detalle_cotizacion;
-- REAL
DROP VIEW IF EXISTS vista_detalle_cotizacion;
CREATE VIEW vista_detalle_cotizacion AS
SELECT 
  c.idcotizacion,
  c.idcliente,
  COALESCE(CONCAT(p.nombres, ' ', p.apellidos), e.nomcomercial) AS cliente,
  dc.idproducto,                                            -- ← lo agregamos aquí
  CONCAT(S.subcategoria, ' ', pr.descripcion) AS producto,
  dc.precio,
  dc.cantidad,
  dc.descuento,
  ROUND(dc.precio * dc.cantidad * (1 - dc.descuento/100), 2) AS total_producto,
  c.fechahora      AS fechahora,
  c.vigenciadias   AS vigenciadias
FROM cotizaciones c
JOIN clientes cli      ON c.idcliente = cli.idcliente
LEFT JOIN personas p   ON cli.idpersona = p.idpersona
LEFT JOIN empresas e   ON cli.idempresa = e.idempresa
JOIN detallecotizacion dc ON c.idcotizacion = dc.idcotizacion
JOIN productos pr        ON dc.idproducto   = pr.idproducto
JOIN subcategorias S     ON pr.idsubcategoria = S.idsubcategoria
WHERE c.estado = TRUE;

/*DROP VIEW IF EXISTS vista_detalle_cotizacion;
CREATE VIEW vista_detalle_cotizacion AS
SELECT 
  c.idcotizacion,
  c.idcliente,
  COALESCE(CONCAT(p.nombres, ' ', p.apellidos), e.nomcomercial) AS cliente,
  CONCAT(S.subcategoria, ' ', pr.descripcion)           AS producto,
  dc.precio,
  dc.cantidad,
  dc.descuento,
  ROUND(dc.precio * dc.cantidad * (1 - dc.descuento/100), 2) AS total_producto,
  c.fechahora         AS fechahora,
  c.vigenciadias      AS vigenciadias
FROM cotizaciones c
JOIN clientes cli    ON c.idcliente   = cli.idcliente
LEFT JOIN personas p ON cli.idpersona  = p.idpersona
LEFT JOIN empresas e ON cli.idempresa  = e.idempresa
JOIN detallecotizacion dc ON c.idcotizacion = dc.idcotizacion
JOIN productos pr        ON dc.idproducto   = pr.idproducto
JOIN subcategorias S     ON pr.idsubcategoria = S.idsubcategoria
WHERE c.estado = TRUE;*/

/*
DROP VIEW IF EXISTS vista_detalle_cotizacion;
CREATE VIEW vista_detalle_cotizacion AS
SELECT 
  c.idcotizacion,
  c.idcliente,
  dc.idproducto,
  COALESCE(CONCAT(p.nombres, ' ', p.apellidos), e.nomcomercial) AS cliente,
  CONCAT(S.subcategoria, ' ', pr.descripcion) AS producto,
  dc.precio,
  dc.cantidad,
  dc.descuento
FROM cotizaciones c
JOIN clientes cli ON c.idcliente = cli.idcliente
LEFT JOIN personas p ON cli.idpersona = p.idpersona
LEFT JOIN empresas e ON cli.idempresa = e.idempresa
JOIN detallecotizacion dc ON c.idcotizacion = dc.idcotizacion
JOIN productos pr ON dc.idproducto = pr.idproducto
INNER JOIN subcategorias S ON pr.idsubcategoria = S.idsubcategoria
WHERE c.estado = TRUE;
*/
-- ************************* VISTAS ELIMINADAS *************************

-- VISTA DE VENTAS ELIMINADAS
DROP VIEW IF EXISTS vs_ventas_eliminadas;
CREATE VIEW vs_ventas_eliminadas AS
SELECT 
  V.idventa,
  COALESCE(
    CASE 
      WHEN C.idempresa IS NOT NULL THEN E.nomcomercial 
      WHEN C.idpersona IS NOT NULL THEN CONCAT(P.nombres, ' ', P.apellidos)
    END,
    'Sin cliente'
  ) AS cliente,
  V.tipocom,
  V.numcom
FROM ventas V
-- 1) Usamos LEFT JOIN con idpropietario, de modo que incluso 
LEFT JOIN clientes C ON V.idpropietario = C.idcliente
LEFT JOIN empresas E ON C.idempresa    = E.idempresa
LEFT JOIN personas P ON C.idpersona    = P.idpersona
WHERE V.estado = FALSE;

-- DETALLE DE VENTA ELIMINADA
DROP VIEW IF EXISTS vista_detalle_venta_eliminada;
CREATE VIEW vista_detalle_venta_eliminada AS

-- 1) Productos anulados
SELECT
  v.idventa,
  v.fechahora,

  -- Cliente
  COALESCE(
    CASE 
      WHEN cli.idempresa IS NOT NULL THEN emp.nomcomercial
      WHEN cli.idpersona IS NOT NULL THEN CONCAT(per.apellidos, ' ', per.nombres)
    END,
    'Sin cliente'
  ) AS cliente,

  -- Propietario
  COALESCE(
    CASE 
      WHEN cte.idempresa IS NOT NULL THEN e.nomcomercial
      WHEN cte.idpersona IS NOT NULL THEN CONCAT(p.apellidos, ' ', p.nombres)
    END,
    'Sin propietario'
  ) AS propietario,

  v.kilometraje,
  CONCAT(tv.tipov, ' ', ma.nombre, ' ', vh.color, ' (', vh.placa, ')') AS vehiculo,

  -- Detalles de producto
  CONCAT(su.subcategoria, ' ', pr.descripcion) AS producto,
  dv.cantidad,
  dv.precioventa AS precio,
  dv.descuento,
  ROUND((dv.precioventa - dv.descuento) * dv.cantidad, 2) AS total_producto,

  -- Campos nulos para servicio
  NULL AS tiposervicio,
  NULL AS nombreservicio,
  NULL AS mecanico,
  NULL AS precio_servicio,

  'producto' AS registro_tipo

FROM ventas v
LEFT JOIN clientes cte    ON v.idpropietario = cte.idcliente
LEFT JOIN personas p      ON cte.idpersona   = p.idpersona
LEFT JOIN empresas e      ON cte.idempresa   = e.idempresa

LEFT JOIN clientes cli    ON v.idcliente     = cli.idcliente
LEFT JOIN personas per    ON cli.idpersona   = per.idpersona
LEFT JOIN empresas emp    ON cli.idempresa   = emp.idempresa

LEFT JOIN vehiculos vh    ON v.idvehiculo    = vh.idvehiculo
LEFT JOIN modelos m       ON vh.idmodelo     = m.idmodelo
LEFT JOIN tipovehiculos tv ON m.idtipov      = tv.idtipov
LEFT JOIN marcas ma       ON m.idmarca       = ma.idmarca

JOIN detalleventa dv      ON v.idventa       = dv.idventa
JOIN productos pr         ON dv.idproducto   = pr.idproducto
JOIN subcategorias su     ON pr.idsubcategoria = su.idsubcategoria
WHERE v.estado = FALSE

UNION ALL

-- 2) Servicios anulados
SELECT
  v.idventa,
  v.fechahora,

  -- Cliente
  COALESCE(
    CASE 
      WHEN cli.idempresa IS NOT NULL THEN emp.nomcomercial
      WHEN cli.idpersona IS NOT NULL THEN CONCAT(per.apellidos, ' ', per.nombres)
    END,
    'Sin cliente'
  ) AS cliente,

  -- Propietario
  COALESCE(
    CASE 
      WHEN cte.idempresa IS NOT NULL THEN e.nomcomercial
      WHEN cte.idpersona IS NOT NULL THEN CONCAT(p.apellidos, ' ', p.nombres)
    END,
    'Sin propietario'
  ) AS propietario,

  v.kilometraje,
  CONCAT(tv.tipov, ' ', ma.nombre, ' ', vh.color, ' (', vh.placa, ')') AS vehiculo,

  -- Campos nulos para producto
  NULL AS producto,
  NULL AS cantidad,
  NULL AS precio,
  NULL AS descuento,
  NULL AS total_producto,

  -- Detalles de servicio
  sc.subcategoria AS tiposervicio,
  se.servicio AS nombreservicio,
  col.namuser AS mecanico,
  dos.precio AS precio_servicio,

  'servicio' AS registro_tipo

FROM ventas v
LEFT JOIN clientes cte    ON v.idpropietario = cte.idcliente
LEFT JOIN personas p      ON cte.idpersona   = p.idpersona
LEFT JOIN empresas e      ON cte.idempresa   = e.idempresa

LEFT JOIN clientes cli    ON v.idcliente     = cli.idcliente
LEFT JOIN personas per    ON cli.idpersona   = per.idpersona
LEFT JOIN empresas emp    ON cli.idempresa   = emp.idempresa

LEFT JOIN vehiculos vh    ON v.idvehiculo    = vh.idvehiculo
LEFT JOIN modelos m       ON vh.idmodelo     = m.idmodelo
LEFT JOIN tipovehiculos tv ON m.idtipov      = tv.idtipov
LEFT JOIN marcas ma       ON m.idmarca       = ma.idmarca

-- ✅ Corrección aquí: unión directa con idorden
LEFT JOIN ordenservicios os          ON v.idexpediente_ot = os.idorden
LEFT JOIN detalleordenservicios dos ON os.idorden        = dos.idorden
LEFT JOIN servicios se              ON dos.idservicio    = se.idservicio
LEFT JOIN subcategorias sc          ON se.idsubcategoria = sc.idsubcategoria
LEFT JOIN colaboradores col         ON dos.idmecanico    = col.idcolaborador

WHERE v.estado = FALSE
  AND dos.idorden IS NOT NULL;
  
/* 
-- sin servicios
DROP VIEW IF EXISTS vista_detalle_venta_eliminada;
CREATE VIEW vista_detalle_venta_eliminada AS
-- Filas de productos
SELECT
  v.idventa,
  v.fechahora,

  -- Cliente
  COALESCE(
    CASE 
      WHEN cli.idempresa IS NOT NULL THEN emp.nomcomercial
      WHEN cli.idpersona IS NOT NULL THEN CONCAT(per.apellidos, ' ', per.nombres)
    END,
    'Sin cliente'
  ) AS cliente,

  -- Propietario
  COALESCE(
    CASE 
      WHEN cte.idempresa IS NOT NULL THEN e.nomcomercial
      WHEN cte.idpersona IS NOT NULL THEN CONCAT(p.apellidos, ' ', p.nombres)
    END,
    'Sin propietario'
  ) AS propietario,

  v.kilometraje,
  CONCAT(tv.tipov, ' ', ma.nombre, ' ', vh.color, ' (', vh.placa, ')') AS vehiculo,

  -- Detalles de producto
  CONCAT(su.subcategoria, ' ', pr.descripcion) AS producto,
  dv.cantidad,
  dv.precioventa AS precio,
  dv.descuento,
  ROUND((dv.precioventa - dv.descuento) * dv.cantidad, 2) AS total_producto,

  -- Campos nulos para servicio
  NULL AS tiposervicio,
  NULL AS nombreservicio,
  NULL AS mecanico,
  NULL AS precio_servicio,

  'producto' AS registro_tipo

FROM ventas v
LEFT JOIN clientes cte    ON v.idpropietario = cte.idcliente
LEFT JOIN personas p      ON cte.idpersona   = p.idpersona
LEFT JOIN empresas e      ON cte.idempresa   = e.idempresa

LEFT JOIN clientes cli    ON v.idcliente     = cli.idcliente
LEFT JOIN personas per    ON cli.idpersona   = per.idpersona
LEFT JOIN empresas emp    ON cli.idempresa   = emp.idempresa

LEFT JOIN vehiculos vh    ON v.idvehiculo    = vh.idvehiculo
LEFT JOIN modelos m       ON vh.idmodelo     = m.idmodelo
LEFT JOIN tipovehiculos tv ON m.idtipov      = tv.idtipov
LEFT JOIN marcas ma       ON m.idmarca       = ma.idmarca

JOIN detalleventa dv      ON v.idventa       = dv.idventa
JOIN productos pr         ON dv.idproducto   = pr.idproducto
JOIN subcategorias su     ON pr.idsubcategoria = su.idsubcategoria
WHERE v.estado = FALSE

UNION ALL

-- Filas de servicios
SELECT
  v.idventa,
  v.fechahora,

  -- Cliente
  COALESCE(
    CASE 
      WHEN cli.idempresa IS NOT NULL THEN emp.nomcomercial
      WHEN cli.idpersona IS NOT NULL THEN CONCAT(per.apellidos, ' ', per.nombres)
    END,
    'Sin cliente'
  ) AS cliente,

  -- Propietario
  COALESCE(
    CASE 
      WHEN cte.idempresa IS NOT NULL THEN e.nomcomercial
      WHEN cte.idpersona IS NOT NULL THEN CONCAT(p.apellidos, ' ', p.nombres)
    END,
    'Sin propietario'
  ) AS propietario,

  v.kilometraje,
  CONCAT(tv.tipov, ' ', ma.nombre, ' ', vh.color, ' (', vh.placa, ')') AS vehiculo,

  -- Campos nulos para producto
  NULL AS producto,
  NULL AS cantidad,
  NULL AS precio,
  NULL AS descuento,
  NULL AS total_producto,

  -- Detalles de servicio
  sc.subcategoria AS tiposervicio,
  se.servicio AS nombreservicio,
  col.namuser AS mecanico,
  dos.precio AS precio_servicio,

  'servicio' AS registro_tipo

FROM ventas v
LEFT JOIN clientes cte    ON v.idpropietario = cte.idcliente
LEFT JOIN personas p      ON cte.idpersona   = p.idpersona
LEFT JOIN empresas e      ON cte.idempresa   = e.idempresa

LEFT JOIN clientes cli    ON v.idcliente     = cli.idcliente
LEFT JOIN personas per    ON cli.idpersona   = per.idpersona
LEFT JOIN empresas emp    ON cli.idempresa   = emp.idempresa

LEFT JOIN vehiculos vh    ON v.idvehiculo    = vh.idvehiculo
LEFT JOIN modelos m       ON vh.idmodelo     = m.idmodelo
LEFT JOIN tipovehiculos tv ON m.idtipov      = tv.idtipov
LEFT JOIN marcas ma       ON m.idmarca       = ma.idmarca

LEFT JOIN ordenservicios os
  ON v.idpropietario = os.idcliente
  AND DATE(v.fechahora) = DATE(os.fechaingreso)

LEFT JOIN detalleordenservicios dos ON os.idorden = dos.idorden
LEFT JOIN servicios se              ON dos.idservicio = se.idservicio
LEFT JOIN subcategorias sc          ON se.idsubcategoria = sc.idsubcategoria
LEFT JOIN colaboradores col         ON dos.idmecanico = col.idcolaborador
WHERE v.estado = FALSE;
*/

/*SELECT 
  v.idventa,
  v.fechahora,
  COALESCE(CONCAT(p.apellidos, ' ', p.nombres), e.nomcomercial) AS cliente,
  v.kilometraje,
  CONCAT(tv.tipov, ' ', ma.nombre, ' ', vh.color, ' (', vh.placa, ')') AS vehiculo,
  CONCAT(s.subcategoria, ' ', pr.descripcion) AS producto,
  dv.cantidad,
  dv.precioventa AS precio,
  dv.descuento,
  ROUND((dv.precioventa - dv.descuento) * dv.cantidad, 2) AS total_producto
FROM ventas v
JOIN clientes c        ON v.idcliente      = c.idcliente
LEFT JOIN personas p   ON c.idpersona      = p.idpersona
LEFT JOIN empresas e   ON c.idempresa      = e.idempresa
LEFT JOIN vehiculos vh ON v.idvehiculo     = vh.idvehiculo
LEFT JOIN modelos m    ON vh.idmodelo      = m.idmodelo
LEFT JOIN tipovehiculos tv ON m.idtipov    = tv.idtipov
LEFT JOIN marcas ma    ON m.idmarca        = ma.idmarca
JOIN detalleventa dv   ON v.idventa        = dv.idventa
JOIN productos pr      ON dv.idproducto    = pr.idproducto
JOIN subcategorias s   ON pr.idsubcategoria = s.idsubcategoria
WHERE v.estado = FALSE;*/

-- JUSTIFICACIÓN DE VENTA
DROP VIEW IF EXISTS vista_justificacion_venta;
CREATE VIEW vista_justificacion_venta AS
SELECT 
  idventa,
  justificacion
FROM ventas
WHERE estado = FALSE;

-- VISTA DE COMPRAS ELIMINADAS
DROP VIEW IF EXISTS vs_compras_eliminadas;
CREATE VIEW vs_compras_eliminadas AS
SELECT 
  C.idcompra AS id,
  E.nomcomercial AS proveedor,
  C.tipocom,
  C.numcom
FROM compras C
JOIN proveedores P ON C.idproveedor = P.idproveedor
JOIN empresas E ON P.idempresa = E.idempresa
WHERE C.estado = FALSE;

-- JUSTIFICACIÓN DE COMPRA
DROP VIEW IF EXISTS vista_justificacion_compra;
CREATE VIEW vista_justificacion_compra AS
SELECT 
  idcompra,
  justificacion
FROM compras
WHERE estado = FALSE;

-- DETALLE DE COMPRA ELIMINADA
DROP VIEW IF EXISTS vista_detalle_compra_eliminada;
CREATE VIEW vista_detalle_compra_eliminada AS
SELECT 
  c.idcompra,
  e.nomcomercial AS proveedor,
  c.fechacompra,
  CONCAT(s.subcategoria, ' ', pr.descripcion) AS producto,
  dc.cantidad,
  dc.preciocompra AS precio,
  dc.descuento,
  ROUND((dc.preciocompra - dc.descuento) * dc.cantidad, 2) AS total_producto
FROM compras c
JOIN proveedores prov ON c.idproveedor = prov.idproveedor
JOIN empresas e ON prov.idempresa = e.idempresa
JOIN detallecompra dc ON c.idcompra = dc.idcompra
JOIN productos pr ON dc.idproducto = pr.idproducto
JOIN subcategorias s ON pr.idsubcategoria = s.idsubcategoria
WHERE c.estado = FALSE;

-- VISTA DE COTIZACIONES ELIMINADAS
DROP VIEW IF EXISTS vs_cotizaciones_eliminadas;
CREATE VIEW vs_cotizaciones_eliminadas AS
SELECT 
  c.idcotizacion,
  CASE
    WHEN cli.idempresa IS NOT NULL THEN e.nomcomercial
    WHEN cli.idpersona IS NOT NULL THEN CONCAT(p.nombres, ' ', p.apellidos)
  END AS cliente,
  SUM(dc.precio) AS total,
  c.vigenciadias AS vigencia,
  c.fechahora
FROM cotizaciones c
LEFT JOIN clientes cli ON c.idcliente = cli.idcliente
LEFT JOIN empresas e ON cli.idempresa = e.idempresa
LEFT JOIN personas p ON cli.idpersona = p.idpersona
JOIN detallecotizacion dc ON c.idcotizacion = dc.idcotizacion
WHERE c.estado = FALSE;

-- DETALLE DE COTIZACIÓN ELIMINADA
DROP VIEW IF EXISTS vista_detalle_cotizacion_eliminada;
CREATE VIEW vista_detalle_cotizacion_eliminada AS
SELECT 
  c.idcotizacion,
  c.idcliente,
  COALESCE(CONCAT(p.nombres, ' ', p.apellidos), e.nomcomercial) AS cliente,
  CONCAT(S.subcategoria, ' ', pr.descripcion)           AS producto,
  dc.precio,
  dc.cantidad,
  dc.descuento,
  ROUND(dc.precio * dc.cantidad * (1 - dc.descuento/100), 2) AS total_producto,
  c.fechahora         AS fechahora,
  c.vigenciadias      AS vigenciadias
FROM cotizaciones c
JOIN clientes cli ON c.idcliente = cli.idcliente
LEFT JOIN personas p ON cli.idpersona = p.idpersona
LEFT JOIN empresas e ON cli.idempresa = e.idempresa
JOIN detallecotizacion dc ON c.idcotizacion = dc.idcotizacion
JOIN productos pr ON dc.idproducto = pr.idproducto
JOIN subcategorias s ON pr.idsubcategoria = s.idsubcategoria
WHERE c.estado = FALSE;

-- JUSTIFICACIÓN DE COTIZACIÓN
DROP VIEW IF EXISTS vista_justificacion_cotizacion;
CREATE VIEW vista_justificacion_cotizacion AS
SELECT 
  idcotizacion,
  justificacion
FROM cotizaciones
WHERE estado = FALSE;

-- ************************* VISTA DE ARQUEO DE CAJA *************************

-- VISTA PARA VER LOS INGRESOS
DROP VIEW IF EXISTS vista_formapagos;
CREATE VIEW vista_formapagos AS
SELECT
  idformapago,
  formapago
FROM formapagos;

-- VISTA PARA VER LOS EGRESOS
CREATE OR REPLACE VIEW vista_conceptos_egresos AS
SELECT 'almuerzo' AS concepto
UNION ALL SELECT 'combustible'
UNION ALL SELECT 'compra de insumos'
UNION ALL SELECT 'otros conceptos'
UNION ALL SELECT 'pasajes'
UNION ALL SELECT 'servicios varios';

-- VISTA PARA EL RESUMEN Y SALDO RESTANTE
DROP VIEW IF EXISTS vista_resumen_arqueo;
CREATE VIEW vista_resumen_arqueo AS
SELECT
  f.fecha,
  COALESCE(
    (SELECT SUM(a.amortizacion) FROM amortizaciones a WHERE a.idventa IS NOT NULL AND DATE(a.creado) < f.fecha),
    0
  )
  -
  COALESCE(
    (SELECT SUM(e.monto) FROM egresos e JOIN vista_conceptos_egresos c ON e.concepto = c.concepto WHERE DATE(e.creado) < f.fecha),
    0
  ) AS saldo_anterior,
  COALESCE(
    (SELECT SUM(a.amortizacion) FROM amortizaciones a WHERE a.idventa IS NOT NULL AND DATE(a.creado) = f.fecha),
    0
  ) AS ingreso_efectivo,
  COALESCE(
    (SELECT SUM(e.monto) FROM egresos e JOIN vista_conceptos_egresos c ON e.concepto = c.concepto WHERE DATE(e.creado) = f.fecha),
    0
  ) AS total_egresos,
  (
    COALESCE((SELECT SUM(a.amortizacion) FROM amortizaciones a WHERE a.idventa IS NOT NULL AND DATE(a.creado) < f.fecha), 0)
    - COALESCE((SELECT SUM(e.monto) FROM egresos e JOIN vista_conceptos_egresos c ON e.concepto = c.concepto WHERE DATE(e.creado) < f.fecha), 0)
  )
  +
  COALESCE((SELECT SUM(a.amortizacion) FROM amortizaciones a WHERE a.idventa IS NOT NULL AND DATE(a.creado) = f.fecha), 0)
  AS total_efectivo,
  GREATEST(
    (
      (
        COALESCE((SELECT SUM(a.amortizacion) FROM amortizaciones a WHERE a.idventa IS NOT NULL AND DATE(a.creado) < f.fecha), 0)
        - COALESCE((SELECT SUM(e.monto) FROM egresos e JOIN vista_conceptos_egresos c ON e.concepto = c.concepto WHERE DATE(e.creado) < f.fecha), 0)
      )
      +
      COALESCE((SELECT SUM(a.amortizacion) FROM amortizaciones a WHERE a.idventa IS NOT NULL AND DATE(a.creado) = f.fecha), 0)
    )
    - COALESCE((SELECT SUM(e.monto) FROM egresos e JOIN vista_conceptos_egresos c ON e.concepto = c.concepto WHERE DATE(e.creado) = f.fecha), 0)
  , 0) AS total_caja
FROM (
  SELECT DATE(creado) AS fecha FROM amortizaciones
  UNION
  SELECT DATE(creado) AS fecha FROM egresos
) AS f
ORDER BY f.fecha;


/*DROP VIEW IF EXISTS vista_detalle_venta;
CREATE VIEW vista_detalle_venta AS
SELECT 
  v.idventa,
  v.fechahora,
  COALESCE(CONCAT(p.apellidos, ' ', p.nombres), e.nomcomercial) AS cliente,
  v.kilometraje,
  CONCAT(tv.tipov, ' ', ma.nombre, ' ', vh.color, ' (', vh.placa, ')') AS vehiculo,
  CONCAT(su.subcategoria, ' ', pr.descripcion) AS producto,
  dv.cantidad,
  dv.precioventa AS precio,
  dv.descuento,
  ROUND((dv.precioventa - dv.descuento) * dv.cantidad, 2) AS total_producto,
  sc.subcategoria AS tiposervicio,
  se.servicio AS nombreservicio,
  c.namuser AS mecanico,
  dos.precio AS precio_servicio
FROM ventas v
JOIN clientes cte        ON v.idcliente      = cte.idcliente
LEFT JOIN personas p     ON cte.idpersona    = p.idpersona
LEFT JOIN empresas e     ON cte.idempresa    = e.idempresa
LEFT JOIN vehiculos vh   ON v.idvehiculo     = vh.idvehiculo
LEFT JOIN modelos m      ON vh.idmodelo      = m.idmodelo
LEFT JOIN tipovehiculos tv ON m.idtipov      = tv.idtipov
LEFT JOIN marcas ma      ON m.idmarca        = ma.idmarca
JOIN detalleventa dv     ON v.idventa        = dv.idventa
JOIN productos pr        ON dv.idproducto    = pr.idproducto
JOIN subcategorias su    ON pr.idsubcategoria = su.idsubcategoria
LEFT JOIN ordenservicios os      ON v.idcliente = os.idcliente AND DATE(v.fechahora) = DATE(os.fechaingreso)
LEFT JOIN detalleordenservicios dos ON os.idorden = dos.idorden
LEFT JOIN servicios se           ON dos.idservicio = se.idservicio
LEFT JOIN subcategorias sc       ON se.idsubcategoria = sc.idsubcategoria
LEFT JOIN colaboradores c        ON dos.idmecanico = c.idcolaborador
WHERE v.estado = TRUE;*/

/*
DROP VIEW IF EXISTS vista_detalle_venta;
CREATE VIEW vista_detalle_venta AS
SELECT
  v.idventa,
  v.fechahora,
  COALESCE(
    CASE 
      WHEN propc.idempresa IS NOT NULL THEN epc.nomcomercial
      WHEN propc.idpersona IS NOT NULL THEN CONCAT(ppc.nombres,' ',ppc.apellidos)
    END,
    'Sin propietario'
  ) AS propietario,
  COALESCE(CONCAT(p.apellidos,' ',p.nombres), e.nomcomercial) AS cliente,
  v.kilometraje,
  CONCAT(tv.tipov,' ',ma.nombre,' ',vh.color,' (',vh.placa,')') AS vehiculo,
  CONCAT(su.subcategoria,' ', pr.descripcion) AS producto,
  dv.cantidad,
  dv.precioventa         AS precio,
  dv.descuento,
  ROUND((dv.precioventa - dv.descuento) * dv.cantidad, 2) AS total_producto,
  NULL                   AS tiposervicio,
  NULL                   AS nombreservicio,
  NULL                   AS mecanico,
  NULL                   AS precio_servicio,
  'producto'             AS registro_tipo
FROM ventas v
  -- Propietario
  LEFT JOIN propietarios prop     ON v.idpropietario = prop.idpropietario
  LEFT JOIN clientes propc       ON prop.idcliente   = propc.idcliente
  LEFT JOIN empresas epc         ON propc.idempresa  = epc.idempresa
  LEFT JOIN personas ppc         ON propc.idpersona  = ppc.idpersona
  -- Cliente
  LEFT JOIN clientes cte         ON v.idcliente      = cte.idcliente
  LEFT JOIN personas p           ON cte.idpersona    = p.idpersona
  LEFT JOIN empresas e           ON cte.idempresa    = e.idempresa
  -- Vehículo
  LEFT JOIN vehiculos vh         ON v.idvehiculo     = vh.idvehiculo
  LEFT JOIN modelos m            ON vh.idmodelo      = m.idmodelo
  LEFT JOIN tipovehiculos tv     ON m.idtipov        = tv.idtipov
  LEFT JOIN marcas ma            ON m.idmarca        = ma.idmarca
  -- Detalle de productos
  JOIN detalleventa dv           ON v.idventa        = dv.idventa
  JOIN productos pr              ON dv.idproducto    = pr.idproducto
  JOIN subcategorias su          ON pr.idsubcategoria = su.idsubcategoria
WHERE v.estado = TRUE

UNION ALL

-- === BLOQUE de SERVICIOS ===
SELECT
  v.idventa,
  v.fechahora,
  COALESCE(
    CASE 
      WHEN propc.idempresa IS NOT NULL THEN epc.nomcomercial
      WHEN propc.idpersona IS NOT NULL THEN CONCAT(ppc.nombres,' ',ppc.apellidos)
    END,
    'Sin propietario'
  ) AS propietario,
  COALESCE(CONCAT(p.apellidos,' ',p.nombres), e.nomcomercial) AS cliente,
  v.kilometraje,
  CONCAT(tv.tipov,' ',ma.nombre,' ',vh.color,' (',vh.placa,')') AS vehiculo,
  NULL                   AS producto,
  NULL                   AS cantidad,
  NULL                   AS precio,
  NULL                   AS descuento,
  NULL                   AS total_producto,
  sc.subcategoria        AS tiposervicio,
  se.servicio            AS nombreservicio,
  col.namuser            AS mecanico,
  dos.precio             AS precio_servicio,
  'servicio'             AS registro_tipo
FROM ventas v
  -- Propietario
  LEFT JOIN propietarios prop     ON v.idpropietario = prop.idpropietario
  LEFT JOIN clientes propc       ON prop.idcliente   = propc.idcliente
  LEFT JOIN empresas epc         ON propc.idempresa  = epc.idempresa
  LEFT JOIN personas ppc         ON propc.idpersona  = ppc.idpersona
  -- Cliente
  LEFT JOIN clientes cte         ON v.idcliente      = cte.idcliente
  LEFT JOIN personas p           ON cte.idpersona    = p.idpersona
  LEFT JOIN empresas e           ON cte.idempresa    = e.idempresa
  -- Vehículo
  LEFT JOIN vehiculos vh         ON v.idvehiculo     = vh.idvehiculo
  LEFT JOIN modelos m            ON vh.idmodelo      = m.idmodelo
  LEFT JOIN tipovehiculos tv     ON m.idtipov        = tv.idtipov
  LEFT JOIN marcas ma            ON m.idmarca        = ma.idmarca
  -- Unir directamente a la OT asociada a la venta
  INNER JOIN ordenservicios os      ON v.idexpediente_ot = os.idorden
  INNER JOIN detalleordenservicios dos ON os.idorden    = dos.idorden
  INNER JOIN servicios se           ON dos.idservicio    = se.idservicio
  INNER JOIN subcategorias sc       ON se.idsubcategoria = sc.idsubcategoria
  INNER JOIN colaboradores col      ON dos.idmecanico    = col.idcolaborador
WHERE v.estado = TRUE
  AND v.idexpediente_ot IS NOT NULL;*/