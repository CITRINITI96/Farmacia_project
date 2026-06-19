<?php
require_once 'db.php';
require_once 'auth.php';
requireAuth(['Admin', 'Dottore', 'Dottoressa']);

csrfToken();

$id_paziente        = (int)($_GET['id_paziente'] ?? 0);
$id_farmaco_get     = (int)($_GET['id_farmaco'] ?? 0);
$data_prescrizione  = $_GET['data_prescrizione'] ?? '';

if ($id_paziente <= 0 || $id_farmaco_get <= 0 || empty($data_prescrizione)) {
    header('Location: paziente.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM Prescrizione WHERE ID_Paziente=? AND ID_Farmaco=? AND Data_Prescrizione=?");
$stmt->execute([$id_paziente, $id_farmaco_get, $data_prescrizione]);
$prescrizione = $stmt->fetch();
if (!$prescrizione) {
    header("Location: gestione_prescrizione.php?id_paziente=$id_paziente");
    exit;
}

$farmaci = $pdo->query("SELECT ID_Farmaco, Nome FROM Farmaco ORDER BY Nome")->fetchAll();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $id_farmaco_nuovo = (int)($_POST['id_farmaco'] ?? 0);
    $quantita         = (int)($_POST['quantita'] ?? 0);

    if ($id_farmaco_nuovo <= 0 || $quantita <= 0) {
        $error = 'Compila tutti i campi con valori validi.';
    } else {
        $stmt2 = $pdo->prepare(
            "UPDATE Prescrizione SET ID_Farmaco=?, Quantità=?
             WHERE ID_Paziente=? AND ID_Farmaco=? AND Data_Prescrizione=?"
        );
        $stmt2->execute([$id_farmaco_nuovo, $quantita, $id_paziente, $id_farmaco_get, $data_prescrizione]);
        header("Location: gestione_prescrizione.php?id_paziente=$id_paziente&msg=modificato");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Prescrizione — PharmaCare</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="layout">
    <?php if ($_SESSION['role'] === 'Admin') include 'sidebar_admin.php'; ?>

    <div class="main-content" style="<?= $_SESSION['role'] !== 'Admin' ? 'margin-left:0' : '' ?>">
        <div class="page-header">
            <h1>✏️ Modifica Prescrizione</h1>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div style="background:var(--white);border-radius:var(--radius-md);padding:24px;box-shadow:var(--shadow-sm);max-width:560px;">
            <form method="POST">
                <?= csrfField() ?>

                <label for="id_farmaco">Farmaco *</label>
                <select id="id_farmaco" name="id_farmaco" required>
                    <?php foreach ($farmaci as $f): ?>
                        <option value="<?= (int)$f['ID_Farmaco'] ?>"
                            <?= ((int)$f['ID_Farmaco'] === (int)$prescrizione['ID_Farmaco']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($f['Nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="quantita">Quantità *</label>
                <input type="number" id="quantita" name="quantita" min="1" required
                       value="<?= (int)$prescrizione['Quantità'] ?>">

                <p style="color:var(--gray-700);font-size:.88rem;">Data prescrizione: <strong><?= htmlspecialchars($data_prescrizione) ?></strong></p>

                <div style="display:flex;gap:12px;margin-top:20px;">
                    <button type="submit" class="btn btn-primary">💾 Salva Modifiche</button>
                    <a href="gestione_prescrizione.php?id_paziente=<?= $id_paziente ?>" class="btn btn-secondary">Annulla</a>
                </div>
            </form>
        </div>
    </div>
</div>
<a href="gestione_prescrizione.php?id_paziente=<?= $id_paziente ?>" class="btn-back">← Indietro</a>
</body>
</html>
