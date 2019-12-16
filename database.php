<?php

//Gestionamos la conexion a base de datos

$dsn = "mysql:host=127.0.0.1;dbname=bd_clima;port=8889";
$usuario = "root";
$pass = "";

try {
    $db = new PDO($dsn, $usuario, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (\Exception $e) {
    die("Conexion fallida: ".$e->getMessage());
}
?>