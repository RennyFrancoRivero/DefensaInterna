<?php
session_start();
if (empty($_SESSION['user_id'])) {
  header('Location: index.php');
  exit;
}
// Opcional: valores para imprimir en el header
$u_name = $_SESSION['user_name'] ?? 'Usuario';
$u_role = $_SESSION['user_role'] ?? 'Rol';
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TRUFIX | Home</title>
    <link rel="stylesheet" href="home.css" />
  </head>
  <body>
    <div class="layout">
      <!-- Sidebar -->
      <aside class="sidebar" role="navigation" aria-label="Menú principal">
        <div class="sidebar__brand">
          <span class="brand">TRUFIX</span>
          <span class="subtitle">Gestión de Envíos</span>
        </div>

        <nav class="menu">
          <ul class="menu__list">
            <li><a class="menu__link" href="envios.php">Encomiendas</a></li>
            <li><a class="menu__link" href="estados.php">Estados de envío</a></li>
            <li><a class="menu__link" href="oficinas.php">Oficinas</a></li>
            <li><a class="menu__link" href="clientes.php">Clientes</a></li>
            <li><a class="menu__link" href="Usuarios.php">Usuarios</a></li>
          </ul>
        </nav>

        <!-- Usuario / salir -->
        <div class="sidebar__user">
          <div class="user-info">
            <div class="user-avatar" aria-hidden="true">
              <span class="user-icon">👨‍💼</span>
            </div>
            <div class="user-text">
              <span class="user-name" id="u_name"><?php echo htmlspecialchars($u_name); ?></span>
              <span class="user-role" id="u_role"><?php echo htmlspecialchars($u_role); ?></span>
            </div>
            <a href="logout.php" class="logout-btn" title="Salir" aria-label="Salir">
              <svg class="logout-svg" width="30" height="30" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M10 17a1 1 0 0 0 1 1h3a3 3 0 0 0 3-3V9a3 3 0 0 0-3-3h-3a1 1 0 1 0 0 2h3a1 1 0 0 1 1 1v6a1 1 0 0 1-1 1h-3a1 1 0 0 0-1 1Zm-2.707-5 2.147-2.146a.5.5 0 0 0-.707-.708l-3 3a.5.5 0 0 0 0 .708l3 3a.5.5 0 1 0 .707-.708L7.293 12H16.5a.5.5 0 0 0 0-1H7.293Z" fill="currentColor"/>
              </svg>
            </a>
          </div>
        </div>
      </aside>

      <!-- Contenido inicial vacío/landing -->
      <main class="content" role="main">
        <header class="content__header">
          <h1 class="content__title">Bienvenido</h1>
          <p class="content__subtitle">Selecciona una opción del menú para empezar.</p>
        </header>
      </main>
    </div>
  </body>
</html>
