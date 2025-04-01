<?php

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>
      <div class="container-main">
        <h2><strong>Grafico de Contactabilidad de los clientes:</strong></h2>
        <div>
          <canvas style="margin-bottom: 100px;" id="myChart"></canvas>
        </div>
        <h2><strong>Grafico de Clientes registrados por mes:</strong></h2>
        <div>
          <canvas id="Chart"></canvas>
        </div>





      </div>

    </div>


  </div>
  <!--FIN VENTAS-->

  <?php

require_once "../../partials/_footer.php";

?>

  <script>
    const ctx = document.getElementById('myChart');

    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['Redes sociales', 'Referencias de amistades', 'Folletos', 'Campañas publicitarias'],
        datasets: [{
          label: '# de Clientes',
          data: [12, 19, 3, 5],
          backgroundColor: [
            'rgba(255, 99, 132, 0.8)',  // Color para "Redes sociales"
            'rgba(54, 162, 235, 0.8)',  // Color para "Referencias de amistades"
            'rgba(255, 206, 86, 0.8)',  // Color para "Folletos"
            'rgba(75, 192, 192, 0.8)'   // Color para "Campañas publicitarias"
          ],
          borderColor: [
            'rgba(255, 99, 132, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)'
          ],
          borderWidth: 1
        }]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
  </script>

  <script>
    const chart = document.getElementById('Chart');

    new Chart(chart, {
      type: 'line',
      data: {
        labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Setiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        datasets: [{
          label: '# de clientes',
          data: [10, 20, 15, 5, 25, 30, 10, 15, 25, 14, 23, 10],
          fill: false,
          borderColor: 'rgb(75, 192, 192)',
          tension: 0.1

        }]
      }
    })

  </script>

  <!-- endinject -->
  <!-- Custom js for this page -->
  <!-- End custom js for this page -->
</body>

</html>