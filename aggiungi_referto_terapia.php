<?php
include 'db.php';  // Connessione al database

$successo = '';  // Variabile per messaggio di successo
$errore = '';    // Variabile per messaggio di errore

// Recupera i dottori dal database (nome e cognome concatenati)
$stmt_dottori = $pdo->query("SELECT ID_Dottore, CONCAT(Nome, ' ', Cognome) AS Nome_Completo FROM Dottore");
$dottori = $stmt_dottori->fetchAll(PDO::FETCH_ASSOC);

// Recupera i farmaci dal database
$stmt_farmaci = $pdo->query("SELECT ID_Farmaco, Nome FROM Farmaco");
$farmaci = $stmt_farmaci->fetchAll(PDO::FETCH_ASSOC);

// Recupera i pazienti dal database
$stmt_pazienti = $pdo->query("SELECT ID_Paziente, CONCAT(Nome, ' ', Cognome) AS Paziente_Nome FROM Paziente");
$pazienti = $stmt_pazienti->fetchAll(PDO::FETCH_ASSOC);

// Verifica se il modulo è stato inviato
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recupera i dati inviati dal modulo
    $id_paziente = $_POST['id_paziente'];
    $id_farmaco = $_POST['id_farmaco'];
    $id_dottore = $_POST['id_dottore'];  // Recupera l'ID del dottore selezionato
    $referto = $_POST['referto'];
    $terapia = $_POST['terapia'];
    $data_assegnazione = $_POST['data_assegnazione'];

    // Esegui l'inserimento nel database
    try {
        $stmt = $pdo->prepare("INSERT INTO RefertiTerapie (ID_Paziente, ID_Farmaco, ID_Dottore, Referto, Terapia, Data_Assegnazione) 
                                VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$id_paziente, $id_farmaco, $id_dottore, $referto, $terapia, $data_assegnazione]);

        $successo = 'Referto e terapia aggiunti con successo!';
    } catch (Exception $e) {
        $errore = 'Errore durante l\'inserimento dei dati: ' . $e->getMessage();
    }

    // Dopo l'inserimento, reindirizza alla pagina di gestione referti
    if (!$errore) {
        header('Location: gestione_referti.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Aggiungi Referto Terapia</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom, #4caf50 50%, #ffffff 50%);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 400px;
            text-align: center;
        }
        .form-container h1 {
            font-size: 24px;
            color: #4caf50;
            margin-bottom: 20px;
        }
        .form-container label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        .form-container input, .form-container select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .form-container input[type="submit"] {
            background-color: #4caf50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .form-container input[type="submit"]:hover {
            background-color: #45a049;
        }

        .back-button {
            position: absolute;
            bottom: 20px;
            left: 20px;
            background-color: #4caf50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .back-button:hover {
            background-color: #45a049;
        }

        /* Messaggi di successo ed errore */
        .message {
            margin-bottom: 20px;
            padding: 10px;
            color: white;
            border-radius: 5px;
        }

        .success {
            background-color: #4caf50;
        }

        .error {
            background-color: #e74c3c;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Aggiungi Referto Terapia</h1>

        <?php if ($successo): ?>
            <div class="message success"><?= $successo ?></div>
        <?php endif; ?>

        <?php if ($errore): ?>
            <div class="message error"><?= $errore ?></div>
        <?php endif; ?>

        <form method="POST">
            <label for="id_paziente">Paziente:</label>
            <select id="id_paziente" name="id_paziente" required>
                <?php foreach ($pazienti as $paziente): ?>
                    <option value="<?= $paziente['ID_Paziente'] ?>"><?= $paziente['Paziente_Nome'] ?></option>
                <?php endforeach; ?>
            </select>

            <label for="id_farmaco">Farmaco:</label>
            <select id="id_farmaco" name="id_farmaco" required>
                <?php foreach ($farmaci as $farmaco): ?>
                    <option value="<?= $farmaco['ID_Farmaco'] ?>"><?= $farmaco['Nome'] ?></option>
                <?php endforeach; ?>
            </select>

            <label for="id_dottore">Dottore:</label>
            <select id="id_dottore" name="id_dottore" required>
                <?php foreach ($dottori as $dottore): ?>
                    <option value="<?= $dottore['ID_Dottore'] ?>"><?= $dottore['Nome_Completo'] ?></option>
                <?php endforeach; ?>
            </select>

            <label for="referto">Referto:</label>
            <input type="text" id="referto" name="referto" required>

            <label for="terapia">Terapia:</label>
            <input type="text" id="terapia" name="terapia" required>

            <label for="data_assegnazione">Data Assegnazione:</label>
            <input type="date" id="data_assegnazione" name="data_assegnazione" required>

            <input type="submit" value="Aggiungi Referto Terapia">
        </form>
    </div>

    <a href="gestione_referti.php" class="back-button">Indietro</a>
</body>
</html>
