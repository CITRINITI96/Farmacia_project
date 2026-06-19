<?php
require_once 'db.php';
require_once 'auth.php';
requireAuth(['Admin', 'Farmacista']);

csrfToken(); // assicura che il token esista

$message = '';
$msgType = 'success';

// --- ELIMINA via POST con CSRF ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['elimina_id'])) {
    verifyCsrf();
    $id = (int) $_POST['elimina_id'];
    $stmt = $pdo->prepare("DELETE FROM Farmaco WHERE ID_Farmaco = ?");
    $stmt->execute([$id]);
    $message = 'Farmaco eliminato con successo.';
    header('Location: farmaco.php?msg=eliminato');
    exit;
}

// --- AGGIORNA farmaco ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_farmaco'])) {
    verifyCsrf();
    $id           = (int) $_POST['id_farmaco'];
    $nome         = trim($_POST['nome'] ?? '');
    $categoria    = $_POST['categoria'] ?? '';
    $pericolosita = isset($_POST['pericolosita']) ? 1 : 0;
    $prezzo       = (float) ($_POST['prezzo_unitario'] ?? 0);

    $stmt = $pdo->prepare("UPDATE Farmaco SET Nome=?, Categoria=?, Pericolosità=?, Prezzo_Unitario=? WHERE ID_Farmaco=?");
    $stmt->execute([$nome, $categoria, $pericolosita, $prezzo, $id]);
    header('Location: farmaco.php?msg=modificato');
    exit;
}

// --- INSERISCE nuovo farmaco ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nome']) && !isset($_POST['id_farmaco'])) {
    verifyCsrf();
    $nome         = trim($_POST['nome'] ?? '');
    $categoria    = $_POST['categoria'] ?? '';
    $pericolosita = isset($_POST['pericolosita']) ? 1 : 0;
    $prezzo       = (float) ($_POST['prezzo_unitario'] ?? 0);

    $stmt = $pdo->prepare("INSERT INTO Farmaco (Nome, Categoria, Pericolosità, Prezzo_Unitario) VALUES (?,?,?,?)");
    $stmt->execute([$nome, $categoria, $pericolosita, $prezzo]);
    header('Location: farmaco.php?msg=aggiunto');
    exit;
}

// Flash message da redirect
if (isset($_GET['msg'])) {
    $msgs = ['aggiunto' => 'Farmaco aggiunto con successo.', 'modificato' => 'Farmaco modificato.', 'eliminato' => 'Farmaco eliminato.'];
    $message = $msgs[$_GET['msg']] ?? '';
}

// Farmaco da modificare
$farmacoDaModificare = null;
if (isset($_GET['id_farmaco'])) {
    $stmt = $pdo->prepare("SELECT * FROM Farmaco WHERE ID_Farmaco = ?");
    $stmt->execute([(int) $_GET['id_farmaco']]);
    $farmacoDaModificare = $stmt->fetch();
}

$farmaci = $pdo->query("SELECT * FROM Farmaco ORDER BY Nome")->fetchAll();

$categorias = ['Antibiotico','Analgesico','Antipiretico','Antidolorifico','Antinfiammatorio',
               'Antidepressivo','Anticoagulante','Beta-Bloccante','Diuretico',
               'Farmaco da banco','Farmaco da prescrizione','Altro'];
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Farmaci — PharmaCare</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="layout">
    <?php if ($_SESSION['role'] === 'Admin') include 'sidebar_admin.php'; ?>

    <div class="main-content" style="<?= $_SESSION['role'] !== 'Admin' ? 'margin-left:0' : '' ?>">
        <div class="page-header flex justify-between items-center">
            <h1>💊 Gestione Farmaci</h1>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success">✅ <?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <!-- FORM ADD / EDIT -->
        <div style="background:var(--white);border-radius:var(--radius-md);padding:24px;box-shadow:var(--shadow-sm);margin-bottom:24px;">
            <h2><?= $farmacoDaModificare ? '✏️ Modifica Farmaco' : '➕ Aggiungi Farmaco' ?></h2>
            <form method="post" style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <?= csrfField() ?>
                <?php if ($farmacoDaModificare): ?>
                    <input type="hidden" name="id_farmaco" value="<?= (int)$farmacoDaModificare['ID_Farmaco'] ?>">
                <?php endif; ?>

                <div>
                    <label>Nome Farmaco *</label>
                    <input type="text" name="nome" required value="<?= htmlspecialchars($farmacoDaModificare['Nome'] ?? '') ?>">
                </div>
                <div>
                    <label>Categoria *</label>
                    <select name="categoria" required>
                        <option value="">— Seleziona —</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= htmlspecialchars($cat) ?>"
                                <?= ($farmacoDaModificare['Categoria'] ?? '') === $cat ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label>Prezzo Unitario (€) *</label>
                    <input type="number" step="0.01" min="0" name="prezzo_unitario" required
                           value="<?= htmlspecialchars($farmacoDaModificare['Prezzo_Unitario'] ?? '') ?>">
                </div>
                <div style="display:flex;align-items:flex-end;padding-bottom:2px;">
                    <div class="checkbox-row">
                        <input type="checkbox" name="pericolosita" id="pericolosita"
                            <?= !empty($farmacoDaModificare['Pericolosità']) ? 'checked' : '' ?>>
                        <label for="pericolosita" style="margin:0">⚠️ Farmaco pericoloso</label>
                    </div>
                </div>

                <div style="grid-column:1/-1;display:flex;gap:10px;margin-top:8px;">
                    <button type="submit" class="btn btn-primary">
                        <?= $farmacoDaModificare ? 'Salva Modifiche' : 'Aggiungi Farmaco' ?>
                    </button>
                    <?php if ($farmacoDaModificare): ?>
                        <a href="farmaco.php" class="btn btn-secondary">Annulla</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- TABELLA -->
        <div style="background:var(--white);border-radius:var(--radius-md);padding:24px;box-shadow:var(--shadow-sm);">
            <h2>📋 Elenco Farmaci (<?= count($farmaci) ?>)</h2>
            <div class="table-wrapper table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th><th>Nome</th><th>Categoria</th><th>Pericolosità</th><th>Prezzo</th><th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($farmaci as $f): ?>
                        <tr>
                            <td><?= (int)$f['ID_Farmaco'] ?></td>
                            <td><?= htmlspecialchars($f['Nome']) ?></td>
                            <td><?= htmlspecialchars($f['Categoria']) ?></td>
                            <td>
                                <?php if ($f['Pericolosità']): ?>
                                    <span class="badge badge-danger">⚠️ Sì</span>
                                <?php else: ?>
                                    <span class="badge badge-success">No</span>
                                <?php endif; ?>
                            </td>
                            <td>€ <?= number_format((float)$f['Prezzo_Unitario'], 2, ',', '.') ?></td>
                            <td class="actions-cell">
                                <a href="farmaco.php?id_farmaco=<?= (int)$f['ID_Farmaco'] ?>" class="btn btn-sm btn-secondary">✏️ Modifica</a>
                                <form method="post" class="inline"
                                      onsubmit="return confirm('Eliminare il farmaco «<?= htmlspecialchars(addslashes($f['Nome'])) ?>»?')">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="elimina_id" value="<?= (int)$f['ID_Farmaco'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">🗑️ Elimina</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
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
