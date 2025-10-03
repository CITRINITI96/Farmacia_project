<?php
// Includi la configurazione del database
require_once 'config.php';
session_start();

// Controlla se l'utente è autenticato e se è un admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit;
}

// Verifica se è stato passato un ID tramite GET
if (!isset($_GET['id'])) {
    header("Location: gestione_utenti.php");
    exit;
}

$userId = $_GET['id']; // Questo è l'ID_Utente

// Recupera i dati dell'utente da modificare
$query = "SELECT * FROM Utente WHERE ID_Utente = :id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $userId, PDO::PARAM_INT);
$stmt->execute();
$utente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$utente) {
    header("Location: gestione_utenti.php");
    exit;
}

// Gestione della modifica dell'utente
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $nome = $_POST['nome'];
    $cognome = $_POST['cognome'];
    $ruolo = $_POST['ruolo'];

    $query = "UPDATE Utente SET username = :username, Nome = :nome, Cognome = :cognome, Ruolo = :ruolo WHERE ID_Utente = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':cognome', $cognome);
    $stmt->bindParam(':ruolo', $ruolo);
    $stmt->bindParam(':id', $userId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        header("Location: gestione_utenti.php");
        exit;
    } else {
        $error = "Errore nel salvataggio dei dati.";
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Utente</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom, #4caf50 50%, #ffffff 50%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            text-align: center;
            width: 300px;
        }
        .container h1 {
            font-size: 24px;
            color: #4caf50;
            margin-bottom: 20px;
        }
        .container label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        .container input[type="text"],
        .container select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Modifica Utente</h1>
        <?php if (isset($error)) { echo "<p class='error'>" . htmlspecialchars($error) . "</p>"; } ?>
        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($utente['username']); ?>" required>
            
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($utente['Nome']); ?>" required>
            
            <label for="cognome">Cognome:</label>
            <input type="text" id="cognome" name="cognome" value="<?php echo htmlspecialchars($utente['Cognome']); ?>" required>
            
            <label for="ruolo">Ruolo:</label>
            <select id="ruolo" name="ruolo" required>
                <option value="Admin" <?php echo ($utente['Ruolo'] === 'Admin') ? 'selected' : ''; ?>>Admin</option>
                <option value="Dottore" <?php echo ($utente['Ruolo'] === 'Dottore') ? 'selected' : ''; ?>>Dottore</option>
                <option value="Dottoressa" <?php echo ($utente['Ruolo'] === 'Dottoressa') ? 'selected' : ''; ?>>Dottoressa</option>
                <option value="Magazziniere" <?php echo ($utente['Ruolo'] === 'Magazziniere') ? 'selected' : ''; ?>>Magazziniere</option>
                <option value="Farmacista" <?php echo ($utente['Ruolo'] === 'Farmacista') ? 'selected' : ''; ?>>Farmacista</option>
            </select>
            
            <button type="submit">Salva Modifiche</button>
        </form>
    </div>
</body>
</html>
