<?php
session_start();
if (empty($_SESSION['user_id'])) {
  header('Location: index.php');
  exit;
}
$u_name = $_SESSION['user_name'] ?? 'Renny Franco';
$u_role = $_SESSION['user_role'] ?? 'Administrador';
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TRUFIX | Usuarios</title>
    <link rel="stylesheet" href="Usuarios.css" />
  </head>
  <body>
    <div class="layout">
      <!-- SIDEBAR -->
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
            <li><a class="menu__link is-active" href="#" aria-current="page">Usuarios</a></li>
          </ul>
        </nav>

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

      <!-- CONTENIDO -->
      <main class="content" role="main">
        <header class="page-header">
          <h1 class="page-title">Usuarios</h1>
          <div class="page-actions">
            <div class="input-with-icon">
              <input id="search" type="text" class="input" placeholder="Buscar usuario" aria-label="Buscar usuarios" />
            </div>
            <button class="btn btn-success" type="button" id="btnAdd">ADD</button>
          </div>
        </header>

        <section class="card">
          <div class="table-responsive">
            <table class="table" id="usersTable">
              <thead>
                <tr>
                  <th>Usuario</th>
                  <th>Nombre completo</th>
                  <th>Rol</th>
                  <th>Estado</th>
                  <th class="th-actions">Actions</th>
                </tr>
              </thead>
              <tbody>
                <!-- filas dinámicas -->
              </tbody>
            </table>
          </div>
        </section>
      </main>
    </div>

    <!-- MODAL Crear/Editar (vertical) -->
    <div id="userModal" class="modal" aria-hidden="true">
      <div class="modal__backdrop" data-close></div>
      <div class="modal__dialog modal--vertical" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        <header class="modal__header">
          <h2 id="modalTitle">Agregar usuario</h2>
          <button class="modal__close" type="button" title="Cerrar" aria-label="Cerrar" data-close>×</button>
        </header>

        <form id="userForm" class="form form--stacked" autocomplete="off">
          <input type="hidden" id="u_id" />
          <label class="field">
            <span class="label">Nombre completo</span>
            <input class="input" id="u_nombre" required placeholder="Ej: Juan Pérez" />
          </label>

          <label class="field">
            <span class="label">Rol</span>
            <select class="select" id="u_rol" required>
              <option value="Administrador">Administrador</option>
              <option value="Empleado">Empleado</option>
              <option value="Conductor">Conductor</option>
            </select>
          </label>

          <label class="field">
            <span class="label">Estado</span>
            <select class="select" id="u_estado">
              <option value="1">Activo</option>
              <option value="0">Inactivo</option>
            </select>
          </label>

          <div id="credBlock">
            <label class="field">
              <span class="label">Usuario</span>
              <input class="input" id="u_user" placeholder="Nombre de usuario" />
            </label>
            <div class="grid-2">
              <label class="field">
                <span class="label">Contraseña</span>
                <input class="input" id="u_pwd" type="password" minlength="8" placeholder="********" />
              </label>
              <label class="field">
                <span class="label">Confirmar contraseña</span>
                <input class="input" id="u_pwd2" type="password" minlength="8" placeholder="********" />
              </label>
            </div>
            <p class="hint">Mínimo 8 caracteres.</p>
          </div>

          <div class="form-actions">
            <button class="btn" type="button" data-close>Cancelar</button>
            <button class="btn btn-primary" type="submit">Guardar</button>
          </div>
        </form>
      </div>
    </div>

    <script>
      (function () {
        const tbody = document.querySelector('#usersTable tbody');
        const search = document.getElementById('search');

        const modal = document.getElementById('userModal');
        const form = document.getElementById('userForm');
        const title = document.getElementById('modalTitle');

        const f_id = document.getElementById('u_id');
        const f_nombre = document.getElementById('u_nombre');
        const f_rol = document.getElementById('u_rol');
        const f_estado = document.getElementById('u_estado');
        const f_user = document.getElementById('u_user');
        const f_pwd = document.getElementById('u_pwd');
        const f_pwd2 = document.getElementById('u_pwd2');
        const credBlock = document.getElementById('credBlock');

        const norm = (t) => t.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        const badge = (rol) => rol === 'Administrador' ? 'badge badge-blue' : 'badge';

        const open = () => { modal.classList.add('is-open'); modal.setAttribute('aria-hidden','false'); };
        const close = () => { modal.classList.remove('is-open'); modal.setAttribute('aria-hidden','true'); };
        document.querySelectorAll('[data-close]').forEach((b) => (b.onclick = close));
        window.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });

        function toggleCredBlock() {
          const isConductor = f_rol.value === 'Conductor';
          credBlock.style.display = isConductor ? 'none' : '';
          if (isConductor) { f_user.value = ''; f_pwd.value = ''; f_pwd2.value = ''; }
        }
        f_rol.addEventListener('change', toggleCredBlock);

        document.getElementById('btnAdd').onclick = () => {
          form.reset(); f_id.value = ''; title.textContent = 'Agregar usuario';
          f_user.disabled = false; // En alta se puede escribir user
          toggleCredBlock(); open(); f_nombre.focus();
        };

        // Buscar en vivo
        search.oninput = () => {
          const q = norm(search.value.trim());
          Array.from(tbody.children).forEach((tr) => {
            tr.style.display = !q || norm(tr.innerText).includes(q) ? '' : 'none';
          });
        };

        // Delegación: EDIT y switch
        tbody.addEventListener('click', (e) => {
          const tr = e.target.closest('tr'); if (!tr) return;
          if (e.target.classList.contains('tag-edit')) {
            title.textContent = 'Editar usuario';
            f_id.value = tr.dataset.id;
            f_nombre.value = tr.children[1].textContent.trim();
            f_rol.value = tr.children[2].innerText.trim();
            f_estado.value = tr.dataset.estado;
            f_user.value = tr.children[0].textContent.trim() || '';
            f_user.disabled = true; // No se edita username
            f_pwd.value = ''; f_pwd2.value = '';
            toggleCredBlock(); open(); f_nombre.focus();
          }
        });

        tbody.addEventListener('change', async (e) => {
          if (!e.target.classList.contains('switch-input')) return;
          const tr = e.target.closest('tr');
          const body = new FormData();
          body.append('id', tr.dataset.id);
          body.append('activo', e.target.checked ? 1 : 0);

          try {
            const r = await fetch('/src/api/users/toggle_active.php', { method: 'POST', body });
            const j = await r.json();
            if (!j.ok) throw new Error(j.error || 'Error');
            tr.dataset.estado = e.target.checked ? '1' : '0';
            tr.querySelector('.td-status').innerHTML = e.target.checked
              ? '<span class="badge badge-green">Activo</span>'
              : '<span class="badge badge-gray">Inactivo</span>';
          } catch (err) {
            alert(err.message || 'No se pudo actualizar el estado');
            e.target.checked = !e.target.checked;
          }
        });

        // Guardar (crear/editar)
        form.addEventListener('submit', async (e) => {
          e.preventDefault();

          const id = f_id.value.trim();
          const rol = f_rol.value;
          const estado = f_estado.value === '1';
          const isConductor = rol === 'Conductor';

          try {
            if (!id) {
              // CREATE
              const fd = new FormData();
              fd.append('nombre', f_nombre.value.trim());
              fd.append('rol', rol);
              fd.append('estado', estado ? 'Activo' : 'Inactivo');

              if (!isConductor) {
                if (!f_user.value.trim()) throw new Error('Usuario requerido');
                if (!f_pwd.value || f_pwd.value.length < 8) throw new Error('Contraseña mínima 8 caracteres');
                if (f_pwd.value !== f_pwd2.value) throw new Error('Las contraseñas no coinciden');
                fd.append('usuario', f_user.value.trim());
                fd.append('password', f_pwd.value);
                fd.append('password2', f_pwd2.value);
              }

              const r = await fetch('/src/api/users/create.php', { method: 'POST', body: fd });
              const j = await r.json();
              if (!j.ok) throw new Error(j.error || 'No se pudo crear');

            } else {
              // UPDATE (sin cambiar username)
              const fd = new FormData();
              fd.append('id', id);
              fd.append('nombre', f_nombre.value.trim());
              fd.append('rol', rol);
              fd.append('estado', estado ? 'Activo' : 'Inactivo');

              if (!isConductor && f_pwd.value) {
                if (f_pwd.value.length < 8) throw new Error('Contraseña mínima 8 caracteres');
                if (f_pwd.value !== f_pwd2.value) throw new Error('Las contraseñas no coinciden');
                fd.append('password', f_pwd.value);
                fd.append('password2', f_pwd2.value);
              }

              const r = await fetch('/src/api/users/update.php', { method: 'POST', body: fd });
              const j = await r.json();
              if (!j.ok) throw new Error(j.error || 'No se pudo actualizar');
            }

            close(); await loadUsers();
          } catch (err) {
            alert(err.message || 'Error al guardar');
          }
        });

        // Cargar lista
        async function loadUsers() {
          const r = await fetch('/src/api/users/list.php', { credentials: 'include' });
          const j = await r.json();
          if (!j.ok) { alert(j.error || 'No se pudo cargar usuarios'); return; }

          tbody.innerHTML = (j.data || []).map((u) => `
            <tr data-id="${u.id}" data-has-login="${u.has_login}" data-estado="${u.estado}">
              <td data-key="usuario">${u.username ?? '-'}</td>
              <td data-key="nombre">${u.nombre}</td>
              <td><span class="${badge(u.rol)}">${u.rol}</span></td>
              <td class="td-status">${u.estado ? '<span class="badge badge-green">Activo</span>' : '<span class="badge badge-gray">Inactivo</span>'}</td>
              <td class="actions">
                <button class="tag tag-edit" type="button">EDIT</button>
                <label class="switch">
                  <input type="checkbox" class="switch-input" ${u.estado ? 'checked' : ''} />
                  <span class="switch-track" title="Activo / Inactivo"></span>
                </label>
              </td>
            </tr>
          `).join('');
        }
        loadUsers();
      })();
    </script>
  </body>
</html>
