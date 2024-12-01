<?php
require_once 'conexion.php';

if (isset($_GET['id'])) {
    $userId = intval($_GET['id']);

    $sql = "DELETE FROM usuarios WHERE id = ?";
    $stmt = $mysql->prepare($sql);
    $stmt->bind_param("i", $userId);

    if ($stmt->execute()) {
        echo "Usuario eliminado correctamente.";
    } else {
        echo "Error al eliminar el usuario: " . $stmt->error;
    }

    $stmt->close();
    $mysql->close();
} else {
    echo "ID de usuario no especificado.";
}