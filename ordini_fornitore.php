<?php
require_once 'db.php';
require_once 'auth.php';
requireAuth(['Admin', 'Farmacista', 'Magazziniere']);

csrfToken();

$farmaci = $pdo->query("SELECT ID_Farmaco, Nome FROM Farmaco ORDER BY Nome")->fetchAll();
$message = '';
$msgType = 'success';

// Inserimento nuovo ordine
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_farmaco']) && !isset($_POST['update_stato'])) {
    verifyCsrf();
    $id_farmaco  = (int)($_POST['id_farmaco'] ?? 0);
    $quantita    = (int)($_POST['quantita'] ?? 0);
    $data_ordine = $_POST['data_ordine'] ?? '';
    $stato       = $_POST['stato'] ?? 'In_Elaborazione';
    $valid_stati = ['In_Elaborazione', 'Evadibile', 'Annullato'];

    if ($id_farmaco <= 0 || $quantita <= 0 || empty($data_ordine) || !in_array($stato, $valid_stati)) {
        $message = 'Dati non validi. Controlla i campi.';
        $msgType = 'error';
    } else {
        $stmt = $pdo->prepare("INSERT INTO Ordine_Fornitore (ID_Farmaco, Quantità_Ordinata, Data_Ordine, Stato) VALUES (?,?,?,?)");
        $stmt->execute([$id_farmaco, $quantita, $data_ordine, $stato]);
        header('Location: ordini_fornitore.php?msg=aggiunto');
        exit;
    }
}

// Aggiornamento stato ordine
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stato'])) {
    verifyCsrf();
    $id_ordine = (int)($_POST['id_ordine'] ?? 0);
    $stato     = $_POST['stato'] ?? '';
    $valid_stati = ['In_Elaborazione', 'Evadibile', 'Annullato'];

    if ($id_ordine > 0 && in_array($stato, $valid_stati)) {
        $stmt = $pdo->prepare("UPDATE Ordine_Fornitore SET Stato=? WHERE ID_Ordine=?");
        $stmt->execute([$stato, $id_ordine]);
        header('Location: ordini_fornitore.php?msg=modificato');
        exit;
    }
}

// Flash message
if (isset($_GET['msg'])) {
    $msgs = ['aggiunto' => 'Ordine aggiunto con successo.', 'modificato' => 'Stato ordine aggiornato.'];
    $message = $msgs[$_GET['msg']] ?? '';
}

$ordini = $pdo->query("SELECT O.ID_Ordine, F.Nome AS Nome_Farmaco, O.Quantità_Ordinata, O.Data_Ordine, O.Stato
                       FROM Ordine_Fornitore O
                       JOIN Farmaco F ON O.ID_Farmaco = F.ID_Farmaco
                       ORDER BY O.Data_Ordine DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordini Fornitori — PharmaCare</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="layout">
    <?php if ($_SESSION['role'] === 'Admin') include 'sidebar_admin.php'; ?>

    <div class="main-content" style="<?= $_SESSION['role'] !== 'Admin' ? 'margin-left:0' : '' ?>">
        <div class="page-header">
            <h1>📦 Gestione Ordini Fornitori</h1>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?= $msgType === 'error' ? 'error' : 'success' ?>">
                <?= $msgType === 'error' ? '⚠️' : '✅' ?> <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- FORM AGGIUNGI ORDINE -->
        <div style="background:var(--white);border-radius:var(--radius-md);padding:24px;box-shadow:var(--shadow-sm);margin-bottom:24px;">
            <h2>➕ Aggiungi Ordine</h2>
            <form method="POST" style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr auto;gap:14px;align-items:end;">
                <?= csrfField() ?>
                <div>
                    <label>Farmaco *</label>
                    <select name="id_farmaco" required>
                        <option value="">— Seleziona —</option>
                        <?php foreach ($farmaci as $f): ?>
                            <option value="<?= (int)$f['ID_Farmaco'] ?>"><?= htmlspecialchars($f['Nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label>Quantità *</label>
                    <input type="number" name="quantita" min="1" required placeholder="es. 100">
                </div>
                <div>
                    <label>Data Ordine *</label>
                    <input type="date" name="data_ordine" required value="<?= date('Y-m-d') ?>">
                </div>
                <div>
                    <label>Stato</label>
                    <select name="stato">
                        <option value="In_Elaborazione">In Elaborazione</option>
                        <option value="Evadibile">Evadibile</option>
                        <option value="Annullato">Annullato</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">Aggiungi</button>
                </div>
            </form>
        </div>

        <!-- TABELLA -->
        <div style="background:var(--white);border-radius:var(--radius-md);padding:24px;box-shadow:var(--shadow-sm);">
            <h2>📋 Elenco Ordini (<?= count($ordini) ?>)</h2>
            <div class="table-wrapper table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Farmaco</th>
                            <th>Quantità</th>
                            <th>Data Ordine</th>
                            <th>Stato</th>
                            <th>Modifica Stato</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($ordini as $o): ?>
                        <tr>
                            <td><?= (int)$o['ID_Ordine'] ?></td>
                            <td><?= htmlspecialchars($o['Nome_Farmaco']) ?></td>
                            <td><?= (int)$o['Quantità_Ordinata'] ?></td>
                            <td><?= htmlspecialchars($o['Data_Ordine']) ?></td>
                            <td>
                                <?php
                                $badgeClass = match($o['Stato']) {
                                    'In_Elaborazione' => 'badge badge-warning',
                                    'Evadibile'       => 'badge badge-success',
                                    'Annullato'       => 'badge badge-danger',
                                    default           => 'badge'
                                };
                                ?>
                                <span class="<?= $badgeClass ?>"><?= htmlspecialchars($o['Stato']) ?></span>
                            </td>
                            <td>
                                <form method="POST" style="display:flex;gap:6px;align-items:center;">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="id_ordine" value="<?= (int)$o['ID_Ordine'] ?>">
                                    <select name="stato" style="padding:4px 8px;font-size:.82rem;">
                                        <option value="In_Elaborazione" <?= $o['Stato'] === 'In_Elaborazione' ? 'selected' : '' ?>>In Elaborazione</option>
                                        <option value="Evadibile" <?= $o['Stato'] === 'Evadibile' ? 'selected' : '' ?>>Evadibile</option>
                                        <option value="Annullato" <?= $o['Stato'] === 'Annullato' ? 'selected' : '' ?>>Annullato</option>
                                    </select>
                                    <button type="submit" name="update_stato" class="btn btn-sm btn-secondary">✔</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($ordini)): ?>
                        <tr><td colspan="6" class="text-center" style="color:var(--gray-700);padding:20px">Nessun ordine presente.</td></tr>
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
