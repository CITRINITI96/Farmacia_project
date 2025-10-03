<?php
include 'db.php';

// Verifica se l'ID del paziente, farmaco e la data della prescrizione sono passati tramite URL
if (isset($_GET['id_paziente'], $_GET['id_farmaco'], $_GET['data_prescrizione'])) {
    $id_paziente = $_GET['id_paziente'];
    $id_farmaco = $_GET['id_farmaco'];
    $data_prescrizione = $_GET['data_prescrizione'];
} else {
    echo "Errore: ID paziente, ID farmaco o data prescrizione non specificati.";
    exit;
}

// Recupera la prescrizione esistente
$stmt = $pdo->prepare("SELECT * FROM Prescrizione WHERE ID_Paziente = ? AND ID_Farmaco = ? AND Data_Prescrizione = ?");
$stmt->execute([$id_paziente, $id_farmaco, $data_prescrizione]);
$prescrizione = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Preleva i dati del form
    $id_farmaco = $_POST['id_farmaco'];
    $quantita = $_POST['quantità'];

    // Modifica la prescrizione nel database
    $stmt = $pdo->prepare("UPDATE Prescrizione SET ID_Farmaco = ?, Quantità = ? WHERE ID_Paziente = ? AND ID_Farmaco = ? AND Data_Prescrizione = ?");
    $stmt->execute([$id_farmaco, $quantita, $id_paziente, $id_farmaco, $data_prescrizione]);

    // Reindirizza alla pagina di gestione prescrizioni
    header("Location: gestione_prescrizione.php?id_paziente=" . $id_paziente);
    exit;
}

// Recupera i farmaci disponibili
$farmaci = $pdo->query("SELECT * FROM Farmaco")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Modifica Prescrizione</title>
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
        .form-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 80%;
            max-width: 600px;
            text-align: center;
        }
        .form-container h1 {
            font-size: 24px;
            color: #4caf50;
            margin-bottom: 20px;
        }
        .form-container label {
            font-size: 18px;
            color: #333;
            margin-bottom: 10px;
        }
        .form-container select, .form-container input {
            width: 100%;
            padding: 8px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .form-container button {
            background-color: #4caf50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .form-container button:hover {
            background-color: #45a049;
        }
        /* Stile per il pulsante indietro */
        .back-button {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background-color: #4caf50;  /* Colore verde */
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
        }
        .back-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Modifica Prescrizione</h1>

        <form action="" method="POST">
            <label for="id_farmaco">Farmaco:</label>
            <select name="id_farmaco" id="id_farmaco" required>
                <?php foreach ($farmaci as $farmaco): ?>
                    <option value="<?= $farmaco['ID_Farmaco'] ?>" <?= $prescrizione['ID_Farmaco'] == $farmaco['ID_Farmaco'] ? 'selected' : '' ?>>
                        <?= $farmaco['Nome'] ?>
                    </option>
                <?php endforeach; ?>
            </select><br>

            <label for="quantità">Quantità:</label>
            <input type="number" name="quantità" id="quantità" value="<?= $prescrizione['Quantità'] ?>" required>

            <button type="submit">Modifica Prescrizione</button>
        </form>
    </div>

    <!-- Pulsante Indietro -->
    <a href="javascript:history.back()" class="back-button">Indietro</a>
</body>
</html>
