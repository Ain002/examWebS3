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
    .menu button{all:unset;display:flex;align-items:center;width:100%;padding:10px 12px;border-radius:8px;cursor:pointer;color:var(--white);transition:background 160ms}
    .menu button:hover{background:rgba(255,255,255,0.04)}
    .menu button.active{background:var(--white);color:#0f172a}
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
      .menu button{white-space:nowrap}
    }
  </style>
</head>
<body>
  <div class="app">
    <aside class="sidebar" aria-label="Menu principal">
      <div class="brand">ExamWebS3</div>
      <nav>
        <ul class="menu" role="menu">
          <li role="none"><button role="menuitem" data-page="dashboard" class="active"><span class="icon">📊</span><span>Tableau de bord</span></button></li>
          <li role="none"><button role="menuitem" data-page="region"><span class="icon">🗺️</span><span>Région</span></button></li>
          <li role="none"><button role="menuitem" data-page="ville"><span class="icon">🏙️</span><span>Ville</span></button></li>
          <li role="none"><button role="menuitem" data-page="don"><span class="icon">🎁</span><span>Don</span></button></li>
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

  <script>
    (function(){

      function setActive(button){
        document.querySelectorAll('.menu button')
          .forEach(b => b.classList.remove('active'));
        button.classList.add('active');
      }

      async function loadPage(pageKey){
        const container = document.getElementById('pageContent');

        try{
          const res = await fetch(
            BASE_URL + '/' + encodeURIComponent(pageKey),
            { cache: 'no-store' }
          );

          if(!res.ok) throw new Error('Erreur '+res.status);

          const html = await res.text();
          container.innerHTML = html;

          document.getElementById('mainContent').focus();
          history.replaceState(null, '', '#'+pageKey);

        }catch(err){
          container.innerHTML = '<h1>Erreur</h1><p>Impossible de charger la vue.</p>';
          console.error(err);
        }
      }

      document.querySelectorAll('.menu button').forEach(btn => {
        btn.addEventListener('click', function(){
          setActive(this);
          loadPage(this.dataset.page);
        });
      });

      const initialHash = (location.hash && location.hash.slice(1)) || 'dashboard';
      const initialButton = document.querySelector('.menu button[data-page="'+initialHash+'"]');

      if(initialButton){
        setActive(initialButton);
        loadPage(initialHash);
      } else {
        loadPage('dashboard');
      }

    })();
  </script>
</body>
</html>
