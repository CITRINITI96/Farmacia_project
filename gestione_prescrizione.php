<?php
require_once 'db.php';
require_once 'auth.php';
requireAuth(['Admin', 'Dottore', 'Dottoressa']);

csrfToken();

$id_paziente = (int)($_GET['id_paziente'] ?? 0);
if ($id_paziente <= 0) {
    header('Location: paziente.php');
    exit;
}

$flash = '';
if (isset($_GET['msg'])) {
    $msgs = [
        'aggiunto'  => 'Prescrizione aggiunta con successo.',
        'modificato'=> 'Prescrizione modificata.',
        'eliminato' => 'Prescrizione eliminata.',
    ];
    $flash = $msgs[$_GET['msg']] ?? '';
}

$stmt = $pdo->prepare("SELECT p.ID_Paziente, p.ID_Farmaco, f.Nome AS Nome_Farmaco, p.Quantità, p.Data_Prescrizione
                       FROM Prescrizione p
                       JOIN Farmaco f ON p.ID_Farmaco = f.ID_Farmaco
                       WHERE p.ID_Paziente = ?
                       ORDER BY p.Data_Prescrizione DESC");
$stmt->execute([$id_paziente]);
$prescrizioni = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescrizioni Paziente — PharmaCare</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="layout">
    <?php if ($_SESSION['role'] === 'Admin') include 'sidebar_admin.php'; ?>

    <div class="main-content" style="<?= $_SESSION['role'] !== 'Admin' ? 'margin-left:0' : '' ?>">
        <div class="page-header flex justify-between items-center">
            <h1>📋 Prescrizioni — Paziente #<?= $id_paziente ?></h1>
            <a href="aggiungi_prescrizione.php?id_paziente=<?= $id_paziente ?>" class="btn btn-primary">➕ Nuova Prescrizione</a>
        </div>

        <?php if ($flash): ?>
            <div class="alert alert-success">✅ <?= htmlspecialchars($flash) ?></div>
        <?php endif; ?>

        <div style="background:var(--white);border-radius:var(--radius-md);padding:24px;box-shadow:var(--shadow-sm);">
            <h2>📋 Elenco Prescrizioni (<?= count($prescrizioni) ?>)</h2>
            <div class="table-wrapper table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>ID Paziente</th>
                            <th>Farmaco</th>
                            <th>Quantità</th>
                            <th>Data Prescrizione</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($prescrizioni as $p): ?>
                        <tr>
                            <td><?= (int)$p['ID_Paziente'] ?></td>
                            <td><?= htmlspecialchars($p['Nome_Farmaco']) ?></td>
                            <td><?= (int)$p['Quantità'] ?></td>
                            <td><?= htmlspecialchars($p['Data_Prescrizione']) ?></td>
                            <td class="actions-cell">
                                <a href="modifica_prescrizione.php?id_paziente=<?= (int)$p['ID_Paziente'] ?>&id_farmaco=<?= (int)$p['ID_Farmaco'] ?>&data_prescrizione=<?= urlencode($p['Data_Prescrizione']) ?>"
                                   class="btn btn-sm btn-secondary">✏️ Modifica</a>
                                <form method="post" action="elimina_prescrizione.php" class="inline"
                                      onsubmit="return confirm('Eliminare questa prescrizione?')">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="id_paziente" value="<?= (int)$p['ID_Paziente'] ?>">
                                    <input type="hidden" name="id_farmaco" value="<?= (int)$p['ID_Farmaco'] ?>">
                                    <input type="hidden" name="data_prescrizione" value="<?= htmlspecialchars($p['Data_Prescrizione']) ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">🗑️ Elimina</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($prescrizioni)): ?>
                        <tr><td colspan="5" class="text-center" style="color:var(--gray-700);padding:20px">Nessuna prescrizione.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<a href="paziente.php" class="btn-back">← Indietro ai Pazienti</a>
</body>
</html>
