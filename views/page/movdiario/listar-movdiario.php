<?php

const NAMEVIEW = "Movimientos del Dia";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php"; 

?>
<div class="container-main">

  <h2> <?= "{$saludo}, " . htmlspecialchars($usuario['nombreCompleto']); ?></h2>

</div>
</div>
</div>

<?php
require_once "../../partials/_footer.php";
?>
</body>

</html>