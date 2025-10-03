<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id_paziente = $_GET['id'];
    // Recupera i dati del paziente da modificare
    $stmt = $pdo->prepare("SELECT * FROM Paziente WHERE ID_Paziente = ?");
    $stmt->execute([$id_paziente]);
    $paziente = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recupera i nuovi dati dal modulo
    $nome = $_POST['nome'];
    $cognome = $_POST['cognome'];
    $data_nascita = $_POST['data_nascita'];
    $sesso = $_POST['sesso'];
    $id_reparto = $_POST['id_reparto'];

    // Esegui l'update nel database
    $stmt = $pdo->prepare("UPDATE Paziente SET Nome = ?, Cognome = ?, Data_Nascita = ?, Sesso = ?, ID_Reparto = ? WHERE ID_Paziente = ?");
    $stmt->execute([$nome, $cognome, $data_nascita, $sesso, $id_reparto, $id_paziente]);

    // Reindirizza alla pagina di gestione pazienti
    header('Location: paziente.php');
    exit;
}

// Recupera i reparti per il menu a discesa
$reparti = $pdo->query("SELECT * FROM Reparto")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Modifica Paziente</title>
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

        /* Stile per il pulsante indietro */
        .back-button {
            position: absolute;
            bottom: 20px;
            left: 20px;
            background-color: #4caf50; /* Colore verde */
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
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Modifica Paziente</h1>
        <form method="POST">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?= $paziente['Nome'] ?>" required>

            <label for="cognome">Cognome:</label>
            <input type="text" id="cognome" name="cognome" value="<?= $paziente['Cognome'] ?>" required>

            <label for="data_nascita">Data di Nascita:</label>
            <input type="date" id="data_nascita" name="data_nascita" value="<?= $paziente['Data_Nascita'] ?>" required>

            <label for="sesso">Sesso:</label>
            <select id="sesso" name="sesso" required>
                <option value="M" <?= $paziente['Sesso'] == 'M' ? 'selected' : '' ?>>Maschio</option>
                <option value="F" <?= $paziente['Sesso'] == 'F' ? 'selected' : '' ?>>Femmina</option>
            </select>

            <label for="id_reparto">Reparto:</label>
            <select id="id_reparto" name="id_reparto" required>
                <?php foreach ($reparti as $reparto): ?>
                    <option value="<?= $reparto['ID_Reparto'] ?>" <?= $reparto['ID_Reparto'] == $paziente['ID_Reparto'] ? 'selected' : '' ?>>
                        <?= $reparto['Nome_Reparto'] ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="submit" value="Salva Modifiche">
        </form>
    </div>

    <!-- Pulsante indietro in basso a sinistra -->
    <a href="paziente.php" class="back-button">Indietro</a>
</body>
</html>
