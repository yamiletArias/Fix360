<?php

$clave1 = "juan1234";           //Admin
$clave2 = "maria567";         //Gerente
$clave3 = "carlosabcd";           //Mecanico

var_dump(password_hash($clave1, PASSWORD_BCRYPT));
var_dump(password_hash($clave2, PASSWORD_BCRYPT));
var_dump(password_hash($clave3, PASSWORD_BCRYPT));