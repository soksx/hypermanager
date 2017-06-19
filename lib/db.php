<?php
    /**
    * IDC: Create the connection to DB.
    */   
    $dbhost = "localhost";
    $dbuser = "usuarioDB";
    $dbpass = "contraseñaDB";
    $dbname = "nombreDB";

    $db = new PDO('mysql:host=' . $dbhost . ';dbname=' . $dbname . '', $dbuser, $dbpass);

?>
