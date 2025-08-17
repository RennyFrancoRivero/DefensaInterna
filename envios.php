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
    <title>TRUFIX | Encomiendas</title>
    <link rel="stylesheet" href="styles/Usuarios.css" />
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
            <li><a class="menu__link is-active" href="#" aria-current="page">Encomiendas</a></li>
            <li><a class="menu__link" href="estados.php">Estados de envío</a></li>
            <li><a class="menu__link" href="oficinas.php">Oficinas</a></li>
            <li><a class="menu__link" href="clientes.php">Clientes</a></li>
            <li><a class="menu__link" href="Usuarios.php">Usuarios</a></li>
          </ul>
        </nav>
        <div class="sidebar__user">
          <div class="user-info">
            <div class="user-avatar" aria-hidden="true"><span class="user-icon">🚚</span></div>
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
          <h1 class="page-title">Encomiendas</h1>
          <div class="page-actions">
            <div class="input-with-icon"><input id="q" class="input" placeholder="Buscar envío (código/cliente/destinatario)" /></div>
            <button class="btn btn-success" id="btnAdd" type="button">REGISTRAR</button>
          </div>
        </header>

        <section class="card">
          <div class="table-responsive">
            <table class="table" id="tbl">
              <thead>
                <tr>
                  <th>Código</th>
                  <th>Cliente (remitente)</th>
                  <th>Destinatario</th>
                  <th>Tipo</th>
                  <th>Ruta</th>
                  <th>Estado</th>
                  <th>Fecha</th>
                  <th class="th-actions">Actions</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </section>
      </main>
    </div>

    <!-- Modal Registrar Envío -->
    <div id="modal" class="modal" aria-hidden="true">
      <div class="modal__backdrop" data-close></div>
      <div class="modal__dialog modal--vertical" role="dialog" aria-modal="true" aria-labelledby="mTitle">
        <header class="modal__header">
          <h2 id="mTitle">Registrar envío</h2>
          <button class="modal__close" type="button" data-close>×</button>
        </header>
        <form id="frm" class="form form--stacked" autocomplete="off">
          <div class="grid-2">
            <label class="field"><span class="label">Cliente (remitente)</span>
              <select class="select" id="cliente_id" required></select>
            </label>
            <label class="field"><span class="label">Tipo de encomienda</span>
              <select class="select" id="tipo_id" required></select>
            </label>
          </div>

          <div class="grid-2">
            <label class="field"><span class="label">Oficina origen</span>
              <select class="select" id="ofi_origen" required></select>
            </label>
            <label class="field"><span class="label">Oficina destino</span>
              <select class="select" id="ofi_destino" required></select>
            </label>
          </div>

          <div class="grid-2">
            <label class="field"><span class="label">Ruta</span>
              <select class="select" id="ruta_id" required></select>
            </label>
            <label class="field"><span class="label">Vehículo/Conductor (opcional)</span>
              <select class="select" id="vc_id"></select>
            </label>
          </div>

          <div class="grid-2">
            <label class="field"><span class="label">Peso (kg)</span><input class="input" id="peso" type="number" min="0" step="0.01" required /></label>
            <label class="field"><span class="label">Valor declarado</span><input class="input" id="valor" type="number" min="0" step="0.01" required /></label>
          </div>

          <label class="field"><span class="label">Descripción del contenido</span><input class="input" id="desc" /></label>

          <div class="grid-2">
            <label class="field"><span class="label">Destinatario - Nombre</span><input class="input" id="dest_nom" required /></label>
            <label class="field"><span class="label">Destinatario - Teléfono</span><input class="input" id="dest_tel" /></label>
          </div>
          <label class="field"><span class="label">Destinatario - Dirección</span><input class="input" id="dest_dir" /></label>

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
          cliente_id:document.getElementById('cliente_id'),
          tipo_id:document.getElementById('tipo_id'),
          ofi_origen:document.getElementById('ofi_origen'),
          ofi_destino:document.getElementById('ofi_destino'),
          ruta_id:document.getElementById('ruta_id'),
          vc_id:document.getElementById('vc_id'),
          peso:document.getElementById('peso'),
          valor:document.getElementById('valor'),
          desc:document.getElementById('desc'),
          dest_nom:document.getElementById('dest_nom'),
          dest_tel:document.getElementById('dest_tel'),
          dest_dir:document.getElementById('dest_dir'),
        };

        const open=()=>{modal.classList.add('is-open');modal.setAttribute('aria-hidden','false');};
        const close=()=>{modal.classList.remove('is-open');modal.setAttribute('aria-hidden','true');};
        document.querySelectorAll('[data-close]').forEach(b=>b.onclick=close);
        window.addEventListener('keydown',e=>{if(e.key==='Escape')close();});

        function norm(t){return t.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,'');}
        const badgeEstado = s => `<span class="badge">${s}</span>`;

        async function loadMin(){
          // TODO: ajusta endpoints de util
          const [clientes, tipos, ofis, rutas, vcs] = await Promise.all([
            fetch('/src/api/util/clientes_min.php').then(r=>r.json()),
            fetch('/src/api/util/tipos_encomienda_min.php').then(r=>r.json()),
            fetch('/src/api/util/oficinas_min.php').then(r=>r.json()),
            fetch('/src/api/util/rutas_min.php').then(r=>r.json()),
            fetch('/src/api/util/vehiculo_conductores_min.php').then(r=>r.json()),
          ]);
          if(!clientes.ok||!tipos.ok||!ofis.ok||!rutas.ok||!vcs.ok) throw new Error('No se pudieron cargar catálogos');
          F.cliente_id.innerHTML = clientes.data.map(x=>`<option value="${x.id}">${x.nombre}</option>`).join('');
          F.tipo_id.innerHTML    = tipos.data.map(x=>`<option value="${x.id}">${x.nombre}</option>`).join('');
          F.ofi_origen.innerHTML = ofis.data.map(x=>`<option value="${x.id}">${x.nombre}</option>`).join('');
          F.ofi_destino.innerHTML= F.ofi_origen.innerHTML;
          F.ruta_id.innerHTML    = rutas.data.map(x=>`<option value="${x.id}">${x.nombre}</option>`).join('');
          F.vc_id.innerHTML      = `<option value="">(sin asignar)</option>` + vcs.data.map(x=>`<option value="${x.id}">${x.nombre}</option>`).join('');
        }

        async function load(){
          const r=await fetch('/src/api/shipments/list.php'); // TODO
          const j=await r.json(); if(!j.ok){alert(j.error||'No se pudo cargar');return;}
          tbody.innerHTML=(j.data||[]).map(v=>`
            <tr data-id="${v.id}">
              <td>${v.codigo}</td>
              <td>${v.cliente}</td>
              <td>${v.destinatario}</td>
              <td>${v.tipo}</td>
              <td>${v.ruta}</td>
              <td>${badgeEstado(v.estado)}</td>
              <td>${v.fecha??''}</td>
              <td class="actions">
                <!-- En MVP solo registro; edición/historial se puede agregar luego -->
                <button class="tag" type="button" title="Ver">VIEW</button>
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

        document.getElementById('btnAdd').onclick=async()=>{
          frm.reset(); mTitle.textContent='Registrar envío';
          await loadMin(); open(); F.cliente_id.focus();
        };

        frm.addEventListener('submit', async (e)=>{
          e.preventDefault();
          const body=new FormData();
          body.append('cliente_id',F.cliente_id.value);
          body.append('tipo_id',F.tipo_id.value);
          body.append('ofi_origen',F.ofi_origen.value);
          body.append('ofi_destino',F.ofi_destino.value);
          body.append('ruta_id',F.ruta_id.value);
          if(F.vc_id.value) body.append('vc_id',F.vc_id.value);
          body.append('peso',F.peso.value);
          body.append('valor',F.valor.value);
          body.append('descripcion',F.desc.value.trim());
          body.append('dest_nom',F.dest_nom.value.trim());
          body.append('dest_tel',F.dest_tel.value.trim());
          body.append('dest_dir',F.dest_dir.value.trim());
          try{
            const r=await fetch('/src/api/shipments/create.php',{method:'POST',body}); // TODO
            const j=await r.json(); if(!j.ok) throw new Error(j.error||'Error');
            close(); await load();
          }catch(err){ alert(err.message||'No se pudo registrar'); }
        });

        load();
      })();
    </script>
  </body>
</html>
