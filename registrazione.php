<?php
// Includi la configurazione del database
require_once 'config.php'; // Assicurati che config.php definisca $conn per la connessione al database

// Inizializza le variabili
$error = '';
$success = '';

// Gestione CSRF Token per la registrazione
session_start();
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Crea un nuovo token CSRF
}

// Controlla se il form di registrazione è stato inviato
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'register') {
    // Verifica che i campi siano definiti e il token CSRF sia valido
    if (!empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['nome']) && !empty($_POST['cognome']) && !empty($_POST['email']) && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $nome = $_POST['nome'];
        $cognome = $_POST['cognome'];
        $email = $_POST['email'];

        // Verifica che il ruolo sia stato selezionato
        if (isset($_POST['Ruolo']) && !empty($_POST['Ruolo'])) {
            $ruolo = $_POST['Ruolo']; // Ruolo scelto dall'utente

            // Verifica che il ruolo selezionato sia valido
            $valid_roles = ['Dottore', 'Dottoressa', 'Magazziniere', 'Farmacista', 'Admin'];
            if (!in_array($ruolo, $valid_roles)) {
                $error = 'Ruolo non valido. Scegli tra Dottore, Dottoressa, Magazziniere,Farmacista o Admin.';
            } else {
                // Hash della password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Prepara la query per inserire i dati dell'utente nel database
                try {
                    $query = "INSERT INTO Utente (Nome, Cognome, Email, Password, Ruolo, username, Data_Creazione) 
                              VALUES (:nome, :cognome, :email, :password, :ruolo, :username, NOW())";
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(':nome', $nome);
                    $stmt->bindParam(':cognome', $cognome);
                    $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':password', $hashedPassword);
                    $stmt->bindParam(':ruolo', $ruolo);
                    $stmt->bindParam(':username', $username);
                    $stmt->execute();

                    // Successo nella registrazione
                    $success = 'Registrazione avvenuta con successo! Puoi ora effettuare il login.';

                    // Reindirizza alla pagina di login
                    header("Location: login.php");
                    exit;  // Termina l'esecuzione dello script
                } catch (PDOException $e) {
                    $error = 'Errore nella registrazione: ' . $e->getMessage();
                }
            }
        } else {
            $error = 'Seleziona un ruolo valido.';
        }
    } else {
        $error = 'Per favore, compila tutti i campi.';
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione - Farmacia Ospedaliera</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(to bottom, #4caf50 50%, #ffffff 50%);
        }
        .registration-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 300px;
        }
        .registration-container h1 {
            font-size: 24px;
            color: #4caf50;
            margin-bottom: 20px;
        }
        .registration-container input[type="text"],
        .registration-container input[type="password"],
        .registration-container input[type="email"],
        .registration-container select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .registration-container button {
            background-color: #4caf50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        .registration-container button:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }
        .success {
            color: green;
            font-size: 14px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="registration-container">
        <h1>Registrazione</h1>
        <form action="registrazione.php" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="text" name="nome" placeholder="Nome" required>
            <input type="text" name="cognome" placeholder="Cognome" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="Ruolo" required>
                <option value="">Seleziona Ruolo</option>
                <option value="Admin">Admin</option>
                <option value="Dottore">Dottore</option>
                <option value="Dottoressa">Dottoressa</option>
                <option value="Farmacista">Farmacista</option>
                <option value="Magazziniere">Magazziniere</option>
            </select>
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="action" value="register">
            <button type="submit">Registrati</button>
        </form>

        <?php if (!empty($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <p class="success"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
