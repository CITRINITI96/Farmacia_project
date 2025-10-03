<?php
include 'db.php';  // Assicurati di avere il file db.php che gestisce la connessione al database

// Recupera i pazienti dal database
$pazienti = $pdo->query("SELECT P.ID_Paziente, P.Nome, P.Cognome, P.Data_Nascita, P.Sesso, R.Nome_Reparto 
                         FROM Paziente P
                         JOIN Reparto R ON P.ID_Reparto = R.ID_Reparto")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Gestione Pazienti</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(to bottom, #4caf50 50%, #ffffff 50%);
        }
        .menu-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 60%;
            overflow-y: auto;
        }
        .menu-container h1 {
            font-size: 24px;
            color: #4caf50;
            margin-bottom: 20px;
        }
        .menu-container table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        .menu-container table th,
        .menu-container table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .menu-container table th {
            background-color: #4caf50;
            color: white;
        }
        .menu-container table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .menu-container table tr:hover {
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
            display: inline-block;
        }
        .add-button:hover {
            background-color: #0056b3;
        }

        /* Posiziona il pulsante indietro in basso a sinistra della pagina */
        .back-button {
            background-color: #4caf50;  /* Stesso verde degli altri pulsanti */
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            position: fixed;
            bottom: 10px;
            left: 10px;
        }
        .back-button:hover {
            background-color: #45a049;  /* Verde più scuro al passaggio */
        }
    </style>
</head>
<body>
    <div class="menu-container">
        <h1>Gestione Pazienti</h1>
        <h2>Elenco Pazienti</h2>
        <!-- Pulsante per aggiungere un nuovo paziente -->
        <a href="aggiungi_paziente.php" class="add-button">Aggiungi Nuovo Paziente</a>
        <table>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Cognome</th>
                <th>Data di Nascita</th>
                <th>Sesso</th>
                <th>Reparto</th>
                <th>Gestione Prescrizioni</th>
                <th>Modifica</th>
                <th>Elimina</th>
            </tr>
            <?php foreach ($pazienti as $paziente): ?>
                <tr>
                    <td><?= $paziente['ID_Paziente'] ?></td>
                    <td><?= $paziente['Nome'] ?></td>
                    <td><?= $paziente['Cognome'] ?></td>
                    <td><?= $paziente['Data_Nascita'] ?></td>
                    <td><?= $paziente['Sesso'] ?></td>
                    <td><?= $paziente['Nome_Reparto'] ?></td>
                    <td>
                        <!-- Aggiunto il link per la gestione delle prescrizioni -->
                        <a href="gestione_prescrizione.php?id_paziente=<?= $paziente['ID_Paziente'] ?>" class="button">Gestione Prescrizioni</a>
                    </td>
                    <td>
                        <a href="modifica_paziente.php?id=<?= $paziente['ID_Paziente'] ?>" class="button">Modifica</a>
                    </td>
                    <td>
                        <a href="elimina_paziente.php?id=<?= $paziente['ID_Paziente'] ?>" class="button" onclick="return confirm('Sei sicuro di voler eliminare questo paziente?');">Elimina</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- Pulsante Indietro in basso a sinistra -->
    <a href="menu.php" class="back-button">Indietro</a>
</body>
</html>
