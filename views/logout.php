<?php
// /app/logout.php
session_start();
session_unset();
session_destroy();
header("Location:http://localhost/Fix360/");
exit;
