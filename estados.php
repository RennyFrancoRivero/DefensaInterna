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
    <title>TRUFIX | Estados de envío</title>
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
            <li><a class="menu__link is-active" href="#" aria-current="page">Estados de envío</a></li>
            <li><a class="menu__link" href="oficinas.php">Oficinas</a></li>
            <li><a class="menu__link" href="clientes.php">Clientes</a></li>
            <li><a class="menu__link" href="Usuarios.php">Usuarios</a></li>
          </ul>
        </nav>
        <div class="sidebar__user">
          <div class="user-info">
            <div class="user-avatar" aria-hidden="true"><span class="user-icon">📦</span></div>
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
          <h1 class="page-title">Estados de envío</h1>
          <div class="page-actions">
            <div class="input-with-icon"><input id="q" class="input" placeholder="Buscar estado" /></div>
            <button class="btn btn-success" id="btnAdd" type="button">ADD</button>
          </div>
        </header>

        <section class="card">
          <div class="table-responsive">
            <table class="table" id="tbl">
              <thead>
                <tr>
                  <th>Código</th>
                  <th>Nombre</th>
                  <th>Orden</th>
                  <th>Inicial</th>
                  <th>Final</th>
                  <th>Color</th>
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
          <h2 id="mTitle">Agregar estado</h2>
          <button class="modal__close" type="button" data-close>×</button>
        </header>
        <form id="frm" class="form form--stacked" autocomplete="off">
          <input type="hidden" id="id" />
          <div class="grid-2">
            <label class="field"><span class="label">Código</span><input class="input" id="codigo" required /></label>
            <label class="field"><span class="label">Nombre</span><input class="input" id="nombre" required /></label>
          </div>
          <label class="field"><span class="label">Orden secuencial</span><input class="input" id="orden" type="number" min="1" required /></label>
          <div class="grid-3">
            <label class="field"><span class="label">¿Inicial?</span>
              <select class="select" id="inicial"><option value="0">No</option><option value="1">Sí</option></select>
            </label>
            <label class="field"><span class="label">¿Final?</span>
              <select class="select" id="final"><option value="0">No</option><option value="1">Sí</option></select>
            </label>
            <label class="field"><span class="label">Color</span><input class="input" id="color" type="text" placeholder="#60a5fa" /></label>
          </div>
          <label class="field"><span class="label">Estado</span>
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
          codigo:document.getElementById('codigo'),
          nombre:document.getElementById('nombre'),
          orden:document.getElementById('orden'),
          inicial:document.getElementById('inicial'),
          final:document.getElementById('final'),
          color:document.getElementById('color'),
          estado:document.getElementById('estado'),
        };
        const open=()=>{modal.classList.add('is-open');modal.setAttribute('aria-hidden','false');};
        const close=()=>{modal.classList.remove('is-open');modal.setAttribute('aria-hidden','true');};
        document.querySelectorAll('[data-close]').forEach(b=>b.onclick=close);
        window.addEventListener('keydown',e=>{if(e.key==='Escape')close();});
        function norm(t){return t.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,'');}
        const badgeEstado=v=>v?'<span class="badge badge-green">Activo</span>':'<span class="badge badge-gray">Inactivo</span>';

        async function load(){
          const r=await fetch('/src/api/states/list.php'); // TODO
          const j=await r.json(); if(!j.ok){alert(j.error||'No se pudo cargar');return;}
          tbody.innerHTML=(j.data||[]).map(s=>`
            <tr data-id="${s.id}" data-estado="${s.estado}">
              <td>${s.codigo}</td>
              <td>${s.nombre}</td>
              <td>${s.orden}</td>
              <td>${+s.inicial?'Sí':'No'}</td>
              <td>${+s.final?'Sí':'No'}</td>
              <td><span class="badge">${s.color_hex||'#cccccc'}</span></td>
              <td class="td-status">${badgeEstado(+s.estado)}</td>
              <td class="actions">
                <button class="tag tag-edit" type="button">EDIT</button>
                <label class="switch">
                  <input type="checkbox" class="switch-input" ${+s.estado?'checked':''}/>
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
          frm.reset();F.id.value='';mTitle.textContent='Agregar estado';open();F.codigo.focus();
        };

        tbody.addEventListener('click',(e)=>{
          const tr=e.target.closest('tr'); if(!tr)return;
          if(e.target.classList.contains('tag-edit')){
            F.id.value=tr.dataset.id;
            F.codigo.value=tr.children[0].textContent.trim();
            F.nombre.value=tr.children[1].textContent.trim();
            F.orden.value=tr.children[2].textContent.trim();
            F.inicial.value=tr.children[3].textContent.trim()==='Sí'?'1':'0';
            F.final.value=tr.children[4].textContent.trim()==='Sí'?'1':'0';
            F.color.value=tr.children[5].textContent.trim();
            F.estado.value=tr.dataset.estado;
            mTitle.textContent='Editar estado'; open(); F.codigo.focus();
          }
        });

        tbody.addEventListener('change',async(e)=>{
          if(!e.target.classList.contains('switch-input'))return;
          const tr=e.target.closest('tr');
          const body=new FormData();
          body.append('id',tr.dataset.id);
          body.append('activo',e.target.checked?1:0);
          try{
            const r=await fetch('/src/api/states/toggle_active.php',{method:'POST',body});
            const j=await r.json(); if(!j.ok) throw new Error(j.error||'Error');
            tr.dataset.estado=e.target.checked?'1':'0';
            tr.querySelector('.td-status').innerHTML=e.target.checked?badgeEstado(1):badgeEstado(0);
          }catch(err){ alert(err.message||'No se pudo actualizar'); e.target.checked=!e.target.checked; }
        });

        frm.addEventListener('submit',async(e)=>{
          e.preventDefault();
          const id=F.id.value.trim();
          const body=new FormData();
          body.append('codigo',F.codigo.value.trim());
          body.append('nombre',F.nombre.value.trim());
          body.append('orden',F.orden.value.trim());
          body.append('inicial',F.inicial.value);
          body.append('final',F.final.value);
          body.append('color_hex',F.color.value.trim());
          body.append('estado',F.estado.value);
          try{
            const url=id?'/src/api/states/update.php':'/src/api/states/create.php';
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
