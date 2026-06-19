<?php
require_once 'db.php';
require_once 'auth.php';

session_start();

// Se già loggato reindirizza
if (isset($_SESSION['user_id'])) {
    header('Location: ' . ($_SESSION['role'] === 'Admin' ? 'dashboard_Admin.php' : 'menu.php'));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $stmt = $pdo->prepare("SELECT username, password, Ruolo FROM Utente WHERE username = :username LIMIT 1");
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            $user = $stmt->fetch();

            if (password_verify($password, $user['password'])) {
                // Prevenire session fixation
                session_regenerate_id(true);

                $_SESSION['user_id']   = $user['username'];
                $_SESSION['role']      = $user['Ruolo'];
                $_SESSION['last_regen'] = time();

                header('Location: ' . ($user['Ruolo'] === 'Admin' ? 'dashboard_Admin.php' : 'menu.php'));
                exit;
            }
        }
        // Messaggio generico per non rivelare se lo username esiste
        $error = 'Credenziali non valide. Riprova.';
    } else {
        $error = 'Inserisci username e password.';
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — PharmaCare</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .login-brand {
            font-size: 2.8rem;
            font-weight: 800;
            text-align: center;
            color: rgba(255,255,255,.9);
            letter-spacing: -1px;
            margin-bottom: 28px;
            text-shadow: 0 2px 8px rgba(0,0,0,.2);
        }
        .login-brand span { color: #a5d6a7; }
    </style>
</head>
<body>
<div class="page-center" style="flex-direction:column;">
    <div class="login-brand">💊 Pharma<span>Care</span></div>

    <div class="card">
        <div class="card-header">
            <h1 style="font-size:1.4rem;color:var(--green-700);">Accedi al Gestionale</h1>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="" method="POST" autocomplete="off" novalidate>
            <input type="hidden" name="action" value="login">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Il tuo username" required autofocus>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="••••••••" required>

            <button type="submit" class="btn btn-primary btn-block" style="margin-top:20px;">Accedi</button>
        </form>

        <p class="text-center mt-2" style="font-size:.88rem;color:var(--gray-700);">
            Non hai un account? <a href="registrazione.php">Registrati</a>
        </p>
        <p class="text-center mt-1" style="font-size:.85rem;">
            <a href="recupera_password.php">Password dimenticata?</a>
        </p>
    </div>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
