<?php
require_once 'db.php';
require_once 'auth.php';
requireAuth('Admin');

csrfToken();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: gestione_utenti.php');
    exit;
}

// Recupera utente
$stmt = $pdo->prepare("SELECT * FROM Utente WHERE ID_Utente = ?");
$stmt->execute([$id]);
$utente = $stmt->fetch();
if (!$utente) {
    header('Location: gestione_utenti.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $username = trim($_POST['username'] ?? '');
    $nome     = trim($_POST['nome'] ?? '');
    $cognome  = trim($_POST['cognome'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $ruolo    = $_POST['ruolo'] ?? '';

    $valid_roles = ['Admin', 'Dottore', 'Dottoressa', 'Magazziniere', 'Farmacista'];

    if (empty($username) || empty($nome) || empty($cognome) || empty($ruolo)) {
        $error = 'Compila tutti i campi obbligatori.';
    } elseif (!in_array($ruolo, $valid_roles, true)) {
        $error = 'Ruolo non valido.';
    } else {
        try {
            $stmt2 = $pdo->prepare(
                "UPDATE Utente SET username=:username, Nome=:nome, Cognome=:cognome, Email=:email, Ruolo=:ruolo WHERE ID_Utente=:id"
            );
            $stmt2->execute([
                ':username' => $username,
                ':nome'     => $nome,
                ':cognome'  => $cognome,
                ':email'    => $email,
                ':ruolo'    => $ruolo,
                ':id'       => $id,
            ]);
            header('Location: gestione_utenti.php?msg=modificato');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = 'Username o email già in uso.';
            } else {
                error_log('[MODIFICA_UTENTE] ' . $e->getMessage());
                $error = 'Errore nel salvataggio dei dati.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Utente — PharmaCare</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="layout">
    <?php include 'sidebar_admin.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <h1>✏️ Modifica Utente</h1>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div style="background:var(--white);border-radius:var(--radius-md);padding:24px;box-shadow:var(--shadow-sm);max-width:560px;">
            <form method="POST">
                <?= csrfField() ?>

                <label for="username">Username *</label>
                <input type="text" id="username" name="username" required
                       value="<?= htmlspecialchars($_POST['username'] ?? $utente['username']) ?>">

                <label for="nome">Nome *</label>
                <input type="text" id="nome" name="nome" required
                       value="<?= htmlspecialchars($_POST['nome'] ?? $utente['Nome']) ?>">

                <label for="cognome">Cognome *</label>
                <input type="text" id="cognome" name="cognome" required
                       value="<?= htmlspecialchars($_POST['cognome'] ?? $utente['Cognome']) ?>">

                <label for="email">Email</label>
                <input type="email" id="email" name="email"
                       value="<?= htmlspecialchars($_POST['email'] ?? ($utente['Email'] ?? '')) ?>">

                <label for="ruolo">Ruolo *</label>
                <select id="ruolo" name="ruolo" required>
                    <?php foreach (['Admin','Dottore','Dottoressa','Farmacista','Magazziniere'] as $r): ?>
                        <option value="<?= $r ?>" <?= ($utente['Ruolo'] === $r) ? 'selected' : '' ?>><?= $r ?></option>
                    <?php endforeach; ?>
                </select>

                <div style="display:flex;gap:12px;margin-top:20px;">
                    <button type="submit" class="btn btn-primary">💾 Salva Modifiche</button>
                    <a href="gestione_utenti.php" class="btn btn-secondary">Annulla</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
