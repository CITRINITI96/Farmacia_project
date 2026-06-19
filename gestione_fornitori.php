<?php
require_once 'db.php';
require_once 'auth.php';
requireAuth(['Admin', 'Farmacista']);

csrfToken();

$message = '';
$msgType = 'success';

// Aggiungi fornitore
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_supplier'])) {
    verifyCsrf();
    $nome      = trim($_POST['nome'] ?? '');
    $indirizzo = trim($_POST['indirizzo'] ?? '');
    $telefono  = trim($_POST['telefono'] ?? '');
    $email     = trim($_POST['email'] ?? '');

    if ($nome && $indirizzo && $telefono && $email) {
        $stmt = $pdo->prepare("INSERT INTO Fornitori (Nome, Indirizzo, Telefono, Email) VALUES (?,?,?,?)");
        $stmt->execute([$nome, $indirizzo, $telefono, $email]);
        header('Location: gestione_fornitori.php?msg=aggiunto');
        exit;
    } else {
        $message = 'Tutti i campi sono obbligatori.';
        $msgType = 'error';
    }
}

// Modifica fornitore
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_supplier'])) {
    verifyCsrf();
    $id        = (int)$_POST['id'];
    $nome      = trim($_POST['nome'] ?? '');
    $indirizzo = trim($_POST['indirizzo'] ?? '');
    $telefono  = trim($_POST['telefono'] ?? '');
    $email     = trim($_POST['email'] ?? '');

    if ($nome && $indirizzo && $telefono && $email) {
        $stmt = $pdo->prepare("UPDATE Fornitori SET Nome=?, Indirizzo=?, Telefono=?, Email=? WHERE ID_Fornitore=?");
        $stmt->execute([$nome, $indirizzo, $telefono, $email, $id]);
        header('Location: gestione_fornitori.php?msg=modificato');
        exit;
    } else {
        $message = 'Tutti i campi sono obbligatori per la modifica.';
        $msgType = 'error';
    }
}

// Elimina fornitore
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_supplier'])) {
    verifyCsrf();
    $id = (int)$_POST['id'];
    $stmt = $pdo->prepare("DELETE FROM Fornitori WHERE ID_Fornitore=?");
    $stmt->execute([$id]);
    header('Location: gestione_fornitori.php?msg=eliminato');
    exit;
}

// Flash message
if (isset($_GET['msg'])) {
    $msgs = ['aggiunto' => 'Fornitore aggiunto con successo.', 'modificato' => 'Fornitore modificato.', 'eliminato' => 'Fornitore eliminato.'];
    $message = $msgs[$_GET['msg']] ?? '';
}

$fornitori = $pdo->query("SELECT * FROM Fornitori ORDER BY Nome")->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Fornitori — PharmaCare</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .edit-form { display:flex; gap:6px; align-items:center; flex-wrap:wrap; margin-top:4px; }
        .edit-form input { flex:1; min-width:90px; padding:5px 8px; font-size:.82rem; }
    </style>
</head>
<body>
<div class="layout">
    <?php if ($_SESSION['role'] === 'Admin') include 'sidebar_admin.php'; ?>

    <div class="main-content" style="<?= $_SESSION['role'] !== 'Admin' ? 'margin-left:0' : '' ?>">
        <div class="page-header">
            <h1>🚚 Gestione Fornitori</h1>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?= $msgType === 'error' ? 'error' : 'success' ?>">
                <?= $msgType === 'error' ? '⚠️' : '✅' ?> <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- FORM AGGIUNGI -->
        <div style="background:var(--white);border-radius:var(--radius-md);padding:24px;box-shadow:var(--shadow-sm);margin-bottom:24px;">
            <h2>➕ Aggiungi Fornitore</h2>
            <form method="POST" style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr auto;gap:12px;align-items:end;">
                <?= csrfField() ?>
                <div><label>Nome *</label><input type="text" name="nome" required placeholder="Nome"></div>
                <div><label>Indirizzo *</label><input type="text" name="indirizzo" required placeholder="Indirizzo"></div>
                <div><label>Telefono *</label><input type="text" name="telefono" required placeholder="Telefono"></div>
                <div><label>Email *</label><input type="email" name="email" required placeholder="Email"></div>
                <div><button type="submit" name="add_supplier" class="btn btn-primary">Aggiungi</button></div>
            </form>
        </div>

        <!-- TABELLA -->
        <div style="background:var(--white);border-radius:var(--radius-md);padding:24px;box-shadow:var(--shadow-sm);">
            <h2>📋 Elenco Fornitori (<?= count($fornitori) ?>)</h2>
            <div class="table-wrapper table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th><th>Nome</th><th>Indirizzo</th><th>Telefono</th><th>Email</th><th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($fornitori as $f): ?>
                        <tr>
                            <td><?= (int)$f['ID_Fornitore'] ?></td>
                            <td><?= htmlspecialchars($f['Nome']) ?></td>
                            <td><?= htmlspecialchars($f['Indirizzo']) ?></td>
                            <td><?= htmlspecialchars($f['Telefono']) ?></td>
                            <td><?= htmlspecialchars($f['Email']) ?></td>
                            <td>
                                <!-- Modifica -->
                                <form method="POST" class="edit-form">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="id" value="<?= (int)$f['ID_Fornitore'] ?>">
                                    <input type="text" name="nome" value="<?= htmlspecialchars($f['Nome']) ?>" required>
                                    <input type="text" name="indirizzo" value="<?= htmlspecialchars($f['Indirizzo']) ?>" required>
                                    <input type="text" name="telefono" value="<?= htmlspecialchars($f['Telefono']) ?>" required>
                                    <input type="email" name="email" value="<?= htmlspecialchars($f['Email']) ?>" required>
                                    <button type="submit" name="edit_supplier" class="btn btn-sm btn-secondary">✏️ Salva</button>
                                </form>
                                <!-- Elimina -->
                                <form method="POST" class="inline" style="margin-top:4px;"
                                      onsubmit="return confirm('Eliminare il fornitore «<?= htmlspecialchars(addslashes($f['Nome'])) ?>»?')">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="id" value="<?= (int)$f['ID_Fornitore'] ?>">
                                    <button type="submit" name="delete_supplier" class="btn btn-sm btn-danger">🗑️ Elimina</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($fornitori)): ?>
                        <tr><td colspan="6" class="text-center" style="color:var(--gray-700);padding:20px">Nessun fornitore.</td></tr>
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
