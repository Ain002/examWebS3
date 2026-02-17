<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>ExamWebS3</title>
  <link rel="stylesheet" href="/css/exam.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

    :root {
      --sidebar-w: 230px;
      --sidebar-bg: #0f172a;
      --sidebar-border: rgba(255,255,255,0.06);
      --accent: #6366f1;
      --accent-soft: rgba(99,102,241,0.12);
      --text-main: #f1f5f9;
      --text-muted: #94a3b8;
      --bg: #f8fafc;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html, body { height: 100%; font-family: 'DM Sans', system-ui, sans-serif; background: var(--bg); color: #1e293b; }
    .app { display: flex; height: 100vh; overflow: hidden; }

    /* SIDEBAR */
    .sidebar {
      width: var(--sidebar-w); flex-shrink: 0;
      background: var(--sidebar-bg);
      display: flex; flex-direction: column;
      overflow-y: auto;
      border-right: 1px solid var(--sidebar-border);
    }
    .brand {
      padding: 22px 20px 16px; font-size: 15px; font-weight: 700;
      color: var(--text-main); border-bottom: 1px solid var(--sidebar-border);
      display: flex; align-items: center; gap: 10px;
    }
    .brand-icon {
      width: 30px; height: 30px; background: var(--accent); border-radius: 8px;
      display: flex; align-items: center; justify-content: center; font-size: 14px;
    }
    .menu-section { padding: 12px 12px 6px; font-size: 10px; font-weight: 600; letter-spacing: 1.2px; color: var(--text-muted); text-transform: uppercase; }
    .menu { list-style: none; padding: 4px 10px; flex: 1; }
    .menu li { margin: 2px 0; }
    .menu a {
      display: flex; align-items: center; gap: 10px; padding: 9px 12px; border-radius: 8px;
      color: var(--text-muted); text-decoration: none; font-size: 14px; font-weight: 500;
      transition: all 150ms ease; position: relative;
    }
    .menu a:hover { background: rgba(255,255,255,0.05); color: var(--text-main); }
    .menu a.active { background: var(--accent-soft); color: #a5b4fc; }
    .menu a.active::before {
      content: ''; position: absolute; left: 0; top: 50%; transform: translateY(-50%);
      width: 3px; height: 60%; background: var(--accent); border-radius: 0 3px 3px 0;
    }
    .menu .icon { width: 20px; text-align: center; font-size: 15px; flex-shrink: 0; }

    /* ZONE CENTRALE */
    .main-wrapper { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
    .topbar {
      height: 54px; flex-shrink: 0; background: white; border-bottom: 1px solid #e2e8f0;
      display: flex; align-items: center; padding: 0 28px; gap: 12px;
    }
    #pageTitle { font-size: 15px; font-weight: 600; color: #1e293b; }
    .topbar-path { font-size: 13px; color: #94a3b8; font-family: 'DM Mono', monospace; }

    #loadingBar {
      position: fixed; top: 0; left: 0; height: 2px; width: 0%;
      background: var(--accent); transition: width 0.25s ease; z-index: 9999;
    }
    .content { flex: 1; overflow-y: auto; padding: 28px; }
    #pageContent { max-width: 1080px; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
    .page-loaded { animation: fadeIn 0.2s ease; }
    .skeleton {
      background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
      background-size: 200% 100%; animation: shimmer 1.2s infinite;
      border-radius: 6px; height: 20px; margin-bottom: 10px;
    }
    @keyframes shimmer { 0% { background-position: -200% 0; } 100% { background-position: 200% 0; } }

    /* FOOTER SIDEBAR */
    .sidebar-footer {
      padding: 12px 16px; background: rgba(0,0,0,0.2); border-top: 1px solid var(--sidebar-border);
      font-size: 11px; color: var(--text-muted);
    }
    .sidebar-footer .members { display: flex; flex-direction: column; gap: 3px; }
    .sidebar-footer .member span { color: var(--text-main); font-weight: 600; font-family: 'DM Mono', monospace; }
    .sidebar-footer .copy { margin-top: 6px; color: #475569; font-size: 10px; }

    @media (max-width: 700px) {
      .app { flex-direction: column; }
      .sidebar { width: 100%; height: auto; flex-direction: row; overflow-x: auto; border-right: none; border-bottom: 1px solid var(--sidebar-border); }
      .menu { display: flex; flex-direction: row; padding: 6px; }
      .menu li { margin: 0; }
      .menu-section, .sidebar-footer { display: none; }
      .brand { padding: 12px 16px; border-bottom: none; }
    }
  </style>
</head>
<body>
  <div id="loadingBar"></div>
  <div class="app">

    <!-- ═══ SIDEBAR ═══ -->
    <aside class="sidebar">
      <div class="brand">
        <div class="brand-icon">📈</div>
        <span>BNGRC</span>
      </div>
      <nav style="flex:1;">
        <div class="menu-section">Navigation</div>
        <ul class="menu">
          <?php
          $nav = [
            'dashboard'      => ['icon'=>'📊', 'label'=>'Tableau de bord', 'url'=>'/dashboard'],
            'region'         => ['icon'=>'🗺️', 'label'=>'Région',          'url'=>'/region'],
            'ville'          => ['icon'=>'🏙️', 'label'=>'Ville',           'url'=>'/ville'],
            'don'            => ['icon'=>'🎁', 'label'=>'Don',              'url'=>'/don'],
            'recapitulatif'  => ['icon'=>'📈', 'label'=>'Récapitulatif',   'url'=>'/recapitulatif'],
            'besoinRestant'  => ['icon'=>'📋', 'label'=>'Besoins restants','url'=>'/besoin/restant'],
          ];
          // data-page utilisé par le JS pour le fetch
          $dataPageMap = [
            'dashboard'     => 'dashboard',
            'region'        => 'region',
            'ville'         => 'ville',
            'don'           => 'don',
            'recapitulatif' => 'recapitulatif',
            'besoinRestant' => 'besoin/restant',
          ];
          foreach ($nav as $key => $item):
            $active = (($currentView ?? 'dashboard') === $key) ? 'active' : '';
          ?>
          <li>
            <a href="<?= $item['url'] ?>"
               data-page="<?= $dataPageMap[$key] ?>"
               class="<?= $active ?>">
              <span class="icon"><?= $item['icon'] ?></span>
              <span><?= $item['label'] ?></span>
            </a>
          </li>
          <?php endforeach; ?>
        </ul>
      </nav>
      <div class="sidebar-footer">
        <div class="members">
          <div class="member">Nia — <span>ETU003925</span></div>
          <div class="member">Randianina — <span>ETU004060</span></div>
          <div class="member">Sundy — <span>ETU003912</span></div>
        </div>
        <div class="copy">ExamWebS3 &copy; <?= date('Y') ?></div>
      </div>
    </aside>

    <!-- ═══ ZONE CENTRALE ═══ -->
    <div class="main-wrapper">
      <div class="topbar">
        <span id="pageTitle"><?= htmlspecialchars($nav[$currentView ?? 'dashboard']['label'] ?? 'Tableau de bord') ?></span>
        <span class="topbar-path" id="topbarPath"><?= htmlspecialchars($nav[$currentView ?? 'dashboard']['url'] ?? '/dashboard') ?></span>
      </div>
      <main class="content" id="mainContent">
        <div id="pageContent" class="page-loaded">
<?php
/*
 * INCLUSION DU FRAGMENT
 * Toutes les variables de $data ont été extraites par renderPage()
 * via extract(), donc $regions, $villes, $dons, etc. sont disponibles ici.
 * On inclut simplement le fichier de vue correspondant.
 */
$__viewFile = __DIR__ . '/' . ($currentView ?? 'dashboard') . '.php';
if (file_exists($__viewFile)) {
    include $__viewFile;
} else {
    echo '<p style="color:red;">Vue introuvable : ' . htmlspecialchars($__viewFile) . '</p>';
}
?>
        </div>
      </main>
    </div>

  </div><!-- .app -->

  <script>
    const bar = document.getElementById('loadingBar');
    function startLoad() { bar.style.width = '60%'; }
    function endLoad()   { bar.style.width = '100%'; setTimeout(() => bar.style.width = '0%', 250); }

    const pageTitles = {
      'dashboard':      'Tableau de bord',
      'region':         'Régions',
      'ville':          'Villes',
      'don':            'Dons',
      'recapitulatif':  'Récapitulatif',
      'besoin/restant': 'Besoins restants',
    };

    async function loadPage(pageKey, pushState = true) {
      const container = document.getElementById('pageContent');
      const titleEl   = document.getElementById('pageTitle');
      const pathEl    = document.getElementById('topbarPath');

      startLoad();
      container.classList.remove('page-loaded');
      container.innerHTML = `
        <div class="skeleton" style="width:40%;height:28px;margin-bottom:20px;"></div>
        <div class="skeleton" style="width:100%;"></div>
        <div class="skeleton" style="width:85%;"></div>
        <div class="skeleton" style="width:70%;"></div>`;

      try {
        const res = await fetch('/' + pageKey, {
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
          cache: 'no-store'
        });
        if (!res.ok) throw new Error('HTTP ' + res.status);

        const html = await res.text();
        container.innerHTML = html;
        container.classList.add('page-loaded');

        const title = pageTitles[pageKey] || pageKey;
        titleEl.textContent = title;
        pathEl.textContent  = '/' + pageKey;
        document.title      = title + ' — ExamWebS3';

        if (pushState) history.pushState({ pageKey }, '', '/' + pageKey);

      } catch (err) {
        container.innerHTML = `
          <div style="padding:40px;text-align:center;color:#ef4444;">
            <div style="font-size:48px;margin-bottom:12px;">⚠️</div>
            <h2>Impossible de charger la page</h2>
            <p style="color:#94a3b8;margin-top:8px;">${err.message}</p>
          </div>`;
      } finally { endLoad(); }
    }

    // Clics dans la sidebar
    document.querySelectorAll('.menu a').forEach(link => {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('.menu a').forEach(a => a.classList.remove('active'));
        this.classList.add('active');
        loadPage(this.dataset.page);
      });
    });

    // Bouton retour/avant navigateur
    window.addEventListener('popstate', e => {
      if (e.state?.pageKey) {
        document.querySelectorAll('.menu a').forEach(a =>
          a.classList.toggle('active', a.dataset.page === e.state.pageKey));
        loadPage(e.state.pageKey, false);
      }
    });
  </script>
</body>
</html>