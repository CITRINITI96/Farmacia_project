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

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $id_farmaco        = (int)($_POST['id_farmaco'] ?? 0);
    $quantita          = (int)($_POST['quantita'] ?? 0);
    $data_prescrizione = $_POST['data_prescrizione'] ?? '';

    if ($id_farmaco <= 0 || $quantita <= 0 || empty($data_prescrizione)) {
        $error = 'Compila tutti i campi obbligatori con valori validi.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO Prescrizione (ID_Paziente, ID_Farmaco, Quantità, Data_Prescrizione) VALUES (?,?,?,?)");
        $stmt->execute([$id_paziente, $id_farmaco, $quantita, $data_prescrizione]);
        header("Location: gestione_prescrizione.php?id_paziente=$id_paziente&msg=aggiunto");
        exit;
    }
}

$farmaci = $pdo->query("SELECT ID_Farmaco, Nome FROM Farmaco ORDER BY Nome")->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aggiungi Prescrizione — PharmaCare</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="layout">
    <?php if ($_SESSION['role'] === 'Admin') include 'sidebar_admin.php'; ?>

    <div class="main-content" style="<?= $_SESSION['role'] !== 'Admin' ? 'margin-left:0' : '' ?>">
        <div class="page-header">
            <h1>➕ Aggiungi Prescrizione</h1>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div style="background:var(--white);border-radius:var(--radius-md);padding:24px;box-shadow:var(--shadow-sm);max-width:560px;">
            <form method="POST">
                <?= csrfField() ?>

                <label for="id_farmaco">Farmaco *</label>
                <select id="id_farmaco" name="id_farmaco" required>
                    <option value="">— Seleziona farmaco —</option>
                    <?php foreach ($farmaci as $f): ?>
                        <option value="<?= (int)$f['ID_Farmaco'] ?>"><?= htmlspecialchars($f['Nome']) ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="quantita">Quantità *</label>
                <input type="number" id="quantita" name="quantita" min="1" required
                       value="<?= htmlspecialchars($_POST['quantita'] ?? '') ?>">

                <label for="data_prescrizione">Data Prescrizione *</label>
                <input type="date" id="data_prescrizione" name="data_prescrizione" required
                       value="<?= htmlspecialchars($_POST['data_prescrizione'] ?? date('Y-m-d')) ?>">

                <div style="display:flex;gap:12px;margin-top:20px;">
                    <button type="submit" class="btn btn-primary">💾 Aggiungi Prescrizione</button>
                    <a href="gestione_prescrizione.php?id_paziente=<?= $id_paziente ?>" class="btn btn-secondary">Annulla</a>
                </div>
            </form>
        </div>
    </div>
</div>
<a href="gestione_prescrizione.php?id_paziente=<?= $id_paziente ?>" class="btn-back">← Indietro</a>
</body>
</html>
