<?php
include '../bd/bd.php';

$tournoi_id = (int)$_GET['id'];

$stmt = $pdo->prepare("
    SELECT u.username, tp.score, tp.rank, tp.coins_gagnes 
    FROM tournoi_participations tp
    JOIN users u ON tp.user_id = u.id
    WHERE tp.tournoi_id = ?
    ORDER BY tp.score DESC, tp.inscrit_le ASC
");
$stmt->execute([$tournoi_id]);
$participants = $stmt->fetchAll();

foreach ($participants as $index => $p):
?>
  <tr class="border-b border-gray-800 hover:bg-gray-800/50 <?= $index === 0 ? 'bg-yellow-900/20' : '' ?>">
    <td class="py-4 px-4 font-bold text-cyan-400">
      <?= $p['rank'] ? $p['rank'] : ($index + 1) ?>
    </td>
    <td class="py-4 px-4"><?= htmlspecialchars($p['username']) ?></td>
    <td class="py-4 px-4 text-right text-yellow-400 font-medium"><?= number_format($p['score']) ?></td>
    <td class="py-4 px-4 text-right text-green-400 font-medium">+<?= number_format($p['coins_gagnes']) ?></td>
  </tr>
<?php endforeach; ?>