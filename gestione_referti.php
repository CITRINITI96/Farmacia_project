<?php
include 'db.php';  // Connessione al database

// Recupera tutti i referti e terapie
$stmt = $pdo->query("SELECT RefertiTerapie.ID_Referto, 
                             Paziente.Nome AS Paziente_Nome, 
                             Paziente.Cognome AS Paziente_Cognome, 
                             Farmaco.Nome AS Nome_Farmaco, 
                             Dottore.Nome AS Dottore_Nome, 
                             RefertiTerapie.Referto, 
                             RefertiTerapie.Terapia, 
                             RefertiTerapie.Data_Assegnazione
                        FROM RefertiTerapie
                        JOIN Paziente ON RefertiTerapie.ID_Paziente = Paziente.ID_Paziente
                        JOIN Farmaco ON RefertiTerapie.ID_Farmaco = Farmaco.ID_Farmaco
                        JOIN Dottore ON RefertiTerapie.ID_Dottore = Dottore.ID_Dottore");

$referti = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Gestione Referti e Terapie</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom, #4caf50 50%, #ffffff 50%);
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            height: 100vh;
        }
        .container {
            text-align: center;
            width: 80%;
            max-width: 1200px;
        }
        h1 {
            font-size: 24px;
            color: #4caf50;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #4caf50;
            color: white;
        }
        .action-buttons {
            display: flex;
            justify-content: space-around;
        }
        .action-buttons a {
            background-color: #4caf50;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
        }
        .action-buttons a:hover {
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
        .add-button {
            background-color: #4caf50;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            display: inline-block;
        }
        .add-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestione Referti e Terapie</h1>

        <!-- Aggiungi pulsante per nuovo referto con lo stesso stile degli altri pulsanti -->
        <a href="aggiungi_referto_terapia.php" class="add-button">Aggiungi Nuovo Referto Terapia</a>

        <table>
            <thead>
                <tr>
                    <th>ID Referto</th>
                    <th>Paziente</th>
                    <th>Farmaco</th>
                    <th>Dottore</th>
                    <th>Referto</th>
                    <th>Terapia</th>
                    <th>Data Assegnazione</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($referti as $referto): ?>
                    <tr>
                        <td><?= $referto['ID_Referto'] ?></td>
                        <td><?= $referto['Paziente_Nome'] ?> <?= $referto['Paziente_Cognome'] ?></td>
                        <td><?= $referto['Nome_Farmaco'] ?></td> <!-- Correzione qui: Nome_Farmaco -->
                        <td><?= $referto['Dottore_Nome'] ?></td>
                        <td><?= $referto['Referto'] ?></td>
                        <td><?= $referto['Terapia'] ?></td>
                        <td><?= $referto['Data_Assegnazione'] ?></td>
                        <td class="action-buttons">
                            <a href="modifica_referto_terapia.php?id_referto=<?= $referto['ID_Referto'] ?>">Modifica</a>
                            <a href="elimina_referto_terapia.php?id_referto=<?= $referto['ID_Referto'] ?>" onclick="return confirm('Sei sicuro di voler eliminare questo referto?')">Elimina</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <a href="menu.php" class="back-button">Indietro</a>
</body>
</html>
