<?php
include 'db.php';  // Connessione al database

$successo = '';
$errore = '';

// Verifica se il token è stato passato tramite GET
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Verifica se il token è valido
    $stmt = $pdo->prepare("SELECT * FROM Utenti WHERE Token = ?");
    $stmt->execute([$token]);
    $utente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($utente) {
        // Gestione del modulo di cambio password
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nuova_password = password_hash($_POST['password'], PASSWORD_DEFAULT);  // Cripta la nuova password

            // Aggiorna la password nel database
            $stmt = $pdo->prepare("UPDATE Utenti SET Password = ?, Token = NULL WHERE Token = ?");
            $stmt->execute([$nuova_password, $token]);

            $successo = 'La tua password è stata aggiornata con successo.';
        }
    } else {
        $errore = 'Token non valido o scaduto.';
    }
} else {
    $errore = 'Token non trovato.';
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Cambia Password</title>
    <style>
        /* Stili simili alla pagina precedente */
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Cambia la tua password</h1>

        <?php if ($successo): ?>
            <div class="message success"><?= $successo ?></div>
        <?php endif; ?>

        <?php if ($errore): ?>
            <div class="message error"><?= $errore ?></div>
        <?php endif; ?>

        <form method="POST">
            <label for="password">Nuova password:</label>
            <input type="password" id="password" name="password" required>

            <input type="submit" value="Cambia Password">
        </form>
    </div>
</body>
</html>
