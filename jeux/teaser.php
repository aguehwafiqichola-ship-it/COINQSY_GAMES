<?php
session_start();
include '../bd/bd.php';  // ← CORRECTION : connexion à la base
?>

<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Coinqsy Rush – Teaser Officiel | Coinqsy Games</title>
  
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
  
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to bottom, #05050f, #0a001a);
      color: white;
      overflow-x: hidden;
    }
    .neon-title {
      background: linear-gradient(90deg, #ffd700, #ffaa00, #ff6b6b, #a855f7, #00ff9d);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      text-shadow: 0 0 40px rgba(255,215,0,0.8);
    }
    .glow-yellow { filter: drop-shadow(0 0 25px #ffd700); }
    .glow-cyan  { filter: drop-shadow(0 0 25px #00ff9d); }
    .glow-purple { filter: drop-shadow(0 0 25px #a855f7); }
    .skin-option {
      transition: all 0.3s ease;
      cursor: pointer;
    }
    .skin-option:hover {
      transform: scale(1.08);
      box-shadow: 0 0 35px rgba(168,85,247,0.7);
    }
    .skin-active {
      border: 4px solid #ffd700 !important;
      box-shadow: 0 0 35px #ffd700 !important;
    }
    .skin-desc {
      font-size: 0.95rem;
      color: #d1d5db;
      min-height: 80px;
    }
  </style>
</head>
<body class="min-h-screen flex flex-col">

  <!-- Header -->
  <header class="fixed top-0 left-0 right-0 z-50 bg-black/80 backdrop-blur-xl border-b border-purple-900/60">
    <div class="max-w-7xl mx-auto px-6 flex justify-between items-center h-16">
      <a href="../index.php" class="flex items-center space-x-3">
        <div class="w-11 h-11 bg-gradient-to-br from-yellow-500 to-purple-600 rounded-xl flex items-center justify-center text-3xl font-black glow-yellow">C</div>
        <span class="text-2xl font-extrabold neon-title">Coinqsy Games</span>
      </a>
      <div class="flex items-center space-x-6">
        <?php if (isset($_SESSION['user_id'])): ?>
          <span class="text-cyan-300">Bonjour, <?= htmlspecialchars($_SESSION['username']) ?></span>
          <a href="../auth/logout.php" class="text-red-400 hover:text-red-300 transition">Déconnexion</a>
        <?php else: ?>
          <a href="../auth/login.php" class="hover:text-cyan-400 transition">Connexion</a>
          <a href="../auth/register.php" class="bg-gradient-to-r from-purple-600 to-cyan-500 px-6 py-2.5 rounded-xl font-semibold hover:brightness-110 transition">
            Inscription
          </a>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <!-- Message tournoi (si venu depuis un tournoi) -->
  <?php
  if (isset($_GET['tournoi_id']) && is_numeric($_GET['tournoi_id'])) {
    $tournoi_id = (int)$_GET['tournoi_id'];
    $stmt = $pdo->prepare("SELECT nom FROM tournois WHERE id = ?");
    $stmt->execute([$tournoi_id]);
    $tournoi = $stmt->fetch();
    if ($tournoi) {
      echo '<div class="max-w-6xl mx-auto mt-24 mb-8 bg-gradient-to-r from-purple-900/70 to-indigo-900/70 rounded-2xl p-6 text-center border border-purple-600/50 shadow-2xl">';
      echo '<p class="text-xl text-cyan-300 font-bold">Vous jouez pour le tournoi :</p>';
      echo '<p class="text-2xl font-black text-yellow-400 mt-2">' . htmlspecialchars($tournoi['nom']) . '</p>';
      echo '</div>';
    }
  }
  ?>

  <!-- Contenu principal -->
  <main class="flex-1 pt-12 pb-20 px-4 md:px-12 lg:px-20">

    <!-- Titre + Boutons -->
    <div class="max-w-6xl mx-auto mb-12 md:mb-16">
      <div class="flex flex-col md:flex-row items-center justify-between gap-8">
        <h1 class="text-5xl md:text-7xl lg:text-8xl font-black text-center md:text-left neon-title leading-none">
          COINQSY<br class="md:hidden"/> RUSH
        </h1>
        
        <div class="flex flex-col sm:flex-row gap-4">
          <button id="start-btn"
                  class="bg-gradient-to-r from-yellow-500 via-amber-500 to-yellow-600 hover:from-yellow-400 hover:via-amber-400 hover:to-yellow-500
                         text-black font-black text-xl md:text-3xl px-12 md:px-16 py-5 md:py-7 rounded-3xl shadow-2xl
                         transform hover:scale-105 active:scale-95 transition-all duration-300 glow-yellow whitespace-nowrap">
            LANCER COINQSY RUSH →
          </button>

          <button id="shop-btn"
                  class="bg-gradient-to-r from-purple-600 to-pink-600 hover:brightness-110
                         text-white font-bold text-xl px-10 md:px-14 py-5 md:py-7 rounded-3xl shadow-2xl
                         transform hover:scale-105 transition-all duration-300 glow-purple">
            BOUTIQUE SKINS
          </button>
        </div>
      </div>

      <p class="text-lg md:text-2xl text-gray-300 mt-8 text-center md:text-left max-w-4xl">
        Ramasse les pièces dorées pour grimper au classement !<br>
        <span class="text-yellow-400 font-medium">Score affiché = points gagnés | Vrais Coinqsy = score total / 100 (ajouté à la fin de partie)</span>
      </p>
    </div>

    <!-- Zone du jeu -->
    <div class="max-w-[1300px] mx-auto mb-16">
      <div class="relative rounded-3xl overflow-hidden border-4 border-purple-700/70 shadow-2xl shadow-purple-900/80 bg-black">
        <canvas id="rush-canvas" width="1300" height="750" class="w-full block rounded-3xl">
          Ton navigateur ne supporte pas le canvas HTML5.
        </canvas>

        <!-- HUD -->
        <div id="ui-overlay" class="absolute inset-0 pointer-events-none p-8 md:p-16 flex flex-col justify-between text-white">
          <div class="flex justify-between items-start">
            <div id="score" class="text-6xl md:text-8xl font-black text-yellow-400 glow-yellow">0</div>
            <div class="text-right">
              <div id="level" class="text-3xl md:text-5xl font-bold text-cyan-400 glow-cyan">NIVEAU 1</div>
              <div id="timer" class="text-2xl md:text-4xl text-cyan-300 mt-3">45 s</div>
            </div>
          </div>

          <div id="message-center" class="text-6xl md:text-8xl font-black text-center opacity-0 transition-opacity duration-700 drop-shadow-2xl"></div>

          <div class="flex flex-col md:flex-row justify-between items-center gap-8">
            <div id="lives" class="text-6xl md:text-8xl text-red-400 drop-shadow-lg">♥ ♥ ♥</div>
            <div id="powerup-text" class="text-3xl md:text-4xl text-purple-300 font-bold hidden">
              POWER-UP ACTIF !
            </div>
          </div>
        </div>

        <button id="restart-btn" class="hidden absolute inset-0 m-auto w-fit h-fit bg-gradient-to-r from-purple-700 via-pink-700 to-purple-800 hover:brightness-110 text-white font-black text-3xl md:text-5xl px-16 md:px-24 py-8 md:py-12 rounded-3xl shadow-2xl transform hover:scale-105 transition-all glow-purple">
          REJOUER
        </button>
      </div>

      <div class="mt-12 text-center text-xl md:text-2xl space-y-5">
        <div>Meilleur score local : <span id="local-high-score" class="text-yellow-400 font-black text-3xl glow-yellow">0</span></div>
        <a href="../classjeux/classement-jeu.php?game=coinqsy-rush" class="text-purple-300 hover:text-purple-100 underline text-2xl block mt-6 transition">
          🏆 Voir le classement mondial de Coinqsy Rush →
        </a>
      </div>
    </div>

  </main>

  <!-- Footer -->
  <footer class="bg-black/90 border-t border-purple-900/60 py-10 text-center text-gray-500 text-sm mt-auto">
    © <?= date('Y') ?> Coinqsy Games – Teaser Coinqsy Rush
  </footer>

  <!-- MODALE BOUTIQUE SKINS -->
  <div id="shop-modal" class="fixed inset-0 bg-black/80 flex items-center justify-center z-50 hidden">
    <div class="bg-gray-900/95 border-2 border-purple-700/70 rounded-3xl p-8 md:p-12 max-w-5xl w-full mx-4 max-h-[90vh] overflow-y-auto">
      <div class="flex justify-between items-center mb-8">
        <h2 class="text-4xl md:text-5xl font-black neon-title">BOUTIQUE SKINS</h2>
        <button id="close-shop" class="text-5xl text-gray-400 hover:text-white transition">×</button>
      </div>

      <p class="text-center text-lg md:text-xl text-gray-300 mb-10">
        Personnalise ton joueur ! Chaque skin a une capacité unique.<br>
        Solde actuel : <span id="shop-balance" class="text-yellow-400 font-bold text-2xl">...</span> Coinqsy
      </p>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
        <!-- Skin 1 - Or Classique -->
        <div class="skin-option bg-gray-800/50 rounded-2xl p-6 text-center border-2 border-yellow-600/50 hover:border-yellow-400 transition-all skin-active" data-skin="gold">
          <div class="w-28 h-28 mx-auto mb-4 rounded-full bg-gradient-to-br from-yellow-400 to-amber-600 glow-yellow"></div>
          <h3 class="text-xl font-bold text-yellow-400 mb-2">Or Classique</h3>
          <p class="skin-desc text-gray-400 mb-4">
            Le skin de base amélioré.<br>
            <strong>Capacité :</strong> Aucun bonus – parfait pour débuter sans avantage.
          </p>
          <p class="text-lg font-bold text-yellow-400">Gratuit</p>
          <button class="mt-4 bg-yellow-600 hover:bg-yellow-500 text-black font-bold py-3 px-8 rounded-xl transition w-full skin-active" data-skin="gold">
            Équipé
          </button>
        </div>

        <!-- Skin 2 - Néon Cyan -->
        <div class="skin-option bg-gray-800/50 rounded-2xl p-6 text-center border-2 border-cyan-600/50 hover:border-cyan-400 transition-all" data-skin="cyan">
          <div class="w-28 h-28 mx-auto mb-4 rounded-full bg-gradient-to-br from-cyan-400 to-teal-600 glow-cyan"></div>
          <h3 class="text-xl font-bold text-cyan-400 mb-2">Néon Cyan</h3>
          <p class="skin-desc text-gray-400 mb-4">
            Brille intensément.<br>
            <strong>Capacité :</strong> +20% de vitesse pendant 5 secondes après chaque power-up collecté.
          </p>
          <p class="text-lg font-bold text-yellow-400">500 Coinqsy</p>
          <button class="mt-4 bg-cyan-600 hover:bg-cyan-500 text-white font-bold py-3 px-8 rounded-xl transition w-full" data-skin="cyan" data-price="500">
            Acheter
          </button>
        </div>

        <!-- Skin 3 - Violet Mystique -->
        <div class="skin-option bg-gray-800/50 rounded-2xl p-6 text-center border-2 border-purple-600/50 hover:border-purple-400 transition-all" data-skin="purple">
          <div class="w-28 h-28 mx-auto mb-4 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 glow-purple"></div>
          <h3 class="text-xl font-bold text-purple-400 mb-2">Violet Mystique</h3>
          <p class="skin-desc text-gray-400 mb-4">
            Dégage une aura magique.<br>
            <strong>Capacité :</strong> Les pièces sont attirées de +50% plus loin (aimant permanent).
          </p>
          <p class="text-lg font-bold text-yellow-400">800 Coinqsy</p>
          <button class="mt-4 bg-purple-600 hover:bg-purple-500 text-white font-bold py-3 px-8 rounded-xl transition w-full" data-skin="purple" data-price="800">
            Acheter
          </button>
        </div>

        <!-- Skin 4 - Rouge Feu -->
        <div class="skin-option bg-gray-800/50 rounded-2xl p-6 text-center border-2 border-red-600/50 hover:border-red-400 transition-all" data-skin="fire">
          <div class="w-28 h-28 mx-auto mb-4 rounded-full bg-gradient-to-br from-red-500 to-orange-600"></div>
          <h3 class="text-xl font-bold text-red-400 mb-2">Rouge Feu</h3>
          <p class="skin-desc text-gray-400 mb-4">
            Enflamme le terrain.<br>
            <strong>Capacité :</strong> +30% de points sur chaque pièce pendant 8 secondes après collision avec un obstacle.
          </p>
          <p class="text-lg font-bold text-yellow-400">1200 Coinqsy</p>
          <button class="mt-4 bg-red-600 hover:bg-red-500 text-white font-bold py-3 px-8 rounded-xl transition w-full" data-skin="fire" data-price="1200">
            Acheter
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- JavaScript (le reste est identique à ta version) -->
  <script>
    const canvas = document.getElementById('rush-canvas');
    const ctx = canvas.getContext('2d');
    const startBtn = document.getElementById('start-btn');
    const restartBtn = document.getElementById('restart-btn');
    const scoreEl = document.getElementById('score');
    const levelEl = document.getElementById('level');
    const timerEl = document.getElementById('timer');
    const livesEl = document.getElementById('lives');
    const messageEl = document.getElementById('message-center');
    const powerupText = document.getElementById('powerup-text');
    const localHighEl = document.getElementById('local-high-score');
    const shopModal = document.getElementById('shop-modal');
    const shopBtn = document.getElementById('shop-btn');
    const closeShop = document.getElementById('close-shop');

    const sndCollect  = new Audio('https://assets.mixkit.co/sfx/preview/mixkit-coin-win-1995.mp3');
    const sndPowerup  = new Audio('https://assets.mixkit.co/sfx/preview/mixkit-magic-notification-3344.mp3');
    const sndHit      = new Audio('https://assets.mixkit.co/sfx/preview/mixkit-arcade-retro-game-over-213.mp3');
    const sndLevelUp  = new Audio('https://assets.mixkit.co/sfx/preview/mixkit-level-up-notification-271.mp3');

    let currentSkin = localStorage.getItem('rushSkin') || 'gold';

    let game = {
      running: false,
      score: 0,
      level: 1,
      time: 45 * 60,
      lives: 3,
      powerups: { magnet:0, shield:0, speed:0 },
      highScore: parseInt(localStorage.getItem('coinqsyRushHigh')) || 0,
      vrai_coins_total: 0
    };

    localHighEl.textContent = game.highScore.toLocaleString();

    let player = { x: canvas.width/2 - 35, y: canvas.height - 120, w: 70, h: 70, speed: 9 };
    let coins = [], obstacles = [], powerItems = [], particles = [];
    let keys = {};

    window.addEventListener('keydown', e => keys[e.key.toLowerCase()] = true);
    window.addEventListener('keyup',   e => keys[e.key.toLowerCase()] = false);

    function applySkin(skin) {
      currentSkin = skin;
      localStorage.setItem('rushSkin', skin);
      showMessage(`Skin ${skin} équipé !`, '#ffd700', 2000);
    }

    shopBtn.onclick = () => shopModal.classList.remove('hidden');
    closeShop.onclick = () => shopModal.classList.add('hidden');

    document.querySelectorAll('[data-skin]').forEach(btn => {
      const skin = btn.dataset.skin;
      const price = parseInt(btn.dataset.price || 0);

      if (skin === currentSkin) {
        btn.classList.add('skin-active');
        btn.textContent = 'Équipé';
      }

      btn.onclick = () => {
        if (price === 0 || currentSkin === skin) {
          applySkin(skin);
          document.querySelectorAll('[data-skin]').forEach(b => b.classList.remove('skin-active'));
          btn.classList.add('skin-active');
          btn.textContent = 'Équipé';
          return;
        }

        if (confirm(`Acheter ${skin} pour ${price} Coinqsy ?`)) {
          // Simulation achat (à remplacer par vrai appel API)
          setTimeout(() => {
            applySkin(skin);
            document.querySelectorAll('[data-skin]').forEach(b => b.classList.remove('skin-active'));
            btn.classList.add('skin-active');
            btn.textContent = 'Équipé';
            showMessage(`Skin ${skin} acheté et équipé !`, '#00ff9d', 2500);
          }, 800);
        }
      };
    });

    function spawn(type) {
      if (type === 'coin') {
        coins.push({
          x: 80 + Math.random() * (canvas.width - 160),
          y: -80,
          r: 28 + Math.random()*18,
          vy: 3.2 + game.level * 0.8
        });
      }
      else if (type === 'obstacle' && game.level > 1) {
        obstacles.push({
          x: 80 + Math.random() * (canvas.width - 160),
          y: -100,
          w: 70 + game.level * 12,
          h: 70 + game.level * 12,
          vy: 4 + game.level * 1
        });
      }
      else if (type === 'powerup' && Math.random() < 0.18) {
        const types = ['magnet','shield','speed'];
        powerItems.push({
          x: 80 + Math.random() * (canvas.width - 160),
          y: -80,
          type: types[Math.floor(Math.random()*3)],
          r: 35
        });
      }
    }

    function createParticles(x, y, color, count=25, spread=8) {
      for (let i = 0; i < count; i++) {
        particles.push({
          x, y,
          vx: (Math.random()-0.5)*spread,
          vy: (Math.random()-0.5)*spread - 4,
          life: 50 + Math.random()*30,
          color
        });
      }
    }

    function update() {
      if (!game.running) return;

      let speed = player.speed + (game.powerups.speed > 0 ? 7 : 0);
      if (keys['arrowleft'] || keys['a']) player.x -= speed;
      if (keys['arrowright'] || keys['d']) player.x += speed;
      if (keys['arrowup'] || keys['w']) player.y -= speed;
      if (keys['arrowdown'] || keys['s']) player.y += speed;

      player.x = Math.max(30, Math.min(canvas.width - player.w - 30, player.x));
      player.y = Math.max(30, Math.min(canvas.height - player.h - 30, player.y));

      coins = coins.filter(c => {
        c.y += c.vy;
        if (c.y > canvas.height + 100) return false;

        if (game.powerups.magnet > 0) {
          let dx = player.x + player.w/2 - c.x;
          let dy = player.y + player.h/2 - c.y;
          let d = Math.hypot(dx, dy);
          if (d < 220) {
            c.x += dx/d * 6;
            c.y += dy/d * 6;
          }
        }

        if (Math.hypot(c.x - (player.x+player.w/2), c.y - (player.y+player.h/2)) < 55) {
          let points_this = 150 * game.level;
          game.score += points_this;

          game.vrai_coins_total = (game.vrai_coins_total || 0) + Math.floor(points_this / 100);

          sndCollect.currentTime = 0; sndCollect.play().catch(()=>{});
          createParticles(c.x, c.y, '#ffeb3b', 28, 8);

          return false;
        }
        return true;
      });

      obstacles = obstacles.filter(o => {
        o.y += o.vy;
        if (o.y > canvas.height + 120) return false;

        if (!game.powerups.shield &&
            player.x < o.x + o.w && player.x + player.w > o.x &&
            player.y < o.y + o.h && player.y + player.h > o.y) {
          game.lives--;
          sndHit.currentTime = 0; sndHit.play().catch(()=>{});
          createParticles(player.x + player.w/2, player.y + player.h/2, '#ff4444', 30, 10);
          if (game.lives <= 0) endGame();
          return false;
        }
        return true;
      });

      powerItems = powerItems.filter(p => {
        p.y += 3.5;
        if (p.y > canvas.height + 100) return false;

        if (Math.hypot(p.x - (player.x+player.w/2), p.y - (player.y+player.h/2)) < 55) {
          game.powerups[p.type] = 900;
          sndPowerup.currentTime = 0; sndPowerup.play().catch(()=>{});
          createParticles(p.x, p.y, p.type==='magnet'?'#00ffff':p.type==='shield'?'#00ff88':'#ffaa00', 35, 9);
          powerupText.textContent = p.type.toUpperCase() + " ACTIVÉ !";
          powerupText.classList.remove('hidden');
          setTimeout(() => powerupText.classList.add('hidden'), 2500);
          return false;
        }
        return true;
      });

      Object.keys(game.powerups).forEach(k => game.powerups[k] > 0 && game.powerups[k]--);

      particles = particles.filter(p => {
        p.x += p.vx;
        p.y += p.vy;
        p.vy += 0.2;
        p.life--;
        return p.life > 0;
      });

      scoreEl.textContent = game.score.toLocaleString();
      levelEl.textContent = `NIVEAU ${game.level}`;
      timerEl.textContent = Math.ceil(game.time / 60) + ' s';
      livesEl.textContent = '♥'.repeat(game.lives) + '♡'.repeat(3 - game.lives);

      if (game.score >= game.level * 8000) {
        game.level++;
        game.time += 1200;
        sndLevelUp.currentTime = 0; sndLevelUp.play().catch(()=>{});
        showMessage(`NIVEAU ${game.level} !`, '#00ff9d', 3000);
      }

      game.time--;
      if (game.time <= 0 || game.lives <= 0) endGame();
    }

    function render() {
      ctx.fillStyle = 'rgba(5,5,15,0.18)';
      ctx.fillRect(0,0,canvas.width,canvas.height);

      ctx.save();
      let playerColor = '#ffd700';
      let shadowColor = '#ffd700';

      if (currentSkin === 'cyan') {
        playerColor = '#00ffff';
        shadowColor = '#00ff9d';
      } else if (currentSkin === 'purple') {
        playerColor = '#a855f7';
        shadowColor = '#a855f7';
      } else if (currentSkin === 'fire') {
        playerColor = '#ff4444';
        shadowColor = '#ff6b6b';
      }

      ctx.shadowColor = game.powerups.shield > 0 ? '#00ff88' : shadowColor;
      ctx.shadowBlur = 50;
      ctx.fillStyle = game.powerups.shield > 0 ? '#00ff88' : playerColor;
      ctx.fillRect(player.x, player.y, player.w, player.h);
      ctx.restore();

      coins.forEach(c => {
        ctx.save();
        ctx.shadowColor = '#ffeb3b';
        ctx.shadowBlur = 35;
        ctx.fillStyle = '#ffeb3b';
        ctx.beginPath();
        ctx.arc(c.x, c.y, c.r, 0, Math.PI*2);
        ctx.fill();
        ctx.restore();
      });

      obstacles.forEach(o => {
        ctx.save();
        ctx.shadowColor = '#ff4444';
        ctx.shadowBlur = 30;
        ctx.fillStyle = '#ff3333';
        ctx.fillRect(o.x, o.y, o.w, o.h);
        ctx.restore();
      });

      powerItems.forEach(p => {
        let col = p.type==='magnet' ? '#00ffff' : p.type==='shield' ? '#00ff88' : '#ffaa00';
        ctx.save();
        ctx.shadowColor = col;
        ctx.shadowBlur = 40;
        ctx.fillStyle = col;
        ctx.beginPath();
        ctx.arc(p.x, p.y, p.r, 0, Math.PI*2);
        ctx.fill();
        ctx.restore();
      });

      particles.forEach(p => {
        ctx.save();
        ctx.globalAlpha = p.life / 70;
        ctx.shadowColor = p.color;
        ctx.shadowBlur = 18;
        ctx.fillStyle = p.color;
        ctx.beginPath();
        ctx.arc(p.x, p.y, p.life/12 + 3, 0, Math.PI*2);
        ctx.fill();
        ctx.restore();
      });
    }

    function showMessage(txt, color, duration = 2000) {
      messageEl.textContent = txt;
      messageEl.style.color = color;
      messageEl.style.opacity = '1';
      setTimeout(() => messageEl.style.opacity = '0', duration);
    }

    function endGame() {
      game.running = false;
      if (game.score > game.highScore) {
        game.highScore = game.score;
        localStorage.setItem('coinqsyRushHigh', game.highScore);
        localHighEl.textContent = game.highScore.toLocaleString();
      }

      $.ajax({
        url: 'api/save_teaser_score.php',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
          score: game.score,
          level: game.level,
          game_slug: 'coinqsy-rush',
          vrai_coins_earned: game.vrai_coins_total
        }),
        success: function(res) {
          console.log('Envoi final OK', res);
        },
        error: function() {
          console.log('Erreur envoi final');
        }
      });

      showMessage(`PARTIE TERMINÉE !\n${game.score.toLocaleString()} points\n→ ${game.vrai_coins_total} vrais Coinqsy`, '#ff6b6b', 7000);
      restartBtn.classList.remove('hidden');
    }

    function startGame() {
      game.running = true;
      game.score = 0;
      game.level = 1;
      game.time = 45 * 60;
      game.lives = 3;
      game.powerups = {magnet:0, shield:0, speed:0};
      game.vrai_coins_total = 0;
      coins = []; obstacles = []; powerItems = []; particles = [];
      player.x = canvas.width/2 - 35;
      player.y = canvas.height - 120;

      startBtn.style.display = 'none';
      restartBtn.classList.add('hidden');

      setInterval(() => spawn('coin'), 450 - game.level*30);
      setInterval(() => spawn('obstacle'), 1400 - game.level*90);
      setInterval(() => spawn('powerup'), 6000);

      showMessage('GO !', '#00ff9d', 1500);
    }

    startBtn.onclick = startGame;
    restartBtn.onclick = startGame;

    function gameLoop() {
      if (game.running) update();
      render();
      requestAnimationFrame(gameLoop);
    }

    gameLoop();
  </script>

</body>
</html>