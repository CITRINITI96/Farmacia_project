<?php
// Connessione al database
include 'db.php';

// Recupera il token dalla URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Verifica se il token esiste e non è scaduto
    $stmt = $pdo->prepare("SELECT * FROM utente WHERE reset_token = ? AND token_expiry > NOW()");
    $stmt->execute([$token]);
    $utente = $stmt->fetch(PDO::FETCH_ASSOC);

    // Se il token è valido
    if ($utente) {
        // Verifica se il form è stato inviato
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $new_password = $_POST['new_password']; // La nuova password

            // Cripta la nuova password
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

            // Aggiorna la password nel database
            $stmt = $pdo->prepare("UPDATE utente SET Password = ?, reset_token = NULL, token_expiry = NULL WHERE reset_token = ?");
            $stmt->execute([$hashed_password, $token]);

            echo '<p style="color: green;">La tua password è stata reimpostata con successo!</p>';
        }
    } else {
        echo '<p style="color: red;">Il token è scaduto o non valido!</p>';
    }
} else {
    echo '<p style="color: red;">Token non fornito!</p>';
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Reimposta la tua Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            text-align: center;
        }
        .form-container {
            width: 300px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        input[type="password"], input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="password"] {
            margin-bottom: 20px;
        }
        input[type="submit"] {
            background-color: #4caf50;
            color: white;
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        h2 {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Reimposta la tua Password</h2>
        <form method="POST" action="">
            <input type="password" name="new_password" placeholder="Nuova password" required>
            <input type="submit" value="Reimposta Password">
        </form>
    </div>
</body>
</html>
