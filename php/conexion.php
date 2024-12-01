<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sismocam";

try {
    $mysql = new mysqli($servername, $username, $password, $dbname);

    if ($mysql->connect_error) {
        throw new Exception("Problemas con la conexión a la base de datos: " . $mysql->connect_error);
    }
} catch (Exception $e) {
    die($e->getMessage());
}