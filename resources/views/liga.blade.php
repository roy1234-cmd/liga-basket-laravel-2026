<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="api-base" content="{{ url('/api') }}">
  <title>Liga Pro · Administración de básquetbol</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🏀</text></svg>">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <script defer src="{{ asset('js/app.js') }}"></script>
</head>
<body>
  <aside class="sidebar">
    <div class="brand"><span class="brand-symbol">🏀</span><span>LIGA<span class="brand-accent">PRO</span><small>SPORTS MANAGEMENT</small></span></div>
    <div class="nav-heading">ESPACIO DE TRABAJO</div>
    <nav aria-label="Menú principal">
      <button class="nav-link active" data-view="inicio"><span>▦</span> Resumen</button>
      <button class="nav-link" data-view="equipos"><span>◈</span> Equipos</button>
      <button class="nav-link" data-view="jugadores"><span>♙</span> Jugadores</button>
      <button class="nav-link" data-view="partidos"><span>▣</span> Partidos</button>
      <button class="nav-link" data-view="posiciones"><span>♛</span> Clasificación</button>
      <button class="nav-link" data-view="respaldo"><span>⇪</span> Respaldo</button>
    </nav>
    <div class="sidebar-info"><span class="pulse"></span><div><strong>Sistema operativo</strong><small>Laravel · Sin MySQL</small></div></div>
    <div class="sidebar-user"><span class="avatar">AD</span><span><strong>Administrador</strong><small>Temporada 2026</small></span></div>
  </aside>

  <main class="main">
    <header class="topbar">
      <div><p class="overline">PANEL DE ADMINISTRACIÓN / TEMPORADA 2026</p><h1 id="page-title">Centro de operaciones</h1><p class="subtitle" id="page-subtitle">Toda tu liga en un solo lugar.</p></div>
      <div class="topbar-right"><span class="date-chip" id="today-date"></span><button class="btn btn-primary" data-go="partidos">＋ Nuevo partido</button></div>
    </header>

    <section id="view-inicio" class="view">
      <div class="hero">
        <div class="hero-copy"><span class="hero-tag">● LIGA EN MARCHA</span><h2>La pasión se vive<br><em>dentro de la cancha.</em></h2><p>Organiza los equipos, lleva los resultados y sigue cada victoria en tiempo real.</p><button class="btn btn-white" data-go="posiciones">Ver tabla de posiciones <span>↗</span></button></div>
        <div class="hero-graphic" aria-hidden="true"><div class="hero-circle"><span>🏀</span></div><div class="hero-line"></div></div>
      </div>
      <div class="stats-grid">
        <article class="stat-card"><span class="stat-icon amber">◈</span><span class="stat-caption">EQUIPOS</span><strong id="stat-equipos">0</strong><small>Clubes registrados</small></article>
        <article class="stat-card"><span class="stat-icon violet">♙</span><span class="stat-caption">JUGADORES</span><strong id="stat-jugadores">0</strong><small>En la competencia</small></article>
        <article class="stat-card"><span class="stat-icon cyan">▣</span><span class="stat-caption">PROGRAMADOS</span><strong id="stat-partidos">0</strong><small>Próximos encuentros</small></article>
        <article class="stat-card"><span class="stat-icon green">✓</span><span class="stat-caption">FINALIZADOS</span><strong id="stat-finalizados">0</strong><small>Resultados oficiales</small></article>
      </div>
      <div class="dashboard-grid">
        <div class="panel"><div class="panel-heading"><div><p class="mini-label">CALENDARIO</p><h3>Próximos encuentros</h3></div><button class="text-button" data-go="partidos">Ver todos ↗</button></div><div id="home-matches" class="table-wrap"></div></div>
        <div class="panel"><div class="panel-heading"><div><p class="mini-label">COMPETENCIA</p><h3>Líderes del torneo</h3></div><button class="text-button" data-go="posiciones">Clasificación ↗</button></div><div id="home-standings"></div></div>
      </div>
    </section>

    <section id="view-equipos" class="view hidden">
      <div class="panel"><div class="panel-heading"><div><p class="mini-label">01 / EQUIPOS</p><h3>Clubes participantes</h3><p>Registra nuevos equipos y modifica sus datos.</p></div><span class="heading-icon">◈</span></div>
        <form id="form-equipo" class="form-grid"><label>Nombre del equipo<input name="nombre" required minlength="2" maxlength="60" placeholder="Ej. Cóndores Abancay"></label><label>Entrenador<input name="entrenador" maxlength="60" placeholder="Nombre del entrenador"></label><label>Ciudad<input name="ciudad" maxlength="60" placeholder="Ej. Abancay"></label><button class="btn btn-primary form-button">＋ Registrar equipo</button></form>
        <div class="table-toolbar"><h4>Equipos registrados <span id="count-equipos" class="count">0</span></h4><input class="search-input" data-search="equipos" placeholder="⌕  Buscar equipo, ciudad..." aria-label="Buscar equipos"></div><div id="list-equipos" class="table-wrap"></div>
      </div>
    </section>

    <section id="view-jugadores" class="view hidden">
      <div class="panel"><div class="panel-heading"><div><p class="mini-label">02 / PLANTILLAS</p><h3>Registro de jugadores</h3><p>Asigna dorsales y organiza cada plantilla.</p></div><span class="heading-icon">♙</span></div>
        <form id="form-jugador" class="form-grid"><label>Nombre completo<input name="nombre" required minlength="2" maxlength="80" placeholder="Nombre del jugador"></label><label>Número de camiseta<input name="numero" type="number" required min="0" max="99" placeholder="Ej. 23"></label><label>Equipo<select name="equipo" id="jugador-equipo" required></select></label><button class="btn btn-primary form-button">＋ Añadir jugador</button></form>
        <div class="table-toolbar"><h4>Plantilla de la liga <span id="count-jugadores" class="count">0</span></h4><input class="search-input" data-search="jugadores" placeholder="⌕  Buscar jugador o equipo..." aria-label="Buscar jugadores"></div><div id="list-jugadores" class="table-wrap"></div>
      </div>
    </section>

    <section id="view-partidos" class="view hidden">
      <div class="panel"><div class="panel-heading"><div><p class="mini-label">03 / COMPETICIÓN</p><h3>Calendario y resultados</h3><p>Programa partidos, modifica fechas y registra marcadores.</p></div><span class="heading-icon">▣</span></div>
        <form id="form-partido" class="form-grid"><label>Equipo local<select name="local" id="partido-local" required></select></label><label>Equipo visitante<select name="visitante" id="partido-visitante" required></select></label><label>Fecha del partido<input type="date" name="fecha" id="partido-fecha" required></label><button class="btn btn-primary form-button">＋ Programar partido</button></form>
        <div class="table-toolbar"><h4>Todos los encuentros <span id="count-partidos" class="count">0</span></h4><div class="filters"><select id="match-filter" aria-label="Filtrar partidos"><option value="todos">Todos</option><option value="programado">Programados</option><option value="finalizado">Finalizados</option></select><input class="search-input" data-search="partidos" placeholder="⌕  Buscar equipos..." aria-label="Buscar partidos"></div></div><div id="list-partidos" class="table-wrap"></div>
      </div>
    </section>

    <section id="view-posiciones" class="view hidden"><div class="panel"><div class="panel-heading"><div><p class="mini-label">04 / CLASIFICACIÓN</p><h3>Tabla de posiciones</h3><p>Clasificación calculada automáticamente con los resultados guardados.</p></div><span class="heading-icon">♛</span></div><div id="list-posiciones" class="table-wrap"></div><p class="footnote">Criterio: victorias, diferencia de puntos y puntos anotados. PJ: jugados · PG: ganados · PP: perdidos · PF/PC: puntos a favor/en contra.</p></div></section>

    <section id="view-respaldo" class="view hidden"><div class="panel"><div class="panel-heading"><div><p class="mini-label">05 / SEGURIDAD DE DATOS</p><h3>Copias de seguridad</h3><p>Toda la información se guarda en un archivo JSON en el servidor, sin MySQL.</p></div><span class="heading-icon">⇪</span></div><div class="backup-grid"><div class="backup-card"><span class="backup-icon">↓</span><h4>Descargar respaldo</h4><p>Obtén un archivo JSON con equipos, jugadores y partidos para guardarlo en tu PC.</p><button id="export-btn" class="btn btn-primary">Descargar JSON</button></div><div class="backup-card"><span class="backup-icon">↑</span><h4>Restaurar respaldo</h4><p>Importa un archivo JSON de esta aplicación. Los registros actuales serán reemplazados.</p><form id="form-import"><input type="file" name="archivo" accept=".json,application/json" required><button class="btn btn-dark">Restaurar datos</button></form></div></div></div></section>

    <footer>© 2026 Liga Pro <span>•</span> Gestión deportiva con Laravel <span>•</span> Proyecto académico</footer>
  </main>

  <dialog id="record-dialog"><form id="form-dialog"><header class="modal-header"><div><p class="mini-label" id="modal-kicker">EDITAR</p><h3 id="modal-title">Editar registro</h3></div><button type="button" class="icon-button" data-close aria-label="Cerrar">✕</button></header><div id="modal-fields" class="modal-fields"></div><div class="modal-actions"><button type="button" class="btn btn-ghost" data-close>Cancelar</button><button class="btn btn-primary">Guardar cambios</button></div></form></dialog>
  <div id="toast" role="status" aria-live="polite"></div>
</body>
</html>
