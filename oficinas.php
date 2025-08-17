<?php
session_start();
if (empty($_SESSION['user'])) { header('Location: index.php'); exit; }
$u_name = $_SESSION['user']['name'] ?? 'Usuario';
$u_role = $_SESSION['user']['role'] ?? 'Rol';
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TRUFIX | Oficinas</title>
    <link rel="stylesheet" href="Usuarios.css" />
  </head>
  <body>
    <div class="layout">
      <aside class="sidebar" role="navigation" aria-label="Menú principal">
        <div class="sidebar__brand">
          <span class="brand">TRUFIX</span>
          <span class="subtitle">Gestión de Envíos</span>
        </div>
        <nav class="menu">
          <ul class="menu__list">
            <li><a class="menu__link" href="envios.php">Encomiendas</a></li>
            <li><a class="menu__link" href="estados.php">Estados de envío</a></li>
            <li><a class="menu__link is-active" href="#" aria-current="page">Oficinas</a></li>
            <li><a class="menu__link" href="clientes.php">Clientes</a></li>
            <li><a class="menu__link" href="Usuarios.php">Usuarios</a></li>
          </ul>
        </nav>
        <div class="sidebar__user">
          <div class="user-info">
            <div class="user-avatar" aria-hidden="true"><span class="user-icon">👨‍💼</span></div>
            <div class="user-text">
              <span class="user-name"><?php echo htmlspecialchars($u_name); ?></span>
              <span class="user-role"><?php echo htmlspecialchars($u_role); ?></span>
            </div>
            <a href="logout.php" class="logout-btn" title="Salir" aria-label="Salir">
              <svg class="logout-svg" width="30" height="30" viewBox="0 0 24 24"><path d="M10 17a1 1 0 0 0 1 1h3a3 3 0 0 0 3-3V9a3 3 0 0 0-3-3h-3a1 1 0 1 0 0 2h3a1 1 0 0 1 1 1v6a1 1 0 0 1-1 1h-3a1 1 0 0 0-1 1Zm-2.707-5 2.147-2.146a.5.5 0 0 0-.707-.708l-3 3a.5.5 0 0 0 0 .708l3 3a.5.5 0 1 0 .707-.708L7.293 12H16.5a.5.5 0 0 0 0-1H7.293Z" fill="currentColor"/></svg>
            </a>
          </div>
        </div>
      </aside>

      <main class="content" role="main">
        <header class="page-header">
          <h1 class="page-title">Oficinas</h1>
          <div class="page-actions">
            <div class="input-with-icon">
              <input id="q" type="text" class="input" placeholder="Buscar oficina" aria-label="Buscar oficinas" />
            </div>
            <button class="btn btn-success" id="btnAdd" type="button">ADD</button>
          </div>
        </header>

        <section class="card">
          <div class="table-responsive">
            <table class="table" id="tbl">
              <thead>
                <tr>
                  <th>Nombre</th>
                  <th>Ciudad</th>
                  <th>Dirección</th>
                  <th>Teléfono</th>
                  <th>Estado</th>
                  <th class="th-actions">Actions</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </section>
      </main>
    </div>

    <!-- Modal -->
    <div id="modal" class="modal" aria-hidden="true">
      <div class="modal__backdrop" data-close></div>
      <div class="modal__dialog modal--vertical" role="dialog" aria-modal="true" aria-labelledby="mTitle">
        <header class="modal__header">
          <h2 id="mTitle">Agregar oficina</h2>
          <button class="modal__close" type="button" data-close>×</button>
        </header>

        <form id="frm" class="form form--stacked" autocomplete="off">
          <input type="hidden" id="id" />
          <label class="field">
            <span class="label">Nombre</span>
            <input class="input" id="nombre" required />
          </label>
          <label class="field">
            <span class="label">Ciudad</span>
            <select class="select" id="ciudad_id" required></select>
          </label>
          <label class="field">
            <span class="label">Dirección</span>
            <input class="input" id="direccion" required />
          </label>
          <label class="field">
            <span class="label">Teléfono</span>
            <input class="input" id="telefono" />
          </label>
          <label class="field">
            <span class="label">Estado</span>
            <select class="select" id="estado">
              <option value="1">Activo</option>
              <option value="0">Inactivo</option>
            </select>
          </label>
          <div class="form-actions">
            <button class="btn" type="button" data-close>Cancelar</button>
            <button class="btn btn-primary" type="submit">Guardar</button>
          </div>
        </form>
      </div>
    </div>

    <script>
      (function(){
        const tbody = document.querySelector('#tbl tbody');
        const q = document.getElementById('q');
        const modal = document.getElementById('modal');
        const frm = document.getElementById('frm');
        const mTitle = document.getElementById('mTitle');

        const F = {
          id: document.getElementById('id'),
          nombre: document.getElementById('nombre'),
          ciudad_id: document.getElementById('ciudad_id'),
          direccion: document.getElementById('direccion'),
          telefono: document.getElementById('telefono'),
          estado: document.getElementById('estado'),
        };

        const open = () => { modal.classList.add('is-open'); modal.setAttribute('aria-hidden','false'); };
        const close = () => { modal.classList.remove('is-open'); modal.setAttribute('aria-hidden','true'); };
        document.querySelectorAll('[data-close]').forEach(el => el.onclick = close);
        window.addEventListener('keydown', e => { if (e.key==='Escape') close(); });

        function norm(t){return t.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,'');}
        function badgeEstado(v){return v?'<span class="badge badge-green">Activo</span>':'<span class="badge badge-gray">Inactivo</span>';}

        async function loadCiudades(){
          // TODO: ajusta endpoint si difiere
          const r = await fetch('/src/api/util/ciudades_min.php');
          const j = await r.json(); if(!j.ok) throw new Error(j.error||'Error');
          F.ciudad_id.innerHTML = j.data.map(c=>`<option value="${c.id}">${c.nombre}</option>`).join('');
        }

        async function load(){
          const r = await fetch('/src/api/offices/list.php'); // TODO: ajusta endpoint
          const j = await r.json(); if(!j.ok){ alert(j.error||'No se pudo cargar'); return; }
          tbody.innerHTML = j.data.map(o=>`
            <tr data-id="${o.id}" data-estado="${o.estado}">
              <td>${o.nombre}</td>
              <td>${o.ciudad}</td>
              <td>${o.direccion??''}</td>
              <td>${o.telefono??''}</td>
              <td class="td-status">${badgeEstado(+o.estado)}</td>
              <td class="actions">
                <button class="tag tag-edit" type="button">EDIT</button>
                <label class="switch">
                  <input type="checkbox" class="switch-input" ${+o.estado?'checked':''}/>
                  <span class="switch-track" title="Activo / Inactivo"></span>
                </label>
              </td>
            </tr>
          `).join('');
        }

        q.oninput = () => {
          const s = norm(q.value.trim());
          Array.from(tbody.children).forEach(tr=>{
            tr.style.display = !s || norm(tr.innerText).includes(s) ? '' : 'none';
          });
        };

        document.getElementById('btnAdd').onclick = async () => {
          frm.reset(); F.id.value=''; mTitle.textContent='Agregar oficina';
          await loadCiudades(); open(); F.nombre.focus();
        };

        tbody.addEventListener('click', async (e)=>{
          const tr = e.target.closest('tr'); if(!tr) return;
          if (e.target.classList.contains('tag-edit')){
            // editar
            const id = tr.dataset.id;
            // podrías tener un endpoint /get.php?id=...
            F.id.value=id;
            F.nombre.value = tr.children[0].textContent.trim();
            await loadCiudades();
            // Selección tentativa por nombre (o trae detalle del backend)
            F.direccion.value = tr.children[2].textContent.trim();
            F.telefono.value = tr.children[3].textContent.trim();
            F.estado.value = tr.dataset.estado;
            mTitle.textContent='Editar oficina'; open(); F.nombre.focus();
          }
        });

        tbody.addEventListener('change', async (e)=>{
          if(!e.target.classList.contains('switch-input')) return;
          const tr = e.target.closest('tr');
          const body = new FormData();
          body.append('id', tr.dataset.id);
          body.append('activo', e.target.checked?1:0);
          try{
            const r = await fetch('/src/api/offices/toggle_active.php',{method:'POST',body});
            const j = await r.json(); if(!j.ok) throw new Error(j.error||'Error');
            tr.dataset.estado = e.target.checked ? '1':'0';
            tr.querySelector('.td-status').innerHTML = e.target.checked?badgeEstado(1):badgeEstado(0);
          }catch(err){
            alert(err.message||'No se pudo actualizar'); e.target.checked = !e.target.checked;
          }
        });

        frm.addEventListener('submit', async (e)=>{
          e.preventDefault();
          const id = F.id.value.trim();
          const body = new FormData();
          body.append('nombre', F.nombre.value.trim());
          body.append('ciudad_id', F.ciudad_id.value);
          body.append('direccion', F.direccion.value.trim());
          body.append('telefono', F.telefono.value.trim());
          body.append('estado', F.estado.value);

          try{
            const url = id ? '/src/api/offices/update.php' : '/src/api/offices/create.php';
            if(id) body.append('id', id);
            const r = await fetch(url,{method:'POST',body});
            const j = await r.json(); if(!j.ok) throw new Error(j.error||'Error');
            close(); await load();
          }catch(err){ alert(err.message||'No se pudo guardar'); }
        });

        load();
      })();
    </script>
  </body>
</html>
