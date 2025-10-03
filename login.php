<?php
// Includi la configurazione del database
require_once 'config.php'; // Assicurati che config.php definisca $conn per la connessione al database

// Inizializza le variabili
$error = '';

// Gestione CSRF Token per la registrazione
session_start();

// Controlla se il form di login è stato inviato
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'login') {
    // Verifica che i campi siano definiti
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Prepara la query per ottenere l'utente dal database
        $query = "SELECT username, password, Ruolo FROM Utente WHERE username = :username";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        // Verifica se l'utente esiste
        if ($stmt->rowCount() === 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $hashedPasswordFromDb = $user['password'];

            // Confronta la password inserita con quella salvata nel database
            if (password_verify($password, $hashedPasswordFromDb)) {
                // Imposta i dati dell'utente nella sessione
                $_SESSION['user_id'] = $user['username'];
                $_SESSION['role'] = $user['Ruolo'];

                // Verifica se il ruolo è "admin"
                if ($_SESSION['role'] == 'Admin') {
                    // Se il ruolo è admin, reindirizza alla dashboard admin
                    header("Location: dashboard_Admin.php");
                } else {
                    // Se il ruolo è diverso, reindirizza alla pagina generale
                    header("Location: menu.php");
                }
                exit;
            } else {
                $error = 'Credenziali non valide. Riprova.';
            }
        } else {
            $error = 'Credenziali non valide. Riprova.';
        }
    } else {
        $error = 'Per favore, inserisci username e password.';
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PharmaCare</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(to bottom, #4caf50 50%, #ffffff 50%);
            position: relative;
            overflow: hidden;
            flex-direction: column;
        }

        /* Testo "PharmaCare" ancora più grande e più in alto */
        .background-text {
            position: absolute;
            top: 20%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 70px;
            font-weight: bold;
            color: rgba(0, 0, 0, 0.3); /* Colore più scuro */
            z-index: 0;
            white-space: nowrap;
        }

        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 300px;
            position: relative;
            z-index: 1;
            margin-top: 50px; /* Aggiunto spazio per il testo sopra */
        }

        .container h1 {
            font-size: 24px;
            color: #4caf50;
            margin-bottom: 20px;
        }

        .container input[type="text"],
        .container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .container button {
            background-color: #4caf50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        .container button:hover {
            background-color: #45a049;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }

        .container p {
            margin-top: 15px;
            font-size: 14px;
        }

        .container a {
            color: #4caf50;
            text-decoration: none;
        }

        .container a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <!-- Testo PharmaCare ancora più in alto e più grande -->
    <div class="background-text">PharmaCare</div>

    <div class="container">
        <h1>Gestionale Farmacia</h1>
        
        <!-- Sezione Login -->
        <h2>Login</h2>
        <form action="" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="hidden" name="action" value="login">
            <button type="submit">Accedi</button>
        </form>

        <?php if (!empty($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <p>Non hai un account? <a href="registrazione.php">Registrati</a></p>
        <a href="recupera_password.php">Hai dimenticato la password?</a>

    </div>
<?php include 'footer.php'; ?>

</body>
</html>
