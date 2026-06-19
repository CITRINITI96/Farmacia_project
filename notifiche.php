<?php
require_once 'db.php';
require_once 'auth.php';
requireAuth(['Admin', 'Farmacista']);

csrfToken();

$message = '';
$msgType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    if (isset($_POST['notifiche_scorte'])) {
        try {
            $pdo->query("CALL Genera_Notifiche_Scorte_Basse()");
            $message = 'Notifiche scorte basse generate.';
        } catch (PDOException $e) {
            error_log('[NOTIFICHE] ' . $e->getMessage());
            $message = 'Errore nella generazione notifiche scorte.';
            $msgType = 'error';
        }
    } elseif (isset($_POST['notifiche_scadenze'])) {
        try {
            $pdo->query("CALL Genera_Notifiche_Scadenza_Imminente()");
            $message = 'Notifiche scadenze imminenti generate.';
        } catch (PDOException $e) {
            error_log('[NOTIFICHE] ' . $e->getMessage());
            $message = 'Errore nella generazione notifiche scadenze.';
            $msgType = 'error';
        }
    } elseif (isset($_POST['azzera_notifiche'])) {
        $pdo->query("DELETE FROM Notifiche");
        $message = 'Notifiche azzerate.';
    }
}

$notifiche = $pdo->query("SELECT N.ID_Notifica, N.Tipo_Notifica, N.ID_Stock, N.Messaggio, N.Data_Notifica,
                                  F.Nome AS Nome_Farmaco
                           FROM Notifiche N
                           LEFT JOIN Stock S ON N.ID_Stock = S.ID_Stock
                           LEFT JOIN Farmaco F ON S.ID_Farmaco = F.ID_Farmaco
                           ORDER BY N.Data_Notifica DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifiche — PharmaCare</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="layout">
    <?php if ($_SESSION['role'] === 'Admin') include 'sidebar_admin.php'; ?>

    <div class="main-content" style="<?= $_SESSION['role'] !== 'Admin' ? 'margin-left:0' : '' ?>">
        <div class="page-header">
            <h1>🔔 Gestione Notifiche</h1>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?= $msgType === 'error' ? 'error' : 'success' ?>">
                <?= $msgType === 'error' ? '⚠️' : '✅' ?> <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- AZIONI -->
        <div style="background:var(--white);border-radius:var(--radius-md);padding:24px;box-shadow:var(--shadow-sm);margin-bottom:24px;">
            <h2>⚙️ Azioni</h2>
            <form method="POST" style="display:flex;gap:12px;flex-wrap:wrap;">
                <?= csrfField() ?>
                <button type="submit" name="notifiche_scorte" class="btn btn-secondary">📉 Genera: Scorte Basse</button>
                <button type="submit" name="notifiche_scadenze" class="btn btn-secondary">⏰ Genera: Scadenze Imminenti</button>
                <button type="submit" name="azzera_notifiche" class="btn btn-danger"
                        onclick="return confirm('Sei sicuro di voler azzerare tutte le notifiche?')">🗑️ Azzera Notifiche</button>
            </form>
        </div>

        <!-- TABELLA -->
        <div style="background:var(--white);border-radius:var(--radius-md);padding:24px;box-shadow:var(--shadow-sm);">
            <h2>📋 Elenco Notifiche (<?= count($notifiche) ?>)</h2>
            <div class="table-wrapper table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th><th>Tipo</th><th>Stock ID</th><th>Farmaco</th><th>Messaggio</th><th>Data</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($notifiche as $n): ?>
                        <tr>
                            <td><?= (int)$n['ID_Notifica'] ?></td>
                            <td>
                                <span class="badge <?= str_contains($n['Tipo_Notifica'], 'Scadenz') ? 'badge-warning' : 'badge-danger' ?>">
                                    <?= htmlspecialchars($n['Tipo_Notifica']) ?>
                                </span>
                            </td>
                            <td><?= (int)$n['ID_Stock'] ?></td>
                            <td><?= htmlspecialchars($n['Nome_Farmaco'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($n['Messaggio']) ?></td>
                            <td><?= htmlspecialchars($n['Data_Notifica']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($notifiche)): ?>
                        <tr><td colspan="6" class="text-center" style="color:var(--gray-700);padding:20px">Nessuna notifica presente.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php if ($_SESSION['role'] !== 'Admin'): ?>
    <a href="menu.php" class="btn-back">← Indietro</a>
<?php endif; ?>
</body>
</html>
