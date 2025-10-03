<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Chiamata alle procedure per generare notifiche
    if (isset($_POST['notifiche_scorte'])) {
        try {
            // Chiamata alla procedura per generare notifiche per scorte basse
            $stmt = $pdo->query("CALL Genera_Notifiche_Scorte_Basse()");
        } catch (PDOException $e) {
            // Nessun messaggio visualizzato in caso di errore
        }
    } elseif (isset($_POST['notifiche_scadenze'])) {
        try {
            // Chiamata alla procedura per generare notifiche per scadenze imminenti
            $stmt = $pdo->query("CALL Genera_Notifiche_Scadenza_Imminente()");
        } catch (PDOException $e) {
            // Nessun messaggio visualizzato in caso di errore
        }
    } elseif (isset($_POST['azzera_notifiche'])) {
        // Azzeramento delle notifiche
        try {
            $pdo->query("DELETE FROM Notifiche");
        } catch (PDOException $e) {
            // Nessun messaggio visualizzato in caso di errore
        }
    }
}

// Recupera tutte le notifiche
$notifiche = $pdo->query("SELECT N.ID_Notifica, N.Tipo_Notifica, N.ID_Stock, N.Messaggio, N.Data_Notifica, 
                                 F.Nome AS Nome_Farmaco
                          FROM Notifiche N
                          LEFT JOIN Stock S ON N.ID_Stock = S.ID_Stock
                          LEFT JOIN Farmaco F ON S.ID_Farmaco = F.ID_Farmaco")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Notifiche</title>
    <style>
        /* Sfondo che sfuma dal verde al bianco */
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom, #4caf50 0%, #ffffff 100%);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .menu-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 60%;
            text-align: center;
        }

        .menu-container h1 {
            font-size: 24px;
            color: #4caf50;
            margin-bottom: 20px;
        }

        .menu-container form {
            margin-bottom: 20px;
        }

        .menu-container button {
            background-color: #4caf50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 45%;
            margin: 10px 2%;
        }

        .menu-container button:hover {
            background-color: #45a049;
        }

        /* Tabella scrollabile */
        .table-container {
            max-height: 300px; /* Limita l'altezza della tabella */
            overflow-y: auto;  /* Abilita lo scroll verticale */
            margin-top: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .menu-container table {
            width: 100%;
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

        /* Pulsante di azzeramento con colore rosso */
        .menu-container button#azzera {
            background-color: #f44336;
        }

        .menu-container button#azzera:hover {
            background-color: #d32f2f;
        }

        /* Pulsante "Indietro" */
        .indietro-button {
            background-color: #4caf50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            position: absolute;
            bottom: 20px;
            left: 20px;
        }

        .indietro-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="menu-container">
        <h1>Gestione Notifiche</h1>
        <form method="post">
            <button name="notifiche_scorte">Genera Notifiche Scorte Basse</button>
            <button name="notifiche_scadenze">Genera Notifiche Scadenze Imminenti</button>
            <br><br>
            <button name="azzera_notifiche" id="azzera">Azzera Notifiche</button>
        </form>

        <h2>Elenco Notifiche</h2>

        <div class="table-container">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Tipo</th>
                    <th>Stock</th>
                    <th>Farmaco</th>
                    <th>Messaggio</th>
                    <th>Data</th>
                </tr>
                <?php foreach ($notifiche as $notifica): ?>
                <tr>
                    <td><?= $notifica['ID_Notifica'] ?></td>
                    <td><?= $notifica['Tipo_Notifica'] ?></td>
                    <td><?= $notifica['ID_Stock'] ?></td>
                    <td><?= $notifica['Nome_Farmaco'] ?? 'N/A' ?></td>
                    <td><?= $notifica['Messaggio'] ?></td>
                    <td><?= $notifica['Data_Notifica'] ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    
    <!-- Pulsante Indietro -->
     <form action="menu.php" method="get">
        <button type="submit" class="indietro-button">Indietro</button>
    </form>
    
</body>
</html>
