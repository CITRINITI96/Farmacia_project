<?php
include 'db.php';

// Recupera tutti i farmaci e i magazzini per i dropdown
$farmaci = $pdo->query("SELECT ID_Farmaco, Nome FROM Farmaco")->fetchAll(PDO::FETCH_ASSOC);
$magazzini = $pdo->query("SELECT ID_Magazzino, Nome_Magazzino FROM Magazzino")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Inserimento di un nuovo record di stock
    $id_farmaco = $_POST['id_farmaco'];
    $id_magazzino = $_POST['id_magazzino'];
    $quantita = $_POST['quantita'];
    $data_scadenza = $_POST['data_scadenza'];
    $temperatura = !empty($_POST['temperatura']) ? $_POST['temperatura'] : null;

    $stmt = $pdo->prepare("INSERT INTO Stock (ID_Farmaco, ID_Magazzino, Quantità, Data_Scadenza, Temperatura_Stoccaggio) 
                           VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$id_farmaco, $id_magazzino, $quantita, $data_scadenza, $temperatura]);
    header("Location: stock.php");
}

// Recupera tutti gli stock
$stock = $pdo->query("SELECT S.ID_Stock, F.Nome AS Nome_Farmaco, M.Nome_Magazzino, S.Quantità, S.Data_Scadenza, S.Temperatura_Stoccaggio
                      FROM Stock S
                      JOIN Farmaco F ON S.ID_Farmaco = F.ID_Farmaco
                      JOIN Magazzino M ON S.ID_Magazzino = M.ID_Magazzino")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Gestione Stock</title>
</head>
<body>
    <h1>Gestione Stock</h1>
    <form method="post">
        <select name="id_farmaco" required>
            <option value="">Seleziona Farmaco</option>
            <?php foreach ($farmaci as $farmaco): ?>
                <option value="<?= $farmaco['ID_Farmaco'] ?>"><?= $farmaco['Nome'] ?></option>
            <?php endforeach; ?>
        </select>
        <select name="id_magazzino" required>
            <option value="">Seleziona Magazzino</option>
            <?php foreach ($magazzini as $magazzino): ?>
                <option value="<?= $magazzino['ID_Magazzino'] ?>"><?= $magazzino['Nome_Magazzino'] ?></option>
            <?php endforeach; ?>
        </select>
        <input type="number" name="quantita" placeholder="Quantità" required>
        <input type="date" name="data_scadenza" placeholder="Data Scadenza" required>
        <input type="number" step="0.1" name="temperatura" placeholder="Temperatura (opzionale)">
        <button type="submit">Aggiungi Stock</button>
    </form>
    <h2>Elenco Stock</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Farmaco</th>
            <th>Magazzino</th>
            <th>Quantità</th>
            <th>Data Scadenza</th>
            <th>Temperatura</th>
        </tr>
        <?php foreach ($stock as $s): ?>
        <tr>
            <td><?= $s['ID_Stock'] ?></td>
            <td><?= $s['Nome_Farmaco'] ?></td>
            <td><?= $s['Nome_Magazzino'] ?></td>
            <td><?= $s['Quantità'] ?></td>
            <td><?= $s['Data_Scadenza'] ?></td>
            <td><?= $s['Temperatura_Stoccaggio'] ?? 'N/A' ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>

