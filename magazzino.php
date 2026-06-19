<?php
require_once 'db.php';
require_once 'auth.php';
requireAuth(['Admin', 'Magazziniere']);

csrfToken();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nome_magazzino'])) {
    verifyCsrf();
    $nome       = trim($_POST['nome_magazzino'] ?? '');
    $ubicazione = trim($_POST['ubicazione'] ?? '');
    if ($nome !== '') {
        $stmt = $pdo->prepare("INSERT INTO Magazzino (Nome_Magazzino, Ubicazione) VALUES (?, ?)");
        $stmt->execute([$nome, $ubicazione]);
        header('Location: magazzino.php?msg=aggiunto');
        exit;
    }
}

if (isset($_GET['msg'])) {
    $message = $_GET['msg'] === 'aggiunto' ? 'Magazzino aggiunto con successo.' : '';
}

$magazzini = $pdo->query("SELECT * FROM Magazzino ORDER BY Nome_Magazzino")->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Magazzini — PharmaCare</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="layout">
    <?php if ($_SESSION['role'] === 'Admin') include 'sidebar_admin.php'; ?>

    <div class="main-content" style="<?= $_SESSION['role'] !== 'Admin' ? 'margin-left:0' : '' ?>">
        <div class="page-header">
            <h1>🏭 Gestione Magazzini</h1>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success">✅ <?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <!-- FORM ADD -->
        <div style="background:var(--white);border-radius:var(--radius-md);padding:24px;box-shadow:var(--shadow-sm);margin-bottom:24px;">
            <h2>➕ Aggiungi Magazzino</h2>
            <form method="post" style="display:grid;grid-template-columns:1fr 1fr auto;gap:16px;align-items:end;">
                <?= csrfField() ?>
                <div>
                    <label>Nome Magazzino *</label>
                    <input type="text" name="nome_magazzino" required placeholder="es. Magazzino Centrale">
                </div>
                <div>
                    <label>Ubicazione</label>
                    <input type="text" name="ubicazione" placeholder="es. Edificio A - Piano Terra">
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">Aggiungi</button>
                </div>
            </form>
        </div>

        <!-- TABELLA -->
        <div style="background:var(--white);border-radius:var(--radius-md);padding:24px;box-shadow:var(--shadow-sm);">
            <h2>📋 Elenco Magazzini (<?= count($magazzini) ?>)</h2>
            <div class="table-wrapper table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Ubicazione</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($magazzini as $m): ?>
                        <tr>
                            <td><?= (int)$m['ID_Magazzino'] ?></td>
                            <td><?= htmlspecialchars($m['Nome_Magazzino']) ?></td>
                            <td><?= htmlspecialchars($m['Ubicazione'] ?? '—') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($magazzini)): ?>
                        <tr><td colspan="3" class="text-center" style="color:var(--gray-700);padding:20px">Nessun magazzino presente.</td></tr>
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
