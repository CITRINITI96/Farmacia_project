<?php
include 'db.php';  // Connessione al database

// Verifica se l'ID del referto è stato passato tramite GET
if (isset($_GET['id_referto'])) {
    $id_referto = $_GET['id_referto'];

    // Recupera i dettagli del referto
    $stmt = $pdo->prepare("SELECT * FROM RefertiTerapie WHERE ID_Referto = ?");
    $stmt->execute([$id_referto]);
    $referto = $stmt->fetch(PDO::FETCH_ASSOC);

    // Recupera i pazienti, farmaci e dottori per i menu a discesa
    $pazienti = $pdo->query("SELECT * FROM Paziente")->fetchAll(PDO::FETCH_ASSOC);
    $farmaci = $pdo->query("SELECT * FROM Farmaco")->fetchAll(PDO::FETCH_ASSOC);
    $dottori = $pdo->query("SELECT * FROM Dottore")->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo "Errore: ID referto non specificato.";
    exit;
}

// Gestione del modulo di aggiornamento
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_paziente = $_POST['id_paziente'];
    $id_farmaco = $_POST['id_farmaco'];
    $id_dottore = $_POST['id_dottore'];
    $referto = $_POST['referto'];
    $terapia = $_POST['terapia'];
    $data_assegnazione = $_POST['data_assegnazione'];

    // Esegui l'aggiornamento nel database
    $stmt = $pdo->prepare("UPDATE RefertiTerapie SET ID_Paziente = ?, ID_Farmaco = ?, ID_Dottore = ?, Referto = ?, Terapia = ?, Data_Assegnazione = ? WHERE ID_Referto = ?");
    $stmt->execute([$id_paziente, $id_farmaco, $id_dottore, $referto, $terapia, $data_assegnazione, $id_referto]);

    // Reindirizza alla pagina di gestione referti e terapie
    header('Location: gestione_referti.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Modifica Referto Terapia</title>
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
        .form-container input, .form-container select, .form-container textarea {
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
            text-decoration: none;
        }
        .back-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Modifica Referto Terapia</h1>
        <form method="POST">
            <label for="id_paziente">Paziente:</label>
            <select id="id_paziente" name="id_paziente" required>
                <?php foreach ($pazienti as $paziente): ?>
                    <option value="<?= $paziente['ID_Paziente'] ?>" <?= $paziente['ID_Paziente'] == $referto['ID_Paziente'] ? 'selected' : '' ?>>
                        <?= $paziente['Nome'] ?> <?= $paziente['Cognome'] ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="id_farmaco">Farmaco:</label>
            <select id="id_farmaco" name="id_farmaco" required>
                <?php foreach ($farmaci as $farmaco): ?>
                    <option value="<?= $farmaco['ID_Farmaco'] ?>" <?= $farmaco['ID_Farmaco'] == $referto['ID_Farmaco'] ? 'selected' : '' ?>>
                        <?= $farmaco['Nome'] ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="id_dottore">Dottore:</label>
            <select id="id_dottore" name="id_dottore" required>
                <?php foreach ($dottori as $dottore): ?>
                    <option value="<?= $dottore['ID_Dottore'] ?>" <?= $dottore['ID_Dottore'] == $referto['ID_Dottore'] ? 'selected' : '' ?>>
                        <?= $dottore['Nome'] ?> <?= $dottore['Cognome'] ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="referto">Referto:</label>
            <textarea id="referto" name="referto" required><?= $referto['Referto'] ?></textarea>

            <label for="terapia">Terapia:</label>
            <textarea id="terapia" name="terapia" required><?= $referto['Terapia'] ?></textarea>

            <label for="data_assegnazione">Data Assegnazione:</label>
            <input type="date" id="data_assegnazione" name="data_assegnazione" value="<?= $referto['Data_Assegnazione'] ?>" required>

            <input type="submit" value="Modifica Referto Terapia">
        </form>
    </div>

    <a href="gestione_referti.php" class="back-button">Indietro</a>
</body>
</html>
