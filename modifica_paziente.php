<?php
require_once 'db.php';
require_once 'auth.php';
requireAuth(['Admin', 'Dottore', 'Dottoressa']);

csrfToken();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: paziente.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM Paziente WHERE ID_Paziente = ?");
$stmt->execute([$id]);
$paziente = $stmt->fetch();
if (!$paziente) {
    header('Location: paziente.php');
    exit;
}

$reparti = $pdo->query("SELECT * FROM Reparto ORDER BY Nome_Reparto")->fetchAll();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $nome         = trim($_POST['nome'] ?? '');
    $cognome      = trim($_POST['cognome'] ?? '');
    $data_nascita = $_POST['data_nascita'] ?? '';
    $sesso        = $_POST['sesso'] ?? '';
    $id_reparto   = (int)($_POST['id_reparto'] ?? 0);

    if (empty($nome) || empty($cognome) || empty($data_nascita) || empty($sesso) || $id_reparto <= 0) {
        $error = 'Compila tutti i campi obbligatori.';
    } else {
        $stmt2 = $pdo->prepare("UPDATE Paziente SET Nome=?, Cognome=?, Data_Nascita=?, Sesso=?, ID_Reparto=? WHERE ID_Paziente=?");
        $stmt2->execute([$nome, $cognome, $data_nascita, $sesso, $id_reparto, $id]);
        header('Location: paziente.php?msg=modificato');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Paziente — PharmaCare</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="layout">
    <?php if ($_SESSION['role'] === 'Admin') include 'sidebar_admin.php'; ?>

    <div class="main-content" style="<?= $_SESSION['role'] !== 'Admin' ? 'margin-left:0' : '' ?>">
        <div class="page-header">
            <h1>✏️ Modifica Paziente</h1>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div style="background:var(--white);border-radius:var(--radius-md);padding:24px;box-shadow:var(--shadow-sm);max-width:560px;">
            <form method="POST">
                <?= csrfField() ?>

                <label for="nome">Nome *</label>
                <input type="text" id="nome" name="nome" required
                       value="<?= htmlspecialchars($_POST['nome'] ?? $paziente['Nome']) ?>">

                <label for="cognome">Cognome *</label>
                <input type="text" id="cognome" name="cognome" required
                       value="<?= htmlspecialchars($_POST['cognome'] ?? $paziente['Cognome']) ?>">

                <label for="data_nascita">Data di Nascita *</label>
                <input type="date" id="data_nascita" name="data_nascita" required
                       value="<?= htmlspecialchars($_POST['data_nascita'] ?? $paziente['Data_Nascita']) ?>">

                <label for="sesso">Sesso *</label>
                <select id="sesso" name="sesso" required>
                    <option value="M" <?= ($paziente['Sesso'] === 'M') ? 'selected' : '' ?>>Maschio</option>
                    <option value="F" <?= ($paziente['Sesso'] === 'F') ? 'selected' : '' ?>>Femmina</option>
                </select>

                <label for="id_reparto">Reparto *</label>
                <select id="id_reparto" name="id_reparto" required>
                    <?php foreach ($reparti as $reparto): ?>
                        <option value="<?= (int)$reparto['ID_Reparto'] ?>"
                            <?= ((int)$reparto['ID_Reparto'] === (int)$paziente['ID_Reparto']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($reparto['Nome_Reparto']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <div style="display:flex;gap:12px;margin-top:20px;">
                    <button type="submit" class="btn btn-primary">💾 Salva Modifiche</button>
                    <a href="paziente.php" class="btn btn-secondary">Annulla</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php if ($_SESSION['role'] !== 'Admin'): ?>
    <a href="paziente.php" class="btn-back">← Indietro</a>
<?php endif; ?>
</body>
</html>
