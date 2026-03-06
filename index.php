<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr" class="dark">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Coinqsy Games – Joue, Gagne, Convertis !</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      overflow-x: hidden;
      background: #0a0a14;
    }

    .neon-glow {
      text-shadow: 0 0 10px #a855f7, 0 0 20px #00ff9d, 0 0 40px #a855f7;
      animation: glowPulse 3s infinite alternate;
    }

    @keyframes glowPulse {
      0% { text-shadow: 0 0 10px #a855f7; }
      100% { text-shadow: 0 0 30px #00ff9d, 0 0 60px #a855f7; }
    }

    .float {
      animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-15px); }
    }

    .animate-fade-in {
      opacity: 0;
      transform: translateY(40px);
      transition: all 1s ease-out;
    }

    .animate-fade-in.visible {
      opacity: 1;
      transform: translateY(0);
    }

    .pulse-glow {
      animation: pulseGlow 2.5s infinite;
    }

    @keyframes pulseGlow {
      0%, 100% { box-shadow: 0 0 15px rgba(0, 255, 157, 0.4); }
      50% { box-shadow: 0 0 35px rgba(0, 255, 157, 0.8), 0 0 60px rgba(168, 85, 247, 0.6); }
    }

    .coin-fall {
      position: absolute;
      font-size: 2.5rem;
      pointer-events: none;
      animation: fall linear forwards;
      opacity: 0.9;
    }

    @keyframes fall {
      to {
        transform: translateY(120vh) rotate(720deg);
        opacity: 0;
      }
    }

    #mini-game {
      border: 4px solid #a855f7;
      box-shadow: 0 0 40px rgba(168, 85, 247, 0.6);
    }

    /* Top 1 stylisé */
    .top1-card {
      animation: glowPulse 4s infinite alternate;
      border: 4px solid #ffd700;
      box-shadow: 0 0 40px #ffd70080, inset 0 0 30px #ffd70040;
    }

    .top1-badge {
      animation: wiggle 3s infinite;
    }

    @keyframes wiggle {
      0%, 100% { transform: rotate(-4deg); }
      50% { transform: rotate(4deg); }
    }

    .avatar-glow {
      box-shadow: 0 0 40px #ffd700, 0 0 80px #ffaa00;
    }
  </style>
</head>

<body class="text-gray-100 min-h-screen relative">

  <!-- HEADER -->
  <header class="fixed top-0 left-0 right-0 z-50 bg-black/85 backdrop-blur-xl border-b border-purple-800/70 shadow-2xl">
    <div class="max-w-7xl mx-auto px-6 flex justify-between items-center h-16">
      <a href="index.php" class="flex items-center space-x-3">
        <div class="w-12 h-12 bg-gradient-to-br from-purple-600 to-cyan-400 rounded-2xl flex items-center justify-center text-3xl font-black pulse-glow float">C</div>
        <span class="text-3xl font-extrabold tracking-tight neon-glow">Coinqsy<span class="text-yellow-400">Games</span></span>
      </a>

      <nav class="hidden md:flex space-x-10 text-lg">
        <a href="index.php" class="hover:text-cyan-300 transition duration-300 hover:scale-110">Accueil</a>
        <a href="jeux/index.php" class="hover:text-cyan-300 transition duration-300 hover:scale-110">Jeux</a>
        <a href="tournois/index.php" class="hover:text-cyan-300 transition duration-300 hover:scale-110">Tournois</a>
      </nav>

      <div class="flex items-center space-x-6">
        <?php if (isset($_SESSION['user_id'])): ?>
          <span class="text-cyan-300 font-medium">Bienvenue, <?= htmlspecialchars($_SESSION['username']) ?></span>
          <a href="auth/logout.php" class="text-red-400 hover:text-red-300 transition">Déconnexion</a>
        <?php else: ?>
          <a href="auth/login.php" class="text-gray-300 hover:text-cyan-400 transition">Connexion</a>
          <a href="auth/register.php" class="bg-gradient-to-r from-purple-600 to-cyan-500 hover:from-purple-500 hover:to-cyan-400 px-6 py-2.5 rounded-xl font-semibold shadow-lg transform hover:scale-105 transition duration-300">
            Inscription
          </a>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <!-- HERO -->
  <section class="pt-28 pb-20 md:pt-40 relative overflow-hidden min-h-[90vh] flex items-center bg-gradient-to-b from-purple-950 via-indigo-950 to-black">
    <div class="absolute inset-0 opacity-30 pointer-events-none" id="particles"></div>

    <div class="max-w-6xl mx-auto px-6 text-center relative z-10">
      <h1 class="text-6xl md:text-8xl font-black mb-8 neon-glow float">
        JOUE.<span class="text-cyan-400">.</span> GAGNE.<span class="text-cyan-400">.</span><br />
        <span class="text-yellow-400 block">CONVERTIS !</span>
      </h1>

      <p class="text-2xl md:text-3xl text-gray-200 mb-12 max-w-4xl mx-auto animate-fade-in" style="transition-delay: 0.4s;">
        Attrape des Coinqsy, domine les tournois, parie malin... et transforme tes points en FCFA réels (si éligible) !
      </p>

      <div class="flex flex-col sm:flex-row justify-center gap-8 mb-16">
        <a href="jeux/index.php" class="bg-gradient-to-r from-cyan-500 to-green-500 text-white font-bold text-2xl px-14 py-7 rounded-3xl shadow-2xl transform hover:scale-115 hover:rotate-2 transition duration-400 pulse-glow">
          🎮 LANCER LE FUN
        </a>
        <a href="tournois/index.php" class="bg-gradient-to-r from-purple-700 to-pink-600 text-white font-bold text-2xl px-14 py-7 rounded-3xl border-4 border-purple-400/50 transform hover:scale-115 transition duration-400">
          🏆 TOURNOIS CASH
        </a>
      </div>
    </div>
  </section>

  <!-- MINI-JEU TEASER -->
  <section class="py-20 bg-black/60">
    <div class="max-w-6xl mx-auto px-6">
      <h3 class="text-4xl font-bold mb-8 text-cyan-300 text-center">Mini-Teaser : Attrape les Coinqsy !</h3>
      <p class="text-gray-300 mb-6 text-center text-xl">Flèches ou WASD → 30 secondes par niveau</p>

      <div class="max-w-2xl mx-auto">
        <div class="text-center mb-8">
          <button id="start-btn" class="bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-400 hover:to-yellow-500 text-black font-bold text-xl px-12 py-5 rounded-2xl shadow-2xl transform hover:scale-110 transition duration-300 pulse-glow">
            COMMENCER LE MINI-JEU
          </button>
        </div>

        <div class="relative">
          <canvas id="mini-game" width="600" height="400" class="mx-auto rounded-2xl bg-gradient-to-b from-black to-purple-950 border-4 border-purple-600 shadow-2xl"></canvas>
          <div id="game-ui" class="absolute inset-0 flex flex-col items-center justify-center text-4xl font-black hidden">
            <div id="game-level" class="text-cyan-400 mb-4 drop-shadow-lg">Niveau 1</div>
            <div id="game-score" class="text-yellow-400 neon-glow text-6xl">0</div>
            <div id="game-timer" class="text-cyan-300 mt-4 text-4xl">30</div>
          </div>
          <button id="restart-btn" class="absolute bottom-8 left-1/2 -translate-x-1/2 bg-purple-700 hover:bg-purple-600 px-10 py-4 rounded-xl text-xl font-bold hidden transform hover:scale-105 transition">
            Rejouer le teaser
          </button>
        </div>

        <div class="text-center mt-8 text-gray-400 text-lg">
          Meilleur score local : <span id="high-score-display" class="text-yellow-400 font-bold">0</span> Coinqsy
        </div>

        <div class="text-center mt-6">
          <a href="classjeux/teaser-classement.php" class="text-xl text-purple-300 hover:text-purple-100 underline transition">
            → Voir le classement mondial du teaser
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- POURQUOI JOUER -->
  <section class="py-20 bg-black/60">
    <div class="max-w-7xl mx-auto px-6">
      <h2 class="text-5xl font-black text-center neon-title mb-12">
        Pourquoi jouer sur Coinqsy Games ?
      </h2>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-gray-900/70 rounded-3xl p-10 text-center border border-cyan-700/50">
          <div class="text-6xl mb-6">🎮</div>
          <h3 class="text-3xl font-bold text-cyan-400 mb-4">Jeux gratuits</h3>
          <p class="text-gray-300">Joue sans payer, gagne des Coinqsy réels !</p>
        </div>
        <div class="bg-gray-900/70 rounded-3xl p-10 text-center border border-purple-700/50">
          <div class="text-6xl mb-6">🏆</div>
          <h3 class="text-3xl font-bold text-purple-400 mb-4">Tournois & récompenses</h3>
          <p class="text-gray-300">Participe et gagne des pools de Coinqsy !</p>
        </div>
        <div class="bg-gray-900/70 rounded-3xl p-10 text-center border border-yellow-700/50">
          <div class="text-6xl mb-6">💰</div>
          <h3 class="text-3xl font-bold text-yellow-400 mb-4">Vrais gains</h3>
          <p class="text-gray-300">Tes scores se convertissent en Coinqsy utilisables.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- CLASSEMENT MONDIAL -->
  <section class="py-16 bg-gradient-to-b from-black/60 to-purple-950/40">
    <div class="max-w-7xl mx-auto px-6">
      <h2 class="text-5xl font-black text-center neon-title mb-12">
        CLASSEMENT MONDIAL
      </h2>

      <?php
      include 'bd/bd.php';
      $stmt_top1 = $pdo->query("
        SELECT u.username, SUM(gs.score) as total_score
        FROM game_scores gs
        JOIN users u ON gs.user_id = u.id
        GROUP BY gs.user_id
        ORDER BY total_score DESC
        LIMIT 1
      ");
      $top1 = $stmt_top1->fetch();

      if ($top1):
      ?>
      <div class="relative bg-gradient-to-br from-yellow-950/70 via-amber-950/50 to-yellow-900/40 rounded-3xl p-12 mb-16 text-center border-4 border-yellow-500/60 shadow-[0_0_60px_rgba(255,215,0,0.5)] overflow-hidden top1-card">
        <div class="absolute -top-16 left-1/2 -translate-x-1/2 text-9xl drop-shadow-2xl top1-badge">👑</div>

        <div class="relative w-48 h-48 mx-auto mb-8">
          <div class="absolute inset-0 rounded-full bg-gradient-to-br from-yellow-400 via-amber-500 to-yellow-600 blur-2xl opacity-70 animate-pulse-fast"></div>
          <div class="absolute inset-4 rounded-full border-4 border-yellow-300 animate-spin-slow"></div>
          <img 
            src="assets/img/picture.webp" 
            alt="<?= htmlspecialchars($top1['username']) ?>" 
            class="relative w-full h-full rounded-full object-cover border-8 border-yellow-400 shadow-2xl avatar-glow"
          >
        </div>

        <div class="text-7xl md:text-8xl font-black text-white mb-4 glow-yellow tracking-widest">
          <?= htmlspecialchars($top1['username']) ?>
        </div>

        <div class="text-6xl md:text-7xl font-extrabold text-yellow-300 mb-10 drop-shadow-2xl">
          <?= number_format($top1['total_score']) ?>
        </div>

        <div class="inline-block bg-gradient-to-r from-yellow-500 via-amber-600 to-yellow-500 text-black font-black text-4xl px-16 py-6 rounded-3xl shadow-2xl transform hover:scale-105 transition animate-pulse">
          🏆 ROI ACTUEL
        </div>
      </div>
      <?php endif; ?>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        <?php
        $jeux = ['coinqsy-rush', 'snake'];

        foreach ($jeux as $jeu_slug):
          $stmt_top5 = $pdo->prepare("
            SELECT u.username, gs.score
            FROM game_scores gs
            JOIN users u ON gs.user_id = u.id
            WHERE gs.game_slug = ?
            ORDER BY gs.score DESC
            LIMIT 5
          ");
          $stmt_top5->execute([$jeu_slug]);
          $top5 = $stmt_top5->fetchAll();

          $jeu_nom = str_replace('-', ' ', ucfirst($jeu_slug));
          $jeu_nom = str_replace('coinqsy rush', 'Coinqsy Rush', $jeu_nom);
        ?>
          <div class="bg-gray-900/70 rounded-3xl p-8 border border-purple-700/50 shadow-2xl">
            <h3 class="text-3xl font-bold text-cyan-400 mb-8 text-center">
              Top 5 – <?= htmlspecialchars($jeu_nom) ?>
            </h3>

            <?php if (empty($top5)): ?>
              <p class="text-center text-gray-400 text-xl">Aucun score enregistré pour ce jeu</p>
            <?php else: ?>
              <div class="space-y-4">
                <?php foreach ($top5 as $pos => $player): ?>
                  <div class="flex items-center justify-between bg-gray-800/50 p-4 rounded-xl hover:bg-gray-700/50 transition">
                    <div class="flex items-center space-x-4">
                      <span class="text-2xl font-black <?= $pos === 0 ? 'text-yellow-400' : 'text-purple-300' ?>">
                        #<?= $pos + 1 ?>
                      </span>
                      <span class="text-xl font-medium">
                        <?= htmlspecialchars($player['username']) ?>
                      </span>
                    </div>
                    <span class="text-2xl font-bold text-yellow-400">
                      <?= number_format($player['score']) ?>
                    </span>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>

    </div>
  </section>

  <!-- TOURNOIS ACTIFS -->
  <section class="py-20 bg-gradient-to-b from-purple-950/40 to-black">
    <div class="max-w-7xl mx-auto px-6">
      <h2 class="text-5xl font-black text-center neon-title mb-12">
        Tournois Actifs
      </h2>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php
        $stmt = $pdo->query("
          SELECT * FROM tournois 
          WHERE statut IN ('ouvert', 'en_cours') 
          ORDER BY date_fin ASC
          LIMIT 3
        ");
        while ($tournoi = $stmt->fetch()):
        ?>
          <div class="bg-gray-900/70 rounded-3xl p-8 border border-purple-700/50 shadow-2xl hover:shadow-purple-600/50 transition-all">
            <h3 class="text-2xl font-bold text-cyan-400 mb-4"><?= htmlspecialchars($tournoi['nom']) ?></h3>
            <p class="text-gray-300 mb-6"><?= htmlspecialchars(substr($tournoi['description'], 0, 120)) ?>...</p>
            <div class="space-y-2 text-sm text-gray-400 mb-6">
              <p>Jeu : <span class="text-yellow-400"><?= htmlspecialchars($tournoi['jeu_slug']) ?></span></p>
              <p>Fin dans : <span class="text-cyan-300"><?= date('d/m/Y H:i', strtotime($tournoi['date_fin'])) ?></span></p>
              <p>Places : <span class="text-cyan-300"><?= $tournoi['places_restantes'] ?> / <?= $tournoi['places_max'] ?></span></p>
            </div>
            <a href="tournois/tournoi_detail.php?id=<?= $tournoi['id'] ?>" class="block bg-gradient-to-r from-cyan-600 to-cyan-800 text-white font-bold py-4 rounded-xl text-center hover:brightness-110 transition">
              Voir le tournoi
            </a>
          </div>
        <?php endwhile; ?>
      </div>

      <div class="text-center mt-12">
        <a href="tournois/index.php" class="text-2xl text-purple-300 hover:text-purple-100 underline">
          Voir tous les tournois →
        </a>
      </div>
    </div>
  </section>

  <!-- STATS -->
  <section class="grid grid-cols-2 md:grid-cols-4 gap-10 text-center mt-20 pb-20">
    <div class="animate-fade-in" style="transition-delay: 1s;">
      <div class="text-6xl font-black text-cyan-400 count-up" data-target="15000">0</div>
      <p>Joueurs</p>
    </div>
    <div class="animate-fade-in" style="transition-delay: 1.2s;">
      <div class="text-6xl font-black text-yellow-400 count-up" data-target="9500">0</div>
      <p>Tournois</p>
    </div>
    <div class="animate-fade-in" style="transition-delay: 1.4s;">
      <div class="text-6xl font-black text-purple-400 count-up" data-target="62000000">0</div>
      <p>Coinqsy</p>
    </div>
    <div class="animate-fade-in" style="transition-delay: 1.6s;">
      <div class="text-6xl font-black text-green-400">24/7</div>
      <p>Action</p>
    </div>
  </section>

  <!-- Scripts -->
  <script>
    // Fade-in on scroll
    const fadeEls = document.querySelectorAll('.animate-fade-in');
    const obs = new IntersectionObserver(e => e.forEach(en => en.isIntersecting && en.target.classList.add('visible')), {
      threshold: 0.15
    });
    fadeEls.forEach(el => obs.observe(el));

    // Compteurs stats
    document.querySelectorAll('.count-up').forEach(c => {
      let val = 0,
        target = +c.dataset.target,
        inc = target / 120;
      let timer = setInterval(() => {
        val += inc;
        c.textContent = Math.ceil(val).toLocaleString();
        if (val >= target) {
          clearInterval(timer);
          c.textContent = target.toLocaleString();
        }
      }, 20);
    });

    // Mini-jeu
    const canvas = document.getElementById('mini-game');
    const ctx = canvas.getContext('2d');
    const startBtn = document.getElementById('start-btn');
    const restartBtn = document.getElementById('restart-btn');
    const ui = document.getElementById('game-ui');
    const levelEl = document.getElementById('game-level');
    const scoreEl = document.getElementById('game-score');
    const timerEl = document.getElementById('game-timer');
    const highScoreEl = document.getElementById('high-score-display');

    let player = {
      x: canvas.width / 2 - 25,
      y: canvas.height - 60,
      width: 50,
      height: 50,
      speed: 8
    };
    let coins = [];
    let score = 0;
    let highScore = localStorage.getItem('coinqsyHighScore') || 0;
    highScoreEl.textContent = highScore;
    let timeLeft = 30;
    let level = 1;
    let gameRunning = false;
    let keys = {};
    let coinInterval, timerInterval;

    const coinSound = new Audio('https://assets.mixkit.co/sfx/preview/mixkit-arcade-game-jump-coin-2164.mp3');

    function loadHighScore() {
      highScore = parseInt(localStorage.getItem('coinqsyHighScore')) || 0;
      highScoreEl.textContent = highScore;
    }

    function saveHighScore() {
      if (score > highScore) {
        highScore = score;
        localStorage.setItem('coinqsyHighScore', highScore);
        highScoreEl.textContent = highScore;
      }
    }

    function spawnCoin() {
      const freq = level === 1 ? 800 : level === 2 ? 500 : 300;
      const speed = level === 1 ? 2.5 : level === 2 ? 4 : 6;
      coins.push({
        x: Math.random() * (canvas.width - 60) + 30,
        y: -60,
        size: 40 + Math.random() * 20,
        speed: speed + Math.random() * 2
      });
    }

    function nextLevel() {
      level++;
      levelEl.textContent = `Niveau ${level}`;
      if (level > 3) {
        endGame("Victoire ! Niveau maximum atteint !");
        return;
      }
      timeLeft = 30;
      timerEl.textContent = timeLeft;
    }

    function endGame(message = "") {
      gameRunning = false;
      clearInterval(coinInterval);
      clearInterval(timerInterval);
      saveHighScore();

      $.ajax({
        url: 'api/save_teaser_score.php',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
          score: score,
          level: level
        }),
        success: function(response) {
          try {
            let res = JSON.parse(response);
            console.log(res.success ? 'Score enregistré sur le serveur' : 'Erreur: ' + res.message);
          } catch (e) {
            console.log('Erreur parsing réponse serveur');
          }
        },
        error: function() {
          console.log('Erreur AJAX lors de l\'envoi du score');
        }
      });

      alert(`${message || "Temps écoulé !"} Score: ${score} Coinqsy (Niv. ${level})\nHigh Score local: ${highScore}\n\nVa voir le classement mondial !`);
      restartBtn.classList.remove('hidden');
    }

    function draw() {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      ctx.fillStyle = '#ffd700';
      ctx.shadowColor = '#ffd700';
      ctx.shadowBlur = 40;
      ctx.beginPath();
      ctx.arc(player.x + 25, player.y + 25, 25, 0, Math.PI * 2);
      ctx.fill();
      ctx.shadowBlur = 0;

      coins.forEach((coin, i) => {
        ctx.fillStyle = '#ffeb3b';
        ctx.shadowColor = '#ffeb3b';
        ctx.shadowBlur = 30;
        ctx.beginPath();
        ctx.arc(coin.x, coin.y, coin.size / 2, 0, Math.PI * 2);
        ctx.fill();
        ctx.shadowBlur = 0;

        coin.y += coin.speed;
        if (coin.y > canvas.height + 60) coins.splice(i, 1);

        if (
          player.x < coin.x + coin.size &&
          player.x + player.width > coin.x &&
          player.y < coin.y + coin.size &&
          player.y + player.height > coin.y
        ) {
          score += 10 * level;
          scoreEl.textContent = score;
          coins.splice(i, 1);
          coinSound.currentTime = 0;
          coinSound.play().catch(() => {});

          for (let k = 0; k < 10; k++) {
            let p = document.createElement('div');
            p.className = 'coin-fall text-yellow-400 text-3xl absolute pointer-events-none';
            p.textContent = '₿';
            p.style.left = `${coin.x + coin.size/2}px`;
            p.style.top = `${coin.y + coin.size/2}px`;
            p.style.animationDuration = (0.8 + Math.random() * 1) + 's';
            document.body.appendChild(p);
            setTimeout(() => p.remove(), 2500);
          }
        }
      });
    }

    function update() {
      if (!gameRunning) return;
      if (keys['ArrowLeft'] || keys['a'] || keys['A']) player.x = Math.max(0, player.x - player.speed);
      if (keys['ArrowRight'] || keys['d'] || keys['D']) player.x = Math.min(canvas.width - player.width, player.x + player.speed);
      if (keys['ArrowUp'] || keys['w'] || keys['W']) player.y = Math.max(0, player.y - player.speed);
      if (keys['ArrowDown'] || keys['s'] || keys['S']) player.y = Math.min(canvas.height - player.height, player.y + player.speed);
    }

    function gameLoop() {
      update();
      draw();
      requestAnimationFrame(gameLoop);
    }

    function startGame() {
      loadHighScore();
      gameRunning = true;
      score = 0;
      level = 1;
      timeLeft = 30;
      coins = [];
      player.x = canvas.width / 2 - 25;
      player.y = canvas.height - 60;
      scoreEl.textContent = '0';
      timerEl.textContent = '30';
      levelEl.textContent = 'Niveau 1';
      restartBtn.classList.add('hidden');
      ui.classList.remove('hidden');
      startBtn.classList.add('hidden');

      coinInterval = setInterval(spawnCoin, 800);
      timerInterval = setInterval(() => {
        timeLeft--;
        timerEl.textContent = timeLeft;
        if (timeLeft <= 0) {
          if (level < 3) {
            nextLevel();
          } else {
            endGame("Bravo ! Tu as terminé les niveaux !");
          }
        }
      }, 1000);

      setInterval(() => {
        if (gameRunning && level < 3 && score > 150 * level) nextLevel();
      }, 5000);
    }

    window.addEventListener('keydown', e => keys[e.key] = true);
    window.addEventListener('keyup', e => keys[e.key] = false);

    startBtn.addEventListener('click', startGame);
    restartBtn.addEventListener('click', () => {
      restartBtn.classList.add('hidden');
      startBtn.classList.remove('hidden');
      ui.classList.add('hidden');
    });

    gameLoop();
  </script>

</body>

</html>