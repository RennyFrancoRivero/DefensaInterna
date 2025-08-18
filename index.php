<?php
session_start();
require_once __DIR__ . '/src/config/db.php';

// Prueba la conexión
try {
    $pdo = db();
    // Si llega aquí, la conexión fue exitosa
} catch (Exception $e) {
    die("Error de conexión: Por favor contacte al administrador");
}

if (!empty($_SESSION['user_id'])) {
    header('Location: home.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TRUFIX | Iniciar sesión</title>
    <link rel="stylesheet" href="styles/login.css" />
  </head>
  <body class="login-page">
    <main class="card" role="main" aria-labelledby="app-title">
      <h1 id="app-title" class="brand">TRUFIX</h1>
      <p class="subtitle">Sistema de Gestión de Envíos</p>

      <!-- Mantén el form; procesado vía fetch a /src/auth/login.php -->
      <form class="form" id="loginForm" method="post" action="src/auth/login.php" novalidate>
        <label class="field">
          <span class="label">Usuario</span>
          <input
            type="text"
            name="username"
            id="username"
            required
            autocomplete="username"
            placeholder="Usuario"
            aria-required="true"
          />
        </label>

        <label class="field">
          <span class="label">Contraseña</span>
          <input
            type="password"
            name="password"
            id="password"
            required
            minlength="4"
            autocomplete="current-password"
            placeholder="********"
            aria-required="true"
          />
        </label>

        <button class="btn" type="submit">Ingresar</button>
        <p id="errorMsg" class="hint" style="color:#b91c1c; display:none; margin-top:10px;"></p>
      </form>
    </main>

    <script>
      (function () {
        const form = document.getElementById('loginForm');
        const error = document.getElementById('errorMsg');

        form.addEventListener('submit', async (e) => {
          e.preventDefault();
          error.style.display = 'none';
          const fd = new FormData(form);

          try {
            const res = await fetch(form.action, {
              method: 'POST',
              body: fd,
              credentials: 'include'
            });
            const j = await res.json();
            if (j.ok) {
              // El backend setea $_SESSION y devolvió ok
              location.href = 'home.php';
            } else {
              throw new Error(j.error || 'Usuario/contraseña inválidos');
            }
          } catch (err) {
            error.textContent = err.message || 'No se pudo iniciar sesión';
            error.style.display = 'block';
          }
        });
      })();
    </script>
  </body>
</html>
