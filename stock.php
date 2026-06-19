<?php
require_once 'db.php';
require_once 'auth.php';
requireAuth(['Admin', 'Farmacista', 'Magazziniere']);

csrfToken();

$farmaci  = $pdo->query("SELECT ID_Farmaco, Nome FROM Farmaco ORDER BY Nome")->fetchAll();
$magazzini = $pdo->query("SELECT ID_Magazzino, Nome_Magazzino FROM Magazzino ORDER BY Nome_Magazzino")->fetchAll();

$message = '';
$msgType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $id_farmaco    = (int)($_POST['id_farmaco'] ?? 0);
    $id_magazzino  = (int)($_POST['id_magazzino'] ?? 0);
    $quantita      = (int)($_POST['quantita'] ?? 0);
    $data_scadenza = $_POST['data_scadenza'] ?? '';
    $temperatura   = !empty($_POST['temperatura']) ? (float)$_POST['temperatura'] : null;

    if ($id_farmaco <= 0 || $id_magazzino <= 0 || $quantita <= 0 || empty($data_scadenza)) {
        $message = 'Compila tutti i campi obbligatori con valori validi.';
        $msgType = 'error';
    } else {
        $stmt = $pdo->prepare("INSERT INTO Stock (ID_Farmaco, ID_Magazzino, Quantità, Data_Scadenza, Temperatura_Stoccaggio) VALUES (?,?,?,?,?)");
        $stmt->execute([$id_farmaco, $id_magazzino, $quantita, $data_scadenza, $temperatura]);
        header('Location: stock.php?msg=aggiunto');
        exit;
    }
}

if (isset($_GET['msg']) && $_GET['msg'] === 'aggiunto') {
    $message = 'Stock aggiunto con successo.';
}

$stock = $pdo->query("SELECT S.ID_Stock, F.Nome AS Nome_Farmaco, M.Nome_Magazzino,
                             S.Quantità, S.Data_Scadenza, S.Temperatura_Stoccaggio
                      FROM Stock S
                      JOIN Farmaco F ON S.ID_Farmaco = F.ID_Farmaco
                      JOIN Magazzino M ON S.ID_Magazzino = M.ID_Magazzino
                      ORDER BY S.Data_Scadenza ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Stock — PharmaCare</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="layout">
    <?php if ($_SESSION['role'] === 'Admin') include 'sidebar_admin.php'; ?>

    <div class="main-content" style="<?= $_SESSION['role'] !== 'Admin' ? 'margin-left:0' : '' ?>">
        <div class="page-header">
            <h1>📊 Gestione Stock</h1>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?= $msgType === 'error' ? 'error' : 'success' ?>">
                <?= $msgType === 'error' ? '⚠️' : '✅' ?> <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- FORM AGGIUNGI -->
        <div style="background:var(--white);border-radius:var(--radius-md);padding:24px;box-shadow:var(--shadow-sm);margin-bottom:24px;">
            <h2>➕ Aggiungi Stock</h2>
            <form method="POST" style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr 1fr auto;gap:14px;align-items:end;">
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
                    <label>Magazzino *</label>
                    <select name="id_magazzino" required>
                        <option value="">— Seleziona —</option>
                        <?php foreach ($magazzini as $m): ?>
                            <option value="<?= (int)$m['ID_Magazzino'] ?>"><?= htmlspecialchars($m['Nome_Magazzino']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label>Quantità *</label>
                    <input type="number" name="quantita" min="1" required placeholder="es. 500">
                </div>
                <div>
                    <label>Data Scadenza *</label>
                    <input type="date" name="data_scadenza" required>
                </div>
                <div>
                    <label>Temperatura (°C)</label>
                    <input type="number" step="0.1" name="temperatura" placeholder="es. 4.5">
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">Aggiungi</button>
                </div>
            </form>
        </div>

        <!-- TABELLA -->
        <div style="background:var(--white);border-radius:var(--radius-md);padding:24px;box-shadow:var(--shadow-sm);">
            <h2>📋 Elenco Stock (<?= count($stock) ?>)</h2>
            <div class="table-wrapper table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th><th>Farmaco</th><th>Magazzino</th><th>Quantità</th><th>Scadenza</th><th>Temp.</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($stock as $s): ?>
                        <tr>
                            <td><?= (int)$s['ID_Stock'] ?></td>
                            <td><?= htmlspecialchars($s['Nome_Farmaco']) ?></td>
                            <td><?= htmlspecialchars($s['Nome_Magazzino']) ?></td>
                            <td><?= (int)$s['Quantità'] ?></td>
                            <td><?= htmlspecialchars($s['Data_Scadenza']) ?></td>
                            <td><?= $s['Temperatura_Stoccaggio'] !== null ? htmlspecialchars($s['Temperatura_Stoccaggio']) . ' °C' : '—' ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($stock)): ?>
                        <tr><td colspan="6" class="text-center" style="color:var(--gray-700);padding:20px">Nessun record di stock.</td></tr>
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
