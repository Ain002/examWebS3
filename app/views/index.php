<?php
// Page statique d'exemple pour afficher un menu vertical et le contenu central
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>ExamWebS3 — Tableau de bord</title>
  <style>
    :root{
      --bg:#f6f8fb;
      --sidebar:#1f2937;
      --accent:#2563eb;
      --muted:#6b7280;
      --white:#ffffff;
    }
    html,body{height:100%;margin:0;font-family:Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; background:var(--bg); color:#111827}
    .app{display:flex;height:100vh;}
    .sidebar{width:220px;background:linear-gradient(180deg,var(--sidebar), #111827);color:var(--white);padding:18px 12px;box-shadow:2px 0 8px rgba(0,0,0,0.06);}
    .brand{font-weight:700;letter-spacing:0.4px;margin-bottom:18px;font-size:18px}
    .menu{list-style:none;padding:0;margin:0}
    .menu li{margin:6px 0}
  .menu a, .menu button{all:unset;display:flex;align-items:center;width:100%;padding:10px 12px;border-radius:8px;cursor:pointer;color:var(--white);transition:background 160ms}
  .menu a:hover, .menu button:hover{background:rgba(255,255,255,0.04)}
  .menu a.active, .menu button.active{background:var(--white);color:#0f172a}
    .menu .icon{width:26px;text-align:center;margin-right:10px;opacity:0.95}
    .content{flex:1;padding:28px;overflow:auto}
    .card{background:var(--white);border-radius:10px;padding:20px;box-shadow:0 6px 18px rgba(15,23,42,0.06);max-width:1000px}
    h1{margin:0 0 12px 0;font-size:20px}
    p{margin:0;color:var(--muted)}
    /* responsive */
    @media (max-width:720px){
      .app{flex-direction:column}
      .sidebar{width:100%;display:flex;overflow:auto}
      .menu{display:flex;gap:8px}
  .menu a, .menu button{white-space:nowrap}
    }
  </style>
</head>
<body>
  <div class="app">
    <aside class="sidebar" aria-label="Menu principal">
      <div class="brand">ExamWebS3</div>
      <nav>
        <ul class="menu" role="menu">
          <li role="none"><a role="menuitem" data-page="dashboard" class="active" href="/dashboard"><span class="icon">📊</span><span>Tableau de bord</span></a></li>
          <li role="none"><a role="menuitem" data-page="region" href="/region"><span class="icon">🗺️</span><span>Région</span></a></li>
          <li role="none"><a role="menuitem" data-page="ville" href="/ville"><span class="icon">🏙️</span><span>Ville</span></a></li>
          <li role="none"><a role="menuitem" data-page="don" href="/don"><span class="icon">🎁</span><span>Don</span></a></li>
        </ul>
      </nav>
    </aside>

    <main class="content" id="mainContent" tabindex="0">
      <section class="card" id="pageContent">
        <?php

        $initial = __DIR__ . '/dashboard.php';
        if(file_exists($initial)){
            include $initial;
        } else {
            echo '<h1>Tableau de bord</h1><p>Bienvenue sur le tableau de bord.</p>';
        }
        ?>
      </section>
    </main>
  </div>

  <?php include __DIR__ . '/footer.php'; ?>
  <script>
    // base URL pour les fetch depuis le navigateur (vide -> racine)
    window.BASE_URL = '';
    (function(){

      function setActive(button){
        document.querySelectorAll('.menu button')
          .forEach(b => b.classList.remove('active'));
        button.classList.add('active');
      }

      // Construit une URL absolue robuste même si BASE_URL contient ou non un slash
      function buildUrl(pageKey){
        const base = (window.BASE_URL || '').toString();
        // normaliser les segments
        const cleanBase = base.replace(/^\/?|\/?$/g, '');
        const parts = [];
        parts.push(location.origin.replace(/\/$/, ''));
        if(cleanBase) parts.push(cleanBase);
        parts.push(encodeURIComponent(pageKey));
        // join avec slash unique
        return parts.join('/').replace(/([^:]\/)\//g, '$1');
      }

      async function loadPage(pageKey){
        const container = document.getElementById('pageContent');
        const url = buildUrl(pageKey);
        console.log('[app] loadPage ->', pageKey, url);

        try{
          const res = await fetch(url, { cache: 'no-store' });

          if(!res.ok) throw new Error('Erreur '+res.status+' pour '+url);

          const html = await res.text();
          container.innerHTML = html;
          console.log('[app] page chargée:', pageKey, 'taille', html.length);

          // focus container and update hash
          document.getElementById('mainContent').focus();
          history.replaceState(null, '', '#'+pageKey);

        }catch(err){
          container.innerHTML = '<h1>Erreur</h1><p>Impossible de charger la vue.</p>';
          console.error('[app] erreur loadPage', err);
        }
      }

      // Support both buttons and links; prefer links (<a>) for progressive enhancement
      document.querySelectorAll('.menu a, .menu button').forEach(el => {
        el.addEventListener('click', function(e){
          e.preventDefault();
          setActive(this);
          const page = this.dataset ? this.dataset.page : null;
          if(page) loadPage(page);
        });
      });

      const initialHash = (location.hash && location.hash.slice(1)) || 'dashboard';
      // chercher d'abord un lien <a>, puis un bouton
      let initialEl = document.querySelector('.menu a[data-page="'+initialHash+'"]') || document.querySelector('.menu button[data-page="'+initialHash+'"]');

      if(initialEl){
        setActive(initialEl);
        loadPage(initialHash);
      } else {
        // si aucun élément trouvé, charger le dashboard par défaut
        const defaultEl = document.querySelector('.menu a[data-page="dashboard"], .menu button[data-page="dashboard"]');
        if(defaultEl) setActive(defaultEl);
        loadPage('dashboard');
      }

    })();
  </script>
</body>
</html>
