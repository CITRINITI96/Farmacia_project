<?php
require_once 'db.php';
require_once 'auth.php';

session_start();

$error   = '';
$success = '';

// Genera token CSRF
csrfToken();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    // Verifica CSRF
    verifyCsrf();

    $username          = trim($_POST['username'] ?? '');
    $password          = $_POST['password'] ?? '';
    $nome              = trim($_POST['nome'] ?? '');
    $cognome           = trim($_POST['cognome'] ?? '');
    $email             = trim($_POST['email'] ?? '');
    $ruolo             = $_POST['Ruolo'] ?? '';
    $domanda_segreta   = trim($_POST['domanda_segreta'] ?? '');
    $risposta_segreta  = trim($_POST['risposta_segreta'] ?? '');

    // Ruoli selezionabili dalla registrazione pubblica (NO Admin)
    $valid_roles = ['Dottore', 'Dottoressa', 'Magazziniere', 'Farmacista'];

    if (empty($username) || empty($password) || empty($nome) || empty($cognome) || empty($email) || empty($ruolo) || empty($domanda_segreta) || empty($risposta_segreta)) {
        $error = 'Compila tutti i campi obbligatori, inclusa la domanda segreta.';
    } elseif (!in_array($ruolo, $valid_roles, true)) {
        $error = 'Ruolo non valido.';
    } elseif (strlen($password) < 8) {
        $error = 'La password deve essere di almeno 8 caratteri.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Indirizzo email non valido.';
    } else {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare(
                "INSERT INTO Utente (Nome, Cognome, Email, Password, Ruolo, username, Data_Creazione, domanda_segreta, risposta_segreta)
                 VALUES (:nome, :cognome, :email, :password, :ruolo, :username, NOW(), :domanda, :risposta)"
            );
            $stmt->execute([
                ':nome'     => $nome,
                ':cognome'  => $cognome,
                ':email'    => $email,
                ':password' => $hashedPassword,
                ':ruolo'    => $ruolo,
                ':username' => $username,
                ':domanda'  => $domanda_segreta,
                ':risposta' => $risposta_segreta,
            ]);

            header('Location: login.php?registered=1');
            exit;
        } catch (PDOException $e) {
            // Verifica username o email già esistenti
            if ($e->getCode() == 23000) {
                $error = 'Username o email già in uso. Scegli credenziali diverse.';
            } else {
                error_log('[REGISTRAZIONE] ' . $e->getMessage());
                $error = 'Errore durante la registrazione. Riprova più tardi.';
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
    <title>Registrazione — PharmaCare</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="page-center" style="flex-direction:column;">
    <div class="card" style="max-width:460px;">
        <div class="card-header">
            <div class="brand">💊 PharmaCare</div>
            <h1>Crea il tuo account</h1>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="registrazione.php" method="POST" novalidate>
            <input type="hidden" name="action" value="register">
            <?= csrfField() ?>

            <label for="username">Username *</label>
            <input type="text" id="username" name="username" required maxlength="50"
                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">

            <label for="nome">Nome *</label>
            <input type="text" id="nome" name="nome" required
                   value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>">

            <label for="cognome">Cognome *</label>
            <input type="text" id="cognome" name="cognome" required
                   value="<?= htmlspecialchars($_POST['cognome'] ?? '') ?>">

            <label for="email">Email *</label>
            <input type="email" id="email" name="email" required
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

            <label for="password">Password * <small style="color:var(--gray-700)">(min. 8 caratteri)</small></label>
            <input type="password" id="password" name="password" required minlength="8">

            <label for="Ruolo">Ruolo *</label>
            <select id="Ruolo" name="Ruolo" required>
                <option value="">— Seleziona —</option>
                <option value="Dottore"     <?= ($_POST['Ruolo'] ?? '') === 'Dottore'     ? 'selected' : '' ?>>Dottore</option>
                <option value="Dottoressa"  <?= ($_POST['Ruolo'] ?? '') === 'Dottoressa'  ? 'selected' : '' ?>>Dottoressa</option>
                <option value="Farmacista"  <?= ($_POST['Ruolo'] ?? '') === 'Farmacista'  ? 'selected' : '' ?>>Farmacista</option>
                <option value="Magazziniere"<?= ($_POST['Ruolo'] ?? '') === 'Magazziniere'? 'selected' : '' ?>>Magazziniere</option>
            </select>

            <hr style="border:none;border-top:1px solid var(--gray-300);margin:20px 0 8px;">
            <p style="font-size:.82rem;color:var(--gray-700);margin-bottom:4px;">🔒 <strong>Domanda segreta</strong> — usata per recuperare la password</p>

            <label for="domanda_segreta">Domanda segreta *</label>
            <select id="domanda_segreta" name="domanda_segreta" required>
                <option value="">— Scegli una domanda —</option>
                <option value="Qual è il nome del tuo primo animale domestico?"     <?= ($_POST['domanda_segreta'] ?? '') === "Qual è il nome del tuo primo animale domestico?"     ? 'selected' : '' ?>>Qual è il nome del tuo primo animale domestico?</option>
                <option value="Qual è il nome della tua città natale?"              <?= ($_POST['domanda_segreta'] ?? '') === "Qual è il nome della tua città natale?"              ? 'selected' : '' ?>>Qual è il nome della tua città natale?</option>
                <option value="Qual è il nome da nubile di tua madre?"              <?= ($_POST['domanda_segreta'] ?? '') === "Qual è il nome da nubile di tua madre?"              ? 'selected' : '' ?>>Qual è il nome da nubile di tua madre?</option>
                <option value="Qual era il nome della tua scuola elementare?"       <?= ($_POST['domanda_segreta'] ?? '') === "Qual era il nome della tua scuola elementare?"       ? 'selected' : '' ?>>Qual era il nome della tua scuola elementare?</option>
                <option value="Qual è il nome del tuo migliore amico d'infanzia?"  <?= ($_POST['domanda_segreta'] ?? '') === "Qual è il nome del tuo migliore amico d'infanzia?"  ? 'selected' : '' ?>>Qual è il nome del tuo migliore amico d'infanzia?</option>
                <option value="Qual è il tuo piatto preferito?"                     <?= ($_POST['domanda_segreta'] ?? '') === "Qual è il tuo piatto preferito?"                     ? 'selected' : '' ?>>Qual è il tuo piatto preferito?</option>
            </select>

            <label for="risposta_segreta">Risposta *</label>
            <input type="text" id="risposta_segreta" name="risposta_segreta" required
                   placeholder="La tua risposta"
                   value="<?= htmlspecialchars($_POST['risposta_segreta'] ?? '') ?>"
                   autocomplete="off">

            <button type="submit" class="btn btn-primary btn-block" style="margin-top:20px;">Registrati</button>
        </form>

        <p class="text-center mt-2" style="font-size:.88rem;color:var(--gray-700);">
            Hai già un account? <a href="login.php">Accedi</a>
        </p>
    </div>
</div>
</body>
</html>
