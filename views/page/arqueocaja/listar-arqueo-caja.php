<?php

const NAMEVIEW = "Arqueo de caja";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";
?>
            <div class="container-main">
                <div class="header-group"  style="padding-left:0px;">
                    <h2 class="title">ARQUEO DE CAJA - FIX 360</h2>
                    <button class="btn btn-danger btn-sm" style="height: 50px;">
                        <i class="fa-solid fa-file-pdf"></i>
                    </button>
                </div>
                <table>
                    <tr>
                        <td><strong>Presentado por</strong></td>
                        <td class="underline"><input type="text" value="Elena Castilla" class="editable-input" readonly></td>
                    </tr>
                    <tr>
                        <td><strong>Fecha</strong></td>
                        <td class="underline"><input type="date" id="fecha" value="2025-03-24" class="editable-input" onchange="actualizarValores()"></td>
                    </tr>
                    <tr>
                        <td><strong>Hora inicio</strong></td>
                        <td class="underline"><input type="text" value="08:00" class="editable-input" readonly></td>
                    </tr>
                    <tr>
                        <td><strong>Hora cierre</strong></td>
                        <td class="underline"><input type="text" value="18:00" class="editable-input" readonly></td>
                    </tr>
                </table>
        
                <!-- Contenedor adicional para centrar el div spacing -->
                <div class="spacing-container">
                    <div class="spacing">
                        <h3>Saldo Inicial</h3>
                        <div class="input-container">
                            <div class="label-container">
                                <label>Saldo restante</label>
                            </div>
                            <div class="input-field">
                                <input type="text" id="saldo-restante" value="S/ 385.00" readonly class="align-right">
                            </div>
                        </div>
        
                        <h3>Ingresos</h3>
                        <div class="input-container">
                            <div class="label-container">
                                <label>Efectivo</label>
                            </div>
                            <div class="input-field">
                                <input type="text" id="ingreso-efectivo" value="S/ 50.00" readonly class="align-right">
                            </div>
                        </div>
                        <div class="input-container">
                            <div class="label-container">
                                <label>Yape</label>
                            </div>
                            <div class="input-field">
                                <input type="text" id="ingreso-yape" value="S/ 30.00" readonly class="align-right">
                            </div>
                        </div>
                        <div class="input-container">
                            <div class="label-container">
                                <label>Plin</label>
                            </div>
                            <div class="input-field">
                                <input type="text" id="ingreso-plin" value="S/ 40.00" readonly class="align-right">
                            </div>
                        </div>
                        <div class="input-container">
                            <div class="label-container">
                                <label>Visa</label>
                            </div>
                            <div class="input-field">
                                <input type="text" id="ingreso-visa" value="S/ 25.00" readonly class="align-right">
                            </div>
                        </div>
                        <div class="input-container">
                            <div class="label-container">
                                <label>Depósito</label>
                            </div>
                            <div class="input-field">
                                <input type="text" id="ingreso-deposito" value="S/ 200.00" readonly class="align-right">
                            </div>
                        </div>
        
                        <h3>Egresos</h3>
                        <div class="input-container">
                            <div class="label-container">
                                <label>Combustible</label>
                            </div>
                            <div class="input-field">
                                <input type="text" id="egreso-pasajes" value="S/ -" readonly class="align-right">
                            </div>
                        </div>
                        <div class="input-container">
                            <div class="label-container">
                                <label>Almuerzo</label>
                            </div>
                            <div class="input-field">
                                <input type="text" id="egreso-pasajes" value="S/ -" readonly class="align-right">
                            </div>
                        </div>
                        <div class="input-container">
                            <div class="label-container">
                                <label>Pasajes</label>
                            </div>
                            <div class="input-field">
                                <input type="text" id="egreso-pasajes" value="S/ 16.00" readonly class="align-right">
                            </div>
                        </div>
                        <div class="input-container">
                            <div class="label-container">
                                <label>Compra de insumos</label>
                            </div>
                            <div class="input-field">
                                <input type="text" id="egreso-insumos" value="S/ 20.00" readonly class="align-right">
                            </div>
                        </div>
                        <div class="input-container">
                            <div class="label-container">
                                <label>Servicios varios</label>
                            </div>
                            <div class="input-field">
                                <input type="text" id="egreso-servicios" value="S/ 236.00" readonly class="align-right">
                            </div>
                        </div>
                        <div class="input-container">
                            <div class="label-container">
                                <label>Otros Conceptos</label>
                            </div>
                            <div class="input-field">
                                <input type="text" id="egreso-pasajes" value="S/ -" readonly class="align-right">
                            </div>
                        </div>
                        <div class="input-container">
                            <div class="label-container">
                                <label>Gerencia</label>
                            </div>
                            <div class="input-field">
                                <input type="text" id="egreso-pasajes" value="S/ -" readonly class="align-right">
                            </div>
                        </div>
        
                        <h3>Resumen</h3>
                        <div class="input-container">
                            <div class="label-container">
                                <label>Saldo anterior en efectivo</label>
                            </div>
                            <div class="input-field">
                                <input type="text" id="saldo-anterior" value="S/ 385.00" readonly class="align-right">
                            </div>
                        </div>
                        <div class="input-container">
                            <div class="label-container">
                                <label>Ingreso diario efectivo</label>
                            </div>
                            <div class="input-field">
                                <input type="text" id="ingreso-diario" value="S/ 545.00" readonly class="align-right">
                            </div>
                        </div>
                        <div class="input-container">
                            <div class="label-container">
                                <label>Total efectivo</label>
                            </div>
                            <div class="input-field">
                                <input type="text" id="total-efectivo" value="S/ 930.00" readonly class="align-right">
                            </div>
                        </div>
                        <div class="input-container">
                            <div class="label-container">
                                <label>Total egresos</label>
                            </div>
                            <div class="input-field">
                                <input type="text" id="total-egresos" value="S/ 272.00" readonly class="align-right">
                            </div>
                        </div>
                        <div class="input-container">
                            <div class="label-container">
                                <label>Total efectivo caja</label>
                            </div>
                            <div class="input-field">
                                <input type="text" id="total-efectivo-caja" value="S/ 658.00" readonly class="align-right input-color-total">
                            </div>
                        </div>
                        <div class="input-container">
                            <div class="label-container">
                                <label>Otros aportes registrados</label>
                                <label><strong>Yape, Plin, Bancos</strong></label>
                            </div>
                            <div class="input-field">
                                <input type="text" value=" - " readonly class="align-right">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php
require_once "../../partials/_footer.php";
?>


    <script>
        function actualizarValores() {
            var fecha = document.getElementById('fecha').value;

            if (fecha === "2025-03-23") {
                // Valores para el 23 de marzo de 2025
                document.getElementById('saldo-restante').value = "S/ 400.00";
                document.getElementById('ingreso-efectivo').value = "S/ 70.00";
                document.getElementById('ingreso-yape').value = "S/ 35.00";
                document.getElementById('ingreso-plin').value = "S/ 50.00";
                document.getElementById('ingreso-visa').value = "S/ 30.00";
                document.getElementById('ingreso-deposito').value = "S/ 150.00";
                document.getElementById('egreso-pasajes').value = "S/ 20.00";
                document.getElementById('egreso-insumos').value = "S/ 15.00";
                document.getElementById('egreso-servicios').value = "S/ 200.00";
                
                // Resumen
                document.getElementById('saldo-anterior').value = "S/ 400.00";
                document.getElementById('ingreso-diario').value = "S/ 545.00";
                document.getElementById('total-efectivo').value = "S/ 945.00";
                document.getElementById('total-egresos').value = "S/ 235.00";
                document.getElementById('total-efectivo-caja').value = "S/ 710.00";
            } else if (fecha === "2025-03-24") {
                // Valores para el 24 de marzo de 2025 (manteniendo los valores anteriores)
                document.getElementById('saldo-restante').value = "S/ 385.00";
                document.getElementById('ingreso-efectivo').value = "S/ 50.00";
                document.getElementById('ingreso-yape').value = "S/ 30.00";
                document.getElementById('ingreso-plin').value = "S/ 40.00";
                document.getElementById('ingreso-visa').value = "S/ 25.00";
                document.getElementById('ingreso-deposito').value = "S/ 200.00";
                document.getElementById('egreso-pasajes').value = "S/ 16.00";
                document.getElementById('egreso-insumos').value = "S/ 20.00";
                document.getElementById('egreso-servicios').value = "S/ 236.00";
                
                // Resumen
                document.getElementById('saldo-anterior').value = "S/ 385.00";
                document.getElementById('ingreso-diario').value = "S/ 545.00";
                document.getElementById('total-efectivo').value = "S/ 930.00";
                document.getElementById('total-egresos').value = "S/ 272.00";
                document.getElementById('total-efectivo-caja').value = "S/ 658.00";
            }
        }
    </script>

</body>

</html>