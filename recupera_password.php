<?php
// Connessione al database
include 'db.php';

// Verifica se è stato inviato il form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email']; // Prendi l'email dal form

    // Recupera la password dalla tabella "utente" in base all'email
    $stmt = $pdo->prepare("SELECT * FROM utente WHERE Email = ?");
    $stmt->execute([$email]);
    $utente = $stmt->fetch(PDO::FETCH_ASSOC);

    // Se l'utente esiste
    if ($utente) {
        // Includi PHPMailer
        require 'vendor/autoload.php';

        // Configurazione dei server SMTP
        $smtp_servers = [
            'gmail' => [
                'host' => 'smtp.gmail.com',
                'username' => 'salvatorecitriniti96@gmail.com',  // Sostituisci con la tua email
                'password' => '30092013m',   // Sostituisci con la tua password (o usa una password per app se hai l'autenticazione 2FA attiva)
                'secure' => PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS,
                'port' => 587
            ]
        ];

        // Scegli il server SMTP (in questo caso Gmail)
        $smtp_provider = 'gmail'; // Puoi cambiarlo a 'outlook' o 'sendgrid' se necessario

        // Recupera le impostazioni del server SMTP scelto
        $smtp_config = $smtp_servers[$smtp_provider];

        // Configura PHPMailer
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->isSMTP();
        $mail->Host = $smtp_config['host'];  // SMTP del provider scelto
        $mail->SMTPAuth = true;
        $mail->Username = $smtp_config['username'];  // Il tuo indirizzo email
        $mail->Password = $smtp_config['password'];  // La tua password o la tua API key
        $mail->SMTPSecure = $smtp_config['secure'];
        $mail->Port = $smtp_config['port'];

        // Aumenta il livello di debug per vedere i dettagli della connessione
        $mail->SMTPDebug = 2; // Debug completo, utile per diagnosticare problemi

        // Impostazioni dell'email
        $mail->setFrom($smtp_config['username'], 'Farmacia Ospedaliera');
        $mail->addAddress($email);  // Email del destinatario
        $mail->Subject = 'Recupero Password';
        $mail->Body = 'La tua password è: ' . $utente['Password'];

        // Invia l'email
        if ($mail->send()) {
            echo '<p style="color: green;">Un\'email è stata inviata con la tua password!</p>';
        } else {
            echo '<p style="color: red;">Errore nell\'invio dell\'email: ' . $mail->ErrorInfo . '</p>';
        }
    } else {
        echo '<p style="color: red;">Email non trovata nel database!</p>';
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Recupero Password</title>
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
        input[type="email"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #4caf50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Recupera la tua Password</h2>
        <form method="POST" action="">
            <input type="email" name="email" placeholder="Inserisci la tua email" required>
            <input type="submit" value="Recupera Password">
        </form>
    </div>
</body>
</html>
