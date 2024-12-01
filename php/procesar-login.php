<?php
require 'conexion.php';

$email = $_POST['email'];
$password = $_POST['password'];

$email = $mysql->real_escape_string($email);

$query = "SELECT * FROM usuarios WHERE email = '$email'";
$result = $mysql->query($query);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['nombre'];
        $_SESSION['rol'] = $user['rol'];

        header("Location: ../sidebars.php");
        exit();
    } else {
        echo "Usuario o contraseña incorrectos.";
    }
} else {
    echo "Usuario o contraseña incorrectos.";
}

$mysql->close();