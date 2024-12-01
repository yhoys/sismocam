<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: loginpage.html");
    exit();
}

$rolUsuario = $_SESSION['rol'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SISMOCAM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="./css/stylesidebar.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <div class="d-flex">
        <aside id="sidebar" class="sidebar-toggle">
            <div class="sidebar-logo">
                <a href="http://192.168.107.230:8080/sismocam/sidebars.php">SISMOCAM</a>
            </div>
            <ul class="sidebar-nav p-0">
                <li class="sidebar-header">
                    Herramientas
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link" data-page="mainmenu.html">
                        <i class="bi bi-house-fill"></i>
                        <span>Menú Principal</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link" data-page="starttest.html">
                        <i class="bi bi-play-circle-fill"></i>
                        <span>Prueba</span>
                    </a>
                </li>
                <?php if ($rolUsuario === 'admin'): ?>
                    <li class="sidebar-item">
                        <a href="#" class="sidebar-link" data-page="adminpage.html">
                            <i class="bi bi-person-plus-fill"></i>
                            <span>Registrar Usuario</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="#" class="sidebar-link" data-page="usermanagement.php">
                            <i class="bi bi-person-fill-gear"></i>
                            <span>Administrar Usuario</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
            <div class="sidebar-footer">
                <a href="./php/logout.php" class="sidebar-link">
                    <i class="bi bi-arrow-left-square-fill"></i>
                    <span>Cerrar Sesión</span>
                </a>
            </div>
        </aside>
        <nav class="navbar navbar-expand">
            <button class="toggler-btn" type="button">
                <i class="bi bi-list"></i>
            </button>
        </nav>
        <div class="main">
            <main id="main-content" class="d-flex flex-column">
                <h1>Bienvenido a SISMOCAM</h1>
                <p>Selecciona una opción del menú para empezar.</p>
            </main>
        </div>
    </div>
    <script>
        const toggler = document.querySelector(".toggler-btn");
        toggler.addEventListener("click", () => {
            document.querySelector("#sidebar").classList.toggle("collapsed");
        });

        function loadPage(page) {
            fetch(page)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Error al cargar ${page}: ${response.statusText}`);
                    }
                    return response.text();
                })
                .then(data => {
                    const mainContent = document.getElementById("main-content");
                    mainContent.innerHTML = data;

                    const scripts = mainContent.querySelectorAll("script");
                    scripts.forEach(oldScript => {
                        const newScript = document.createElement("script");
                        newScript.textContent = oldScript.textContent;
                        document.body.appendChild(newScript);
                        oldScript.remove();
                    });

                    if (page === "usermanagement.php") {
                        setButtonListeners();
                    }
                })
                .catch(error => console.error("Error al cargar el contenido:", error));
        }

        document.querySelectorAll(".sidebar-link").forEach(link => {
            link.addEventListener("click", event => {
                event.preventDefault();
                const page = link.getAttribute("data-page");
                if (page) {
                    loadPage(page);
                } else if (link.getAttribute("href") === "./php/logout.php") {
                    window.location.href = "./php/logout.php";
                }
            });
        });

        function setButtonListeners() {
            const modifyButtons = document.querySelectorAll(".btn-modify");
            const deleteButtons = document.querySelectorAll(".btn-delete");

            modifyButtons.forEach(button => {
                button.addEventListener("click", () => {
                    const userId = button.getAttribute("data-id");
                    window.location.href = `./php/modify_user.php?id=${userId}`;
                });
            });

            deleteButtons.forEach(button => {
                button.addEventListener("click", () => {
                    const userId = button.getAttribute("data-id");
                    if (confirm("¿Estás seguro de que deseas eliminar este usuario?")) {
                        fetch(`./php/delete_user.php?id=${userId}`, {
                                method: "GET"
                            })
                            .then(response => response.text())
                            .then(data => {
                                alert(data);
                                loadPage("usermanagement.php");
                            })
                            .catch(error => console.error("Error al eliminar el usuario:", error));
                    }
                });
            });
        }
    </script>
</body>

</html>