Para soportar el flujo de “órdenes de trabajo” con cobros parciales y luego la emisión de una boleta final, necesitas en esencia dos cosas:

1. **Un mecanismo de agrupación** para que todas las órdenes de trabajo parciales queden asociadas a un mismo “contrato” o “expediente” de OT.
2. **Una señalización en la tabla `ventas`** que distinga las OT parciales de la boleta definitiva, y que relacione cada registro de OT parcial con ese expediente.

---

## 1. Crear una tabla de “Expedientes de OT”

Esta tabla agrupa todos los registros parciales de OT de un cliente/vehículo:

```sql
CREATE TABLE expediente_ot (
  idexpediente    INT PRIMARY KEY AUTO_INCREMENT,
  idcliente       INT NOT NULL,
  idvehiculo      INT NOT NULL,
  idcotizacion    INT NULL,       -- opcional: enlazar la cotización original
  fecha_apertura  DATETIME DEFAULT CURRENT_TIMESTAMP,
  estado          ENUM('ABIERTA','CERRADA') NOT NULL DEFAULT 'ABIERTA',
  total_estimado  DECIMAL(10,2)   NULL,  -- por si quieres guardar el monto inicialmente presupuestado
  CONSTRAINT fk_exp_ot_cliente FOREIGN KEY (idcliente)  REFERENCES clientes(idcliente),
  CONSTRAINT fk_exp_ot_vehiculo FOREIGN KEY (idvehiculo) REFERENCES vehiculos(idvehiculo)
) ENGINE=INNODB;
```

* **`idexpediente`**: identifica el “contrato” de OT.
* **`estado`**: ‘ABIERTA’ mientras siga habiendo pagos parciales; pasará a ‘CERRADA’ cuando se emita la boleta final.

---

## 2. Enlazar cada OT parcial con el expediente

Alteramos la tabla `ventas` para:

* Añadir la FK a `expediente_ot`.
* Mantener `tipocom = 'orden de trabajo'` para parciales y tipocom = 'boleta' para la boleta final.
* Ajustar el unique key para permitir múltiples OT parciales en el mismo expediente.

```sql
ALTER TABLE ventas
  ADD COLUMN idexpediente_ot INT           NULL AFTER idcliente,
  ADD CONSTRAINT fk_venta_expediente_ot FOREIGN KEY (idexpediente_ot)
    REFERENCES expediente_ot(idexpediente);

-- Eliminamos la restricción UNIQUE actual sobre (idcliente, tipocom, numserie, numcom)
ALTER TABLE ventas DROP INDEX uq_venta;

-- Creamos un nuevo índice que permita múltiples OT en un mismo expediente
ALTER TABLE ventas 
  ADD CONSTRAINT uq_venta_expediente UNIQUE (idexpediente_ot, tipocom, numserie, numcom);
```

Con esto:

* **Órdenes de trabajo parciales**:

  * Se insertan en `ventas` con `tipocom = 'orden de trabajo'` y `idexpediente_ot = X`.
  * Cada pago parcial genera una fila distinta en `ventas`, enlazada al mismo `idexpediente_ot`.
* **Boleta final**:

  * Cuando la suma de amortizaciones alcance el monto total acumulado en el expediente, cerramos el expediente (`estado = 'CERRADA'`) y generamos en `ventas` una fila adicional con `tipocom = 'boleta'`, misma FK `idexpediente_ot = X`.

---

## 3. Lógica de control

1. **Apertura de expediente**

   * Al primer adelanto:

     ```sql
     INSERT INTO expediente_ot (idcliente, idvehiculo, idcotizacion, total_estimado)
     VALUES ( :idcliente, :idvehiculo, :idcotizacion, :total );
     ```
   * Luego insertas la primera OT parcial en `ventas` referenciando el nuevo `idexpediente_ot`.

2. **Cada OT parcial**

   * Insertar en `ventas`:

     ```sql
     INSERT INTO ventas (
       idexpediente_ot, idcliente, idcolaborador, tipocom, numserie, numcom, moneda, kilometraje, justificacion
     ) VALUES (
       :idexp, :idcliente, :idcolab, 'orden de trabajo', :serie, :comprobante, :moneda, :km, :obs
     );
     ```
   * Registrar amortización en `amortizaciones` con `idventa` apuntando a esta fila.

3. **Cierre del expediente y boleta final**

   * Calcular suma de todas las OT parciales (montos en `detalleventa` o en amortizaciones).
   * Si `suma_amortizaciones >= total_estimado`, entonces:

     ```sql
     UPDATE expediente_ot
       SET estado = 'CERRADA'
     WHERE idexpediente = :idexp;

     INSERT INTO ventas (
       idexpediente_ot, idcliente, idcolaborador, tipocom, numserie, numcom, moneda, kilometraje, justificacion
     ) VALUES (
       :idexp, :idcliente, :idcolab, 'boleta', :serie_boleta, :com_boleta, :moneda, :km, 'Boleta final OT'
     );
     ```
   * Registrar en `detalleventa` el detalle completo de todos los servicios/productos acumulados.

---

### Ventajas de este enfoque

* **Historial completo**: quedan todas las OT parciales con su fecha, comprobante y amortizaciones.
* **Agrupación clara**: el expediente OT actúa de “cabecera” para consultas y reportes.
* **Boleta definitiva**: se genera solo una vez que el expediente cierra, mostrando en su detalle TODO lo realizado.

Con estos cambios estructurales en la base de datos tendrás soporte pleno para el flujo pedido, sin romper las referencias ni los informes de caja ya existentes.
