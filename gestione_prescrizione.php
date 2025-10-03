<?php
include 'db.php';  // Assicurati di avere il file db.php che gestisce la connessione al database

// Controlla se l'ID del paziente è stato passato tramite GET
if (isset($_GET['id_paziente'])) {
    $id_paziente = $_GET['id_paziente'];

    // Query per recuperare le prescrizioni del paziente
    $stmt = $pdo->prepare("SELECT p.ID_Paziente, p.ID_Farmaco, p.Quantità, p.Data_Prescrizione
                           FROM Prescrizione p
                           WHERE p.ID_Paziente = ?");
    $stmt->execute([$id_paziente]);

    // Ottieni tutte le prescrizioni
    $prescrizioni = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo "Errore: ID paziente non specificato.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Gestione Prescrizioni</title>
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
            text-align: center;
        }
        .form-container h1 {
            font-size: 24px;
            color: #4caf50;
            margin-bottom: 20px;
        }
        .form-container table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        .form-container table th,
        .form-container table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .form-container table th {
            background-color: #4caf50;
            color: white;
        }
        .form-container table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .form-container table tr:hover {
            background-color: #e2e2e2;
        }
        .button {
            background-color: #4caf50;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        .button:hover {
            background-color: #45a049;
        }
        .add-button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            margin-bottom: 20px;
        }
        .add-button:hover {
            background-color: #0056b3;
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
        <h1>Gestione Prescrizioni - Paziente</h1>
        <a href="aggiungi_prescrizione.php?id_paziente=<?= $id_paziente ?>" class="add-button">Aggiungi Nuova Prescrizione</a>
        <table>
            <tr>
                <th>ID Paziente</th>
                <th>ID Farmaco</th>
                <th>Quantità</th>
                <th>Data Prescrizione</th>
                <th>Modifica</th>
                <th>Elimina</th>
            </tr>
            <?php foreach ($prescrizioni as $prescrizione): ?>
                <tr>
                    <td><?= $prescrizione['ID_Paziente'] ?></td>
                    <td><?= $prescrizione['ID_Farmaco'] ?></td>
                    <td><?= $prescrizione['Quantità'] ?></td>
                    <td><?= $prescrizione['Data_Prescrizione'] ?></td>
                    <td>
                        <a href="modifica_prescrizione.php?id_paziente=<?= $prescrizione['ID_Paziente'] ?>&id_farmaco=<?= $prescrizione['ID_Farmaco'] ?>&data_prescrizione=<?= $prescrizione['Data_Prescrizione'] ?>" class="button">Modifica</a>
                    </td>
                    <td>
                        <a href="elimina_prescrizione.php?id_paziente=<?= $prescrizione['ID_Paziente'] ?>&id_farmaco=<?= $prescrizione['ID_Farmaco'] ?>&data_prescrizione=<?= $prescrizione['Data_Prescrizione'] ?>" class="button" onclick="return confirm('Sei sicuro di voler eliminare questa prescrizione?');">Elimina</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- Pulsante Indietro -->
    <a href="paziente.php" class="back-button">Indietro</a>
</body>
</html>
