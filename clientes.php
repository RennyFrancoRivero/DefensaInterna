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
    <title>TRUFIX | Clientes</title>
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
            <li><a class="menu__link" href="oficinas.php">Oficinas</a></li>
            <li><a class="menu__link is-active" href="#" aria-current="page">Clientes</a></li>
            <li><a class="menu__link" href="Usuarios.php">Usuarios</a></li>
          </ul>
        </nav>
        <div class="sidebar__user">
          <div class="user-info">
            <div class="user-avatar" aria-hidden="true"><span class="user-icon">🧍</span></div>
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
          <h1 class="page-title">Clientes</h1>
          <div class="page-actions">
            <div class="input-with-icon">
              <input id="q" type="text" class="input" placeholder="Buscar cliente" aria-label="Buscar clientes" />
            </div>
            <button class="btn btn-success" id="btnAdd" type="button">ADD</button>
          </div>
        </header>

        <section class="card">
          <div class="table-responsive">
            <table class="table" id="tbl">
              <thead>
                <tr>
                  <th>CI</th>
                  <th>Nombre</th>
                  <th>Teléfono</th>
                  <th>Email</th>
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
          <h2 id="mTitle">Agregar cliente</h2>
          <button class="modal__close" type="button" data-close>×</button>
        </header>

        <form id="frm" class="form form--stacked" autocomplete="off">
          <input type="hidden" id="id" />
          <div class="grid-2">
            <label class="field"><span class="label">CI</span><input class="input" id="ci" /></label>
            <label class="field"><span class="label">Teléfono</span><input class="input" id="telefono" /></label>
          </div>
          <div class="grid-2">
            <label class="field"><span class="label">Nombres</span><input class="input" id="nombres" required /></label>
            <label class="field"><span class="label">Apellidos</span><input class="input" id="apellidos" required /></label>
          </div>
          <label class="field"><span class="label">Email</span><input class="input" id="email" type="email" /></label>
          <label class="field"><span class="label">Dirección</span><input class="input" id="direccion" /></label>
          <label class="field">
            <span class="label">Estado</span>
            <select class="select" id="estado"><option value="1">Activo</option><option value="0">Inactivo</option></select>
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
        const tbody=document.querySelector('#tbl tbody');
        const q=document.getElementById('q');
        const modal=document.getElementById('modal');
        const frm=document.getElementById('frm');
        const mTitle=document.getElementById('mTitle');
        const F={
          id:document.getElementById('id'),
          ci:document.getElementById('ci'),
          nombres:document.getElementById('nombres'),
          apellidos:document.getElementById('apellidos'),
          telefono:document.getElementById('telefono'),
          email:document.getElementById('email'),
          direccion:document.getElementById('direccion'),
          estado:document.getElementById('estado'),
        };
        const open=()=>{modal.classList.add('is-open');modal.setAttribute('aria-hidden','false');};
        const close=()=>{modal.classList.remove('is-open');modal.setAttribute('aria-hidden','true');};
        document.querySelectorAll('[data-close]').forEach(b=>b.onclick=close);
        window.addEventListener('keydown',e=>{if(e.key==='Escape')close();});
        function norm(t){return t.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,'');}
        const badgeEstado=v=>v?'<span class="badge badge-green">Activo</span>':'<span class="badge badge-gray">Inactivo</span>';

        async function load(){
          const r=await fetch('/src/api/clients/list.php'); // TODO: ajusta endpoint
          const j=await r.json(); if(!j.ok){alert(j.error||'No se pudo cargar');return;}
          tbody.innerHTML=(j.data||[]).map(c=>`
            <tr data-id="${c.id}" data-estado="${c.estado}">
              <td>${c.ci??''}</td>
              <td>${c.nombre}</td>
              <td>${c.telefono??''}</td>
              <td>${c.email??''}</td>
              <td class="td-status">${badgeEstado(+c.estado)}</td>
              <td class="actions">
                <button class="tag tag-edit" type="button">EDIT</button>
                <label class="switch">
                  <input type="checkbox" class="switch-input" ${+c.estado?'checked':''}/>
                  <span class="switch-track" title="Activo / Inactivo"></span>
                </label>
              </td>
            </tr>
          `).join('');
        }

        q.oninput=()=>{
          const s=norm(q.value.trim());
          Array.from(tbody.children).forEach(tr=>{
            tr.style.display=!s||norm(tr.innerText).includes(s)?'':'none';
          });
        };

        document.getElementById('btnAdd').onclick=()=>{
          frm.reset();F.id.value='';mTitle.textContent='Agregar cliente';open();F.nombres.focus();
        };

        tbody.addEventListener('click',(e)=>{
          const tr=e.target.closest('tr'); if(!tr)return;
          if(e.target.classList.contains('tag-edit')){
            F.id.value=tr.dataset.id;
            F.ci.value=tr.children[0].textContent.trim();
            const [nom, ...rest]=tr.children[1].textContent.trim().split(' ');
            F.nombres.value=tr.children[1].textContent.trim(); // puedes separar si quieres
            F.apellidos.value='';
            F.telefono.value=tr.children[2].textContent.trim();
            F.email.value=tr.children[3].textContent.trim();
            F.estado.value=tr.dataset.estado;
            mTitle.textContent='Editar cliente'; open(); F.nombres.focus();
          }
        });

        tbody.addEventListener('change',async(e)=>{
          if(!e.target.classList.contains('switch-input'))return;
          const tr=e.target.closest('tr');
          const body=new FormData();
          body.append('id',tr.dataset.id);
          body.append('activo',e.target.checked?1:0);
          try{
            const r=await fetch('/src/api/clients/toggle_active.php',{method:'POST',body});
            const j=await r.json(); if(!j.ok) throw new Error(j.error||'Error');
            tr.dataset.estado=e.target.checked?'1':'0';
            tr.querySelector('.td-status').innerHTML=e.target.checked?badgeEstado(1):badgeEstado(0);
          }catch(err){ alert(err.message||'No se pudo actualizar'); e.target.checked=!e.target.checked; }
        });

        frm.addEventListener('submit',async(e)=>{
          e.preventDefault();
          const id=F.id.value.trim();
          const body=new FormData();
          body.append('ci',F.ci.value.trim());
          body.append('nombres',F.nombres.value.trim());
          body.append('apellidos',F.apellidos.value.trim());
          body.append('telefono',F.telefono.value.trim());
          body.append('email',F.email.value.trim());
          body.append('direccion',F.direccion.value.trim());
          body.append('estado',F.estado.value);

          try{
            const url=id?'/src/api/clients/update.php':'/src/api/clients/create.php';
            if(id) body.append('id',id);
            const r=await fetch(url,{method:'POST',body});
            const j=await r.json(); if(!j.ok) throw new Error(j.error||'Error');
            close(); await load();
          }catch(err){ alert(err.message||'No se pudo guardar'); }
        });

        load();
      })();
    </script>
  </body>
</html>
