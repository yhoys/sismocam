<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
  header("Location: ../loginpage.html");
  exit();
}

require_once './php/conexion.php';

$sql = "SELECT id, nombre, email, rol FROM usuarios";
$result = $mysql->query($sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de Usuarios</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="./css/stylemanagement.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
  <div class="full-height" id="fullHeight">
    <div class="table-container table-responsive">
      <h1 class="table-title">Administración de Usuarios</h1>
      <div class="table-container-scroll">
        <table class="table table-hover table-striped table-sm">
          <thead class="table-dark">
            <tr>
              <th scope="col">#</th>
              <th scope="col">Nombre</th>
              <th scope="col">Correo</th>
              <th scope="col">Rol</th>
              <th scope="col">Acción</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<th scope='row'>" . htmlspecialchars($row['id']) . "</th>";
                echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                echo "<td>" . htmlspecialchars($row['rol']) . "</td>";
                echo "<td>";
                echo "<button type='button' class='btn btn-sm btn-primary btn-modify' data-id='" . $row['id'] . "'>Modificar</button>";
                echo "<button type='button' class='btn btn-sm btn-danger btn-delete' data-id='" . $row['id'] . "'>Eliminar</button>";
                echo "</td>";
                echo "</tr>";
              }
            } else {
              echo "<tr><td colspan='5'>No hay usuarios registrados.</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      console.log("DOM totalmente cargado");
      const modifyButtons = document.querySelectorAll(".btn-modify");
      const deleteButtons = document.querySelectorAll(".btn-delete");

      modifyButtons.forEach(button => {
        button.addEventListener("click", () => {
          const userId = button.getAttribute("data-id");
          console.log(`Modificar usuario con ID: ${userId}`);
          window.location.href = `./php/modify_user.php?id=${userId}`;
        });
      });

      deleteButtons.forEach(button => {
        button.addEventListener("click", () => {
          const userId = button.getAttribute("data-id");
          console.log(`Eliminar usuario con ID: ${userId}`);
          if (confirm("¿Estás seguro de que deseas eliminar este usuario?")) {
            fetch(`./php/delete_user.php?id=${userId}`, {
                method: "GET"
              })
              .then(response => response.text())
              .then(data => {
                alert(data);
                location.reload();
              })
              .catch(error => console.error("Error al eliminar el usuario:", error));
          }
        });
      });
    });
  </script>
</body>

</html>