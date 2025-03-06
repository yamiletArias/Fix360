<?php
// Aqui iran mas funciones de herramientas

class Helper
{

    public static function limpiarCadena($cadena): string
    {
        $cadena = trim($cadena);
        $cadena = stripslashes($cadena); //Eliminar el backslash

        //Javascript
        $cadena = str_ireplace("<script>", "", $cadena);
        $cadena = str_ireplace("</script>", "", $cadena);
        $cadena = str_ireplace("<script src=", "", $cadena);
        $cadena = str_ireplace("<script type=", "", $cadena);
        $cadena = str_ireplace("'>", "", $cadena);

        //SQL
        $cadena = str_ireplace("SELECT * FROM", "", $cadena);
        $cadena = str_ireplace("DELETE FROM", "", $cadena);
        $cadena = str_ireplace("INSERT INTO", "", $cadena);
        $cadena = str_ireplace("DROP TABLE", "", $cadena);
        $cadena = str_ireplace("TRUNCATE TABLE", "", $cadena);
        $cadena = str_ireplace("SHOW TABLES", "", $cadena);
        $cadena = str_ireplace("SHOW DATABASE", "", $cadena);

        //Etiquetas
        $cadena = str_ireplace("<?php", "", $cadena);
        $cadena = str_ireplace("?>", "", $cadena);
        $cadena = str_ireplace("--", "", $cadena);
        $cadena = str_ireplace(">", "", $cadena);
        $cadena = str_ireplace("<", "", $cadena);
        $cadena = str_ireplace("[", "", $cadena);
        $cadena = str_ireplace("]", "", $cadena);
        $cadena = str_ireplace("{", "", $cadena);
        $cadena = str_ireplace("}", "", $cadena);
        $cadena = str_ireplace("==", "", $cadena);
        $cadena = str_ireplace("===", "", $cadena);
        $cadena = str_ireplace("^", "", $cadena); //ALT + 94
        $cadena = str_ireplace(";", "", $cadena);
        $cadena = str_ireplace("::", "", $cadena);

        $cadena = trim($cadena);
        return $cadena;
    }


    public static function renderContentHeader($title, $home, $path)
    {
        return "
            <div class='page-header'>
              <h3 class='page-title'> $title </h3>
              <nav aria-label='breadcrumb'>
                <ol class='breadcrumb'>
                  <li class='breadcrumb-item'><a href='$path'>$home</a></li>
                  <li class='breadcrumb-item active' aria-current='page'>$title</li>
                </ol>
              </nav>
            </div>
            ";
    }
}