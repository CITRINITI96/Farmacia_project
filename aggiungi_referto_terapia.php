<?php
require_once 'db.php';
require_once 'auth.php';
requireAuth(['Admin', 'Dottore', 'Dottoressa']);

csrfToken();

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
        $stmt = $pdo->prepare(
            "INSERT INTO RefertiTerapie (ID_Paziente, ID_Farmaco, ID_Dottore, Referto, Terapia, Data_Assegnazione)
             VALUES (?,?,?,?,?,?)"
        );
        $stmt->execute([$id_paziente, $id_farmaco, $id_dottore ?: null, $referto_txt, $terapia_txt, $data_assegnazione]);
        header('Location: gestione_referti.php?msg=aggiunto');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aggiungi Referto — PharmaCare</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="layout">
    <?php if ($_SESSION['role'] === 'Admin') include 'sidebar_admin.php'; ?>

    <div class="main-content" style="<?= $_SESSION['role'] !== 'Admin' ? 'margin-left:0' : '' ?>">
        <div class="page-header">
            <h1>➕ Aggiungi Referto Terapia</h1>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div style="background:var(--white);border-radius:var(--radius-md);padding:24px;box-shadow:var(--shadow-sm);max-width:620px;">
            <form method="POST">
                <?= csrfField() ?>

                <label for="id_paziente">Paziente *</label>
                <select id="id_paziente" name="id_paziente" required>
                    <option value="">— Seleziona paziente —</option>
                    <?php foreach ($pazienti as $p): ?>
                        <option value="<?= (int)$p['ID_Paziente'] ?>"><?= htmlspecialchars($p['Nome_Completo']) ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="id_farmaco">Farmaco *</label>
                <select id="id_farmaco" name="id_farmaco" required>
                    <option value="">— Seleziona farmaco —</option>
                    <?php foreach ($farmaci as $f): ?>
                        <option value="<?= (int)$f['ID_Farmaco'] ?>"><?= htmlspecialchars($f['Nome']) ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="id_dottore">Dottore</label>
                <select id="id_dottore" name="id_dottore">
                    <option value="">— Nessuno —</option>
                    <?php foreach ($dottori as $d): ?>
                        <option value="<?= (int)$d['ID_Utente'] ?>"><?= htmlspecialchars($d['Nome_Completo']) ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="referto">Referto *</label>
                <textarea id="referto" name="referto" rows="3" required
                          style="width:100%;padding:10px;border:1px solid var(--gray-300);border-radius:var(--radius-sm);margin-bottom:16px;"><?= htmlspecialchars($_POST['referto'] ?? '') ?></textarea>

                <label for="terapia">Terapia *</label>
                <textarea id="terapia" name="terapia" rows="3" required
                          style="width:100%;padding:10px;border:1px solid var(--gray-300);border-radius:var(--radius-sm);margin-bottom:16px;"><?= htmlspecialchars($_POST['terapia'] ?? '') ?></textarea>

                <label for="data_assegnazione">Data Assegnazione *</label>
                <input type="date" id="data_assegnazione" name="data_assegnazione" required
                       value="<?= htmlspecialchars($_POST['data_assegnazione'] ?? date('Y-m-d')) ?>">

                <div style="display:flex;gap:12px;margin-top:20px;">
                    <button type="submit" class="btn btn-primary">💾 Aggiungi Referto</button>
                    <a href="gestione_referti.php" class="btn btn-secondary">Annulla</a>
                </div>
            </form>
        </div>
    </div>
</div>
<a href="gestione_referti.php" class="btn-back">← Indietro</a>
</body>
</html>
