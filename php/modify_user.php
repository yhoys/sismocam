<?php
require_once 'conexion.php';

if (isset($_GET['id'])) {
    $userId = intval($_GET['id']);
    $sql = "SELECT id, nombre, email, rol FROM usuarios WHERE id = ?";
    $stmt = $mysql->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo "Usuario no encontrado.";
        exit();
    }
    $stmt->close();
} else {
    echo "ID de usuario no especificado.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Modificar Usuario</title>
</head>

<body>
    <h1>Modificar Usuario</h1>
    <form action="./update_user.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
        <label>Nombre:
            <input type="text" name="nombre" value="<?php echo htmlspecialchars($user['nombre']); ?>">
        </label>
        <label>Email:
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
        </label>
        <label>Rol:
            <select name="rol">
                <option value="admin" <?php if ($user['rol'] == 'admin') echo 'selected'; ?>>Admin</option>
                <option value="estudiante" <?php if ($user['rol'] == 'estudiante') echo 'selected'; ?>>Estudiante</option>
            </select>
        </label>
        <button type="submit">Guardar Cambios</button>
    </form>
</body>

</html>