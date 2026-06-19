<?php
require_once 'db.php';
require_once 'auth.php';
requireAuth(['Admin', 'Dottore', 'Dottoressa', 'Farmacista', 'Magazziniere']);

csrfToken();

$message = '';
$msgType = 'success';

// Elimina reparto (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_reparto_elimina'])) {
    verifyCsrf();
    $id = (int)$_POST['id_reparto_elimina'];
    try {
        $stmt = $pdo->prepare("DELETE FROM Reparto WHERE ID_Reparto = ?");
        $stmt->execute([$id]);
        $message = 'Reparto eliminato con successo.';
    } catch (PDOException $e) {
        error_log($e->getMessage());
        $message = 'Impossibile eliminare il reparto (potrebbe avere pazienti associati).';
        $msgType = 'error';
    }
}

// Modifica reparto (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_reparto_modifica'])) {
    verifyCsrf();
    $id           = (int)$_POST['id_reparto_modifica'];
    $nome_reparto = trim($_POST['nome_reparto_modifica'] ?? '');
    $responsabile = trim($_POST['responsabile_modifica'] ?? '');
    if ($id <= 0 || empty($nome_reparto) || empty($responsabile)) {
        $message = 'Dati mancanti o errati.';
        $msgType = 'error';
    } else {
        $stmt = $pdo->prepare("UPDATE Reparto SET Nome_Reparto=?, Responsabile=? WHERE ID_Reparto=?");
        $stmt->execute([$nome_reparto, $responsabile, $id]);
        $message = 'Reparto modificato con successo.';
    }
}

// Aggiungi reparto (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nome_reparto']) && !isset($_POST['id_reparto_modifica'])) {
    verifyCsrf();
    $nome_reparto = trim($_POST['nome_reparto'] ?? '');
    $responsabile = trim($_POST['responsabile'] ?? '');
    if (empty($nome_reparto) || empty($responsabile)) {
        $message = 'Dati mancanti.';
        $msgType = 'error';
    } else {
        $stmt = $pdo->prepare("INSERT INTO Reparto (Nome_Reparto, Responsabile) VALUES (?,?)");
        $stmt->execute([$nome_reparto, $responsabile]);
        $message = 'Reparto aggiunto con successo.';
    }
}

$reparti = $pdo->query("SELECT * FROM Reparto ORDER BY Nome_Reparto")->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Reparti — PharmaCare</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .edit-form { display:flex; gap:8px; align-items:center; flex-wrap:wrap; }
        .edit-form input { flex:1; min-width:120px; padding:6px 10px; font-size:.88rem; }
    </style>
</head>
<body>
<div class="layout">
    <?php if ($_SESSION['role'] === 'Admin') include 'sidebar_admin.php'; ?>

    <div class="main-content" style="<?= $_SESSION['role'] !== 'Admin' ? 'margin-left:0' : '' ?>">
        <div class="page-header">
            <h1>🏥 Gestione Reparti</h1>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?= $msgType === 'error' ? 'error' : 'success' ?>">
                <?= $msgType === 'error' ? '⚠️' : '✅' ?> <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- FORM AGGIUNGI -->
        <div style="background:var(--white);border-radius:var(--radius-md);padding:24px;box-shadow:var(--shadow-sm);margin-bottom:24px;">
            <h2>➕ Aggiungi Reparto</h2>
            <form method="POST" style="display:grid;grid-template-columns:1fr 1fr auto;gap:16px;align-items:end;">
                <?= csrfField() ?>
                <div>
                    <label>Nome Reparto *</label>
                    <input type="text" name="nome_reparto" required placeholder="es. Cardiologia">
                </div>
                <div>
                    <label>Responsabile *</label>
                    <input type="text" name="responsabile" required placeholder="es. Dr. Rossi">
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">Aggiungi</button>
                </div>
            </form>
        </div>

        <!-- TABELLA -->
        <div style="background:var(--white);border-radius:var(--radius-md);padding:24px;box-shadow:var(--shadow-sm);">
            <h2>📋 Elenco Reparti (<?= count($reparti) ?>)</h2>
            <div class="table-wrapper table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome Reparto</th>
                            <th>Responsabile</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($reparti as $reparto): ?>
                        <tr>
                            <td><?= (int)$reparto['ID_Reparto'] ?></td>
                            <td><?= htmlspecialchars($reparto['Nome_Reparto']) ?></td>
                            <td><?= htmlspecialchars($reparto['Responsabile']) ?></td>
                            <td>
                                <!-- Modifica inline -->
                                <form method="POST" class="edit-form">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="id_reparto_modifica" value="<?= (int)$reparto['ID_Reparto'] ?>">
                                    <input type="text" name="nome_reparto_modifica" value="<?= htmlspecialchars($reparto['Nome_Reparto']) ?>" required>
                                    <input type="text" name="responsabile_modifica" value="<?= htmlspecialchars($reparto['Responsabile']) ?>" required>
                                    <button type="submit" class="btn btn-sm btn-secondary">✏️ Salva</button>
                                </form>
                                <!-- Elimina -->
                                <form method="POST" class="inline" style="margin-top:4px;"
                                      onsubmit="return confirm('Eliminare il reparto «<?= htmlspecialchars(addslashes($reparto['Nome_Reparto'])) ?>»?')">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="id_reparto_elimina" value="<?= (int)$reparto['ID_Reparto'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">🗑️ Elimina</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($reparti)): ?>
                        <tr><td colspan="4" class="text-center" style="color:var(--gray-700);padding:20px">Nessun reparto.</td></tr>
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
