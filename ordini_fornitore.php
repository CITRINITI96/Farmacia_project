<?php
include 'db.php';

$farmaci = $pdo->query("SELECT ID_Farmaco, Nome FROM Farmaco")->fetchAll(PDO::FETCH_ASSOC);
$message = '';

// Gestione dell'inserimento di un nuovo ordine
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_farmaco'])) {
    try {
        $id_farmaco = $_POST['id_farmaco'];
        $quantita = $_POST['quantita'];
        $data_ordine = $_POST['data_ordine'];
        $stato = $_POST['stato'];

        // Verifica se la quantità è positiva
        if ($quantita <= 0) {
            $message = 'La quantità ordinata deve essere maggiore di zero!';
        } else {
            $stmt = $pdo->prepare("INSERT INTO Ordine_Fornitore (ID_Farmaco, Quantità_Ordinata, Data_Ordine, Stato) 
                                   VALUES (?, ?, ?, ?)");
            $stmt->execute([$id_farmaco, $quantita, $data_ordine, $stato]);
            $message = 'Ordine aggiunto con successo!';
        }
    } catch (Exception $e) {
        $message = 'Errore nell\'aggiungere l\'ordine: ' . $e->getMessage();
    }
}

// Gestione dell'aggiornamento dello stato dell'ordine
if (isset($_POST['update_stato'])) {
    try {
        $id_ordine = $_POST['id_ordine'];
        $stato = $_POST['stato'];

        $stmt = $pdo->prepare("UPDATE Ordine_Fornitore SET Stato = ? WHERE ID_Ordine = ?");
        $stmt->execute([$stato, $id_ordine]);
        $message = 'Stato ordine aggiornato con successo!';
    } catch (Exception $e) {
        $message = 'Errore nell\'aggiornamento dello stato: ' . $e->getMessage();
    }
}

// Recupera tutti gli ordini fornitori
$ordini = $pdo->query("SELECT O.ID_Ordine, F.Nome AS Nome_Farmaco, O.Quantità_Ordinata, O.Data_Ordine, O.Stato
                       FROM Ordine_Fornitore O
                       JOIN Farmaco F ON O.ID_Farmaco = F.ID_Farmaco")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Ordini Fornitori</title>
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
        }
        .menu-container h1 {
            font-size: 24px;
            color: #4caf50;
            margin-bottom: 20px;
        }
        .menu-container form {
            margin-bottom: 20px;
        }
        .menu-container input[type="text"],
        .menu-container input[type="number"],
        .menu-container input[type="date"],
        .menu-container select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .menu-container button {
            background-color: #4caf50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        .menu-container button:hover {
            background-color: #45a049;
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
        .menu-container .table-scroll {
            max-height: 400px;
            overflow-y: auto;
        }
        .message {
            background-color: #4caf50;
            color: white;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: bold;
        }
        /* Stile per il pulsante Indietro */
        .back-button {
            position: absolute;
            bottom: 20px;
            left: 20px;
            background-color: #4caf50;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }
        .back-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="menu-container">
        <h1>Gestione Ordini Fornitori</h1>

        <?php if ($message): ?>
        <div class="message"><?= $message ?></div>
        <?php endif; ?>

        <form method="post">
            <select name="id_farmaco" required>
                <option value="">Seleziona Farmaco</option>
                <?php foreach ($farmaci as $farmaco): ?>
                    <option value="<?= $farmaco['ID_Farmaco'] ?>"><?= $farmaco['Nome'] ?></option>
                <?php endforeach; ?>
            </select>
            <input type="number" name="quantita" placeholder="Quantità Ordinata" required>
            <input type="date" name="data_ordine" placeholder="Data Ordine" required>
            <select name="stato" required>
                <option value="In_Elaborazione">In Elaborazione</option>
                <option value="Evadibile">Evadibile</option>
                <option value="Annullato">Annullato</option>
            </select>
            <button type="submit">Aggiungi Ordine</button>
        </form>
        
        <h2>Elenco Ordini Fornitori</h2>
        <div class="table-scroll">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Farmaco</th>
                    <th>Quantità</th>
                    <th>Data Ordine</th>
                    <th>Stato</th>
                    <th>Modifica Stato</th>
                </tr>
                <?php foreach ($ordini as $ordine): ?>
                <tr>
                    <td><?= $ordine['ID_Ordine'] ?></td>
                    <td><?= $ordine['Nome_Farmaco'] ?></td>
                    <td><?= $ordine['Quantità_Ordinata'] ?></td>
                    <td><?= $ordine['Data_Ordine'] ?></td>
                    <td><?= $ordine['Stato'] ?></td>
                    <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="id_ordine" value="<?= $ordine['ID_Ordine'] ?>">
                            <select name="stato" required>
                                <option value="In_Elaborazione" <?= $ordine['Stato'] == 'In_Elaborazione' ? 'selected' : '' ?>>In Elaborazione</option>
                                <option value="Evadibile" <?= $ordine['Stato'] == 'Evadibile' ? 'selected' : '' ?>>Evadibile</option>
                                <option value="Annullato" <?= $ordine['Stato'] == 'Annullato' ? 'selected' : '' ?>>Annullato</option>
                            </select>
                            <button type="submit" name="update_stato">Aggiorna Stato</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <!-- Pulsante Indietro posizionato in basso a sinistra -->
    <a href="javascript:history.back()" class="back-button">Indietro</a>
</body>
</html>
