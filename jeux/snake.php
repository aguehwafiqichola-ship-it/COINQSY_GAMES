<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Coinqsy Snake | Coinqsy Games</title>
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
      text-shadow: 0 0 40px rgba(255,215,0,0.8);
    }
    .glow-yellow { filter: drop-shadow(0 0 25px #ffd700); }
    .glow-cyan { filter: drop-shadow(0 0 25px #00ff9d); }
    .skin-card {
      transition: all 0.3s ease;
    }
    .skin-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(168,85,247,0.4);
    }
    .skin-active {
      border: 3px solid #ffd700;
      box-shadow: 0 0 20px #ffd70080;
    }
  </style>
</head>
<body class="min-h-screen flex flex-col">

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

  <?php
  if (isset($_GET['tournoi_id']) && is_numeric($_GET['tournoi_id'])) {
    include '../bd/bd.php';
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

  <main class="flex-1 pt-12 pb-12 px-4 md:px-12 lg:px-20">

    <h1 class="text-5xl md:text-7xl font-black text-center neon-title mb-6">
      COINQSY SNAKE
    </h1>

    <p class="text-xl text-gray-300 text-center mb-8 max-w-3xl mx-auto">
      Mange les pièces Coinqsy et deviens le serpent le plus long !<br>
      <span class="text-yellow-400">Score = pièces mangées | Vrais Coinqsy = score / 50</span>
    </p>

    <div class="max-w-[800px] mx-auto bg-black/60 rounded-3xl p-6 border-4 border-purple-700/70 shadow-2xl">
      <canvas id="snake-canvas" width="800" height="600" class="mx-auto block rounded-2xl bg-gradient-to-b from-indigo-950 to-purple-950"></canvas>

      <div class="text-center mt-6 text-2xl space-x-6">
        Score : <span id="score" class="text-yellow-400 font-bold">0</span> |
        Meilleur : <span id="high-score" class="text-cyan-400 font-bold">0</span>
      </div>

      <div class="flex justify-center gap-6 mt-6">
        <button id="start-btn" class="bg-gradient-to-r from-yellow-500 to-amber-600 text-black font-bold text-2xl px-12 py-5 rounded-2xl shadow-2xl hover:brightness-110 transition">
          LANCER LE SNAKE
        </button>
        <button id="shop-btn" class="bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold text-2xl px-12 py-5 rounded-2xl shadow-2xl hover:brightness-110 transition">
          Boutique de skins
        </button>
      </div>

      <div class="mt-10 text-center">
        <a href="../classjeux/classement-jeu.php?game=snake" class="text-purple-300 hover:text-purple-100 underline text-xl">
          🏆 Voir le classement mondial de Coinqsy Snake →
        </a>
      </div>
    </div>

    <div class="mt-12 text-center">
      <a href="../tournois/index.php" class="text-purple-300 hover:text-purple-100 underline text-xl">
        ← Retour aux tournois
      </a>
    </div>

  </main>

  <footer class="bg-black/90 border-t border-purple-900/60 py-10 text-center text-gray-500">
    © <?= date('Y') ?> Coinqsy Games
  </footer>

  <!-- MODALE BOUTIQUE SKINS -->
  <div id="shop-modal" class="fixed inset-0 bg-black/80 flex items-center justify-center z-50 hidden">
    <div class="bg-gray-900/95 border-2 border-purple-700/70 rounded-3xl p-8 max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto relative">
      <button id="close-shop" class="absolute top-4 right-6 text-5xl text-gray-400 hover:text-white transition">×</button>

      <h2 class="text-4xl font-black neon-title text-center mb-8">BOUTIQUE DE SKINS – SNAKE</h2>

      <p class="text-center text-lg text-gray-300 mb-10">
        Personnalise ton serpent ! Chaque skin débloque une capacité unique.
      </p>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="skin-card bg-gray-800/50 rounded-2xl p-6 text-center border-2 border-yellow-600/50 skin-active" data-skin="classic">
          <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gradient-to-br from-yellow-400 to-amber-600 glow-yellow"></div>
          <h3 class="text-xl font-bold text-yellow-400 mb-2">Classic</h3>
          <p class="text-sm text-gray-400 mb-4">Le serpent classique, sans bonus particulier.</p>
          <p class="text-lg font-bold text-yellow-400">Gratuit</p>
          <button class="mt-4 bg-yellow-600 hover:bg-yellow-500 text-black font-bold py-2 px-6 rounded-xl transition w-full skin-active" disabled>
            Équipé
          </button>
        </div>

        <div class="skin-card bg-gray-800/50 rounded-2xl p-6 text-center border-2 border-cyan-600/50" data-skin="ghost">
          <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gradient-to-br from-cyan-400 to-teal-600 glow-cyan"></div>
          <h3 class="text-xl font-bold text-cyan-400 mb-2">Ghost</h3>
          <p class="text-sm text-gray-400 mb-4">Permet de passer à travers les murs.</p>
          <p class="text-lg font-bold text-yellow-400">300 Coinqsy</p>
          <button class="mt-4 bg-cyan-600 hover:bg-cyan-500 text-white font-bold py-2 px-6 rounded-xl transition w-full buy-btn" data-skin="ghost" data-price="300">
            Acheter
          </button>
        </div>

        <div class="skin-card bg-gray-800/50 rounded-2xl p-6 text-center border-2 border-purple-600/50" data-skin="radar">
          <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 glow-purple"></div>
          <h3 class="text-xl font-bold text-purple-400 mb-2">Radar</h3>
          <p class="text-sm text-gray-400 mb-4">Affiche le chemin vers la prochaine pièce.</p>
          <p class="text-lg font-bold text-yellow-400">500 Coinqsy</p>
          <button class="mt-4 bg-purple-600 hover:bg-purple-500 text-white font-bold py-2 px-6 rounded-xl transition w-full buy-btn" data-skin="radar" data-price="500">
            Acheter
          </button>
        </div>

        <div class="skin-card bg-gray-800/50 rounded-2xl p-6 text-center border-2 border-green-600/50" data-skin="speed">
          <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gradient-to-br from-green-400 to-lime-600"></div>
          <h3 class="text-xl font-bold text-green-400 mb-2">Speed</h3>
          <p class="text-sm text-gray-400 mb-4">Le serpent va 30% plus vite.</p>
          <p class="text-lg font-bold text-yellow-400">700 Coinqsy</p>
          <button class="mt-4 bg-green-600 hover:bg-green-500 text-white font-bold py-2 px-6 rounded-xl transition w-full buy-btn" data-skin="speed" data-price="700">
            Acheter
          </button>
        </div>
      </div>
    </div>
  </div>

  <script>
    const canvas = document.getElementById('snake-canvas');
    const ctx = canvas.getContext('2d');
    const startBtn = document.getElementById('start-btn');
    const shopBtn = document.getElementById('shop-btn');
    const shopModal = document.getElementById('shop-modal');
    const closeShop = document.getElementById('close-shop');
    const scoreEl = document.getElementById('score');
    const highScoreEl = document.getElementById('high-score');

    let snake = [{ x: 400, y: 300 }];
    let food = { x: 0, y: 0 };
    let dx = 20;
    let dy = 0;
    let score = 0;
    let highScore = localStorage.getItem('snakeHighScore') || 0;
    let gameRunning = false;
    let gameInterval;

    let currentSkin = localStorage.getItem('snakeSkin') || 'classic';
    let passThroughWalls = false;
    let showPath = false;
    let speedMultiplier = 1;

    highScoreEl.textContent = highScore;

    function applySkin(skin) {
      currentSkin = skin;
      localStorage.setItem('snakeSkin', skin);

      passThroughWalls = (skin === 'ghost');
      showPath = (skin === 'radar');
      speedMultiplier = (skin === 'speed') ? 1.3 : 1;

      document.querySelectorAll('.skin-card').forEach(card => {
        const isActive = card.dataset.skin === skin;
        card.classList.toggle('skin-active', isActive);
        const btn = card.querySelector('button');
        if (btn) {
          btn.textContent = isActive ? 'Équipé' : (card.querySelector('p.text-lg.font-bold').textContent.includes('Gratuit') ? 'Équipé' : 'Acheter');
        }
      });

      if (gameRunning) {
        clearInterval(gameInterval);
        gameInterval = setInterval(update, 100 / speedMultiplier);
      }
    }

    applySkin(currentSkin);

    shopBtn.onclick = () => shopModal.classList.remove('hidden');
    closeShop.onclick = () => shopModal.classList.add('hidden');

    document.querySelectorAll('.buy-btn').forEach(btn => {
      btn.onclick = () => {
        const card = btn.closest('.skin-card');
        const skin = card.dataset.skin;
        const price = parseInt(btn.dataset.price);

        if (confirm(`Acheter le skin "${skin}" pour ${price} Coinqsy ?`)) {
          $.ajax({
            url: 'api/buy_skin_multi.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
              game_slug: 'snake',
              skin: skin,
              price: price
            }),
            success: function(res) {
              if (res.success) {
                alert(res.message);
                applySkin(skin);
                btn.textContent = 'Équipé';
                card.classList.add('skin-active');
              } else {
                alert(res.message);
              }
            },
            error: function() {
              alert('Erreur lors de la connexion au serveur');
            }
          });
        }
      };
    });

    function placeFood() {
      food.x = Math.floor(Math.random() * (canvas.width / 20)) * 20;
      food.y = Math.floor(Math.random() * (canvas.height / 20)) * 20;
    }

    function draw() {
      ctx.clearRect(0, 0, canvas.width, canvas.height);

      ctx.strokeStyle = 'rgba(168,85,247,0.08)';
      for (let i = 0; i < canvas.width; i += 20) {
        ctx.beginPath(); ctx.moveTo(i, 0); ctx.lineTo(i, canvas.height); ctx.stroke();
      }
      for (let i = 0; i < canvas.height; i += 20) {
        ctx.beginPath(); ctx.moveTo(0, i); ctx.lineTo(canvas.width, i); ctx.stroke();
      }

      snake.forEach((segment, i) => {
        ctx.fillStyle = i === 0 ? '#ffd700' : '#ffaa00';
        ctx.shadowColor = '#ffd700';
        ctx.shadowBlur = 15;
        ctx.fillRect(segment.x, segment.y, 20, 20);
      });

      ctx.shadowColor = '#ffeb3b';
      ctx.shadowBlur = 20;
      ctx.fillStyle = '#ffeb3b';
      ctx.beginPath();
      ctx.arc(food.x + 10, food.y + 10, 10, 0, Math.PI * 2);
      ctx.fill();

      if (showPath && snake.length > 0) {
        const head = snake[0];
        ctx.beginPath();
        ctx.strokeStyle = 'rgba(0, 255, 255, 0.5)';
        ctx.lineWidth = 3;
        ctx.setLineDash([5, 5]);
        ctx.moveTo(head.x + 10, head.y + 10);
        ctx.lineTo(food.x + 10, food.y + 10);
        ctx.stroke();
        ctx.setLineDash([]);
      }
    }

    function update() {
      if (!gameRunning) return;

      let head = { x: snake[0].x + dx, y: snake[0].y + dy };

      if (passThroughWalls) {
        if (head.x < 0) head.x = canvas.width - 20;
        if (head.x >= canvas.width) head.x = 0;
        if (head.y < 0) head.y = canvas.height - 20;
        if (head.y >= canvas.height) head.y = 0;
      } else {
        if (head.x < 0 || head.x >= canvas.width || head.y < 0 || head.y >= canvas.height) {
          endGame();
          return;
        }
      }

      for (let segment of snake) {
        if (head.x === segment.x && head.y === segment.y) {
          endGame();
          return;
        }
      }

      snake.unshift(head);

      if (head.x === food.x && head.y === food.y) {
        score += 10;
        scoreEl.textContent = score;
        placeFood();
      } else {
        snake.pop();
      }

      draw();
    }

    function endGame() {
      gameRunning = false;
      clearInterval(gameInterval);
      if (score > highScore) {
        highScore = score;
        localStorage.setItem('snakeHighScore', highScore);
        highScoreEl.textContent = highScore;
      }

      $.ajax({
        url: 'api/save_snake_score.php',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
          score: score,
          level: 1,
          game_slug: 'snake',
          vrai_coins_earned: Math.floor(score / 50)
        }),
        success: function(res) {
          console.log('Score envoyé', res);
        },
        error: function() {
          console.log('Erreur envoi score');
        }
      });

      alert(`Game Over !\nScore : ${score}\nVrais Coinqsy gagnés : ${Math.floor(score / 50)}`);
      startBtn.style.display = 'block';
    }

    document.addEventListener('keydown', e => {
      if (!gameRunning) return;
      switch (e.key) {
        case 'ArrowUp':    if (dy === 0)  { dx = 0; dy = -20; } break;
        case 'ArrowDown':  if (dy === 0)  { dx = 0; dy = 20;  } break;
        case 'ArrowLeft':  if (dx === 0)  { dx = -20; dy = 0; } break;
        case 'ArrowRight': if (dx === 0)  { dx = 20; dy = 0;  } break;
      }
    });

    startBtn.onclick = () => {
      snake = [{ x: 400, y: 300 }];
      dx = 20;
      dy = 0;
      score = 0;
      scoreEl.textContent = score;
      placeFood();
      gameRunning = true;
      startBtn.style.display = 'none';
      gameInterval = setInterval(update, 100 / speedMultiplier);
    };
  </script>

</body>
</html>