<?php
require_once 'db.php';
require_once 'auth.php';
requireAuth(['Admin', 'Dottore', 'Dottoressa']);

csrfToken();

$id_referto = (int)($_GET['id_referto'] ?? 0);
if ($id_referto <= 0) {
    header('Location: gestione_referti.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM RefertiTerapie WHERE ID_Referto = ?");
$stmt->execute([$id_referto]);
$referto = $stmt->fetch();
if (!$referto) {
    header('Location: gestione_referti.php');
    exit;
}

$pazienti = $pdo->query("SELECT ID_Paziente, CONCAT(Nome,' ',Cognome) AS Nome_Completo FROM Paziente ORDER BY Cognome")->fetchAll();
$farmaci  = $pdo->query("SELECT ID_Farmaco, Nome FROM Farmaco ORDER BY Nome")->fetchAll();
$dottori  = $pdo->query("SELECT ID_Utente, CONCAT(Nome,' ',Cognome) AS Nome_Completo FROM Utente WHERE Ruolo IN ('Dottore','Dottoressa') ORDER BY Cognome")->fetchAll();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $id_paziente       = (int)($_POST['id_paziente'] ?? 0);
    $id_farmaco        = (int)($_POST['id_farmaco'] ?? 0);
    $id_dottore        = (int)($_POST['id_dottore'] ?? 0);
    $referto_txt       = trim($_POST['referto'] ?? '');
    $terapia_txt       = trim($_POST['terapia'] ?? '');
    $data_assegnazione = $_POST['data_assegnazione'] ?? '';

    if ($id_paziente <= 0 || $id_farmaco <= 0 || empty($referto_txt) || empty($terapia_txt) || empty($data_assegnazione)) {
        $error = 'Compila tutti i campi obbligatori.';
    } else {
        $stmt2 = $pdo->prepare(
            "UPDATE RefertiTerapie SET ID_Paziente=?, ID_Farmaco=?, ID_Dottore=?, Referto=?, Terapia=?, Data_Assegnazione=?
             WHERE ID_Referto=?"
        );
        $stmt2->execute([$id_paziente, $id_farmaco, $id_dottore ?: null, $referto_txt, $terapia_txt, $data_assegnazione, $id_referto]);
        header('Location: gestione_referti.php?msg=modificato');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Referto — PharmaCare</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="layout">
    <?php if ($_SESSION['role'] === 'Admin') include 'sidebar_admin.php'; ?>

    <div class="main-content" style="<?= $_SESSION['role'] !== 'Admin' ? 'margin-left:0' : '' ?>">
        <div class="page-header">
            <h1>✏️ Modifica Referto Terapia</h1>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div style="background:var(--white);border-radius:var(--radius-md);padding:24px;box-shadow:var(--shadow-sm);max-width:620px;">
            <form method="POST">
                <?= csrfField() ?>

                <label for="id_paziente">Paziente *</label>
                <select id="id_paziente" name="id_paziente" required>
                    <?php foreach ($pazienti as $p): ?>
                        <option value="<?= (int)$p['ID_Paziente'] ?>"
                            <?= ((int)$p['ID_Paziente'] === (int)$referto['ID_Paziente']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['Nome_Completo']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="id_farmaco">Farmaco *</label>
                <select id="id_farmaco" name="id_farmaco" required>
                    <?php foreach ($farmaci as $f): ?>
                        <option value="<?= (int)$f['ID_Farmaco'] ?>"
                            <?= ((int)$f['ID_Farmaco'] === (int)$referto['ID_Farmaco']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($f['Nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="id_dottore">Dottore</label>
                <select id="id_dottore" name="id_dottore">
                    <option value="">— Nessuno —</option>
                    <?php foreach ($dottori as $d): ?>
                        <option value="<?= (int)$d['ID_Utente'] ?>"
                            <?= ((int)$d['ID_Utente'] === (int)$referto['ID_Dottore']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($d['Nome_Completo']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="referto">Referto *</label>
                <textarea id="referto" name="referto" rows="3" required
                          style="width:100%;padding:10px;border:1px solid var(--gray-300);border-radius:var(--radius-sm);"><?= htmlspecialchars($referto['Referto']) ?></textarea>

                <label for="terapia" style="margin-top:12px;">Terapia *</label>
                <textarea id="terapia" name="terapia" rows="3" required
                          style="width:100%;padding:10px;border:1px solid var(--gray-300);border-radius:var(--radius-sm);"><?= htmlspecialchars($referto['Terapia']) ?></textarea>

                <label for="data_assegnazione" style="margin-top:12px;">Data Assegnazione *</label>
                <input type="date" id="data_assegnazione" name="data_assegnazione" required
                       value="<?= htmlspecialchars($referto['Data_Assegnazione']) ?>">

                <div style="display:flex;gap:12px;margin-top:20px;">
                    <button type="submit" class="btn btn-primary">💾 Salva Modifiche</button>
                    <a href="gestione_referti.php" class="btn btn-secondary">Annulla</a>
                </div>
            </form>
        </div>
    </div>
</div>
<a href="gestione_referti.php" class="btn-back">← Indietro</a>
</body>
</html>
