<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['nome_magazzino'])) {
        // Inserimento di un nuovo magazzino
        $nome = $_POST['nome_magazzino'];
        $ubicazione = $_POST['ubicazione'];

        $stmt = $pdo->prepare("INSERT INTO Magazzino (Nome_Magazzino, Ubicazione) VALUES (?, ?)");
        $stmt->execute([$nome, $ubicazione]);
        header("Location: magazzino.php");
    }
}

// Recupera tutti i magazzini
$magazzini = $pdo->query("SELECT * FROM Magazzino")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Magazzini</title>
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
        .menu-container input[type="text"] {
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
        /* Stile per il pulsante Indietro */
        .menu-button {
            padding: 10px 20px;
            background-color: #4caf50;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            position: fixed;
            bottom: 20px;
            left: 20px;
            z-index: 1000;
        }
        .menu-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="menu-container">
        <h1>Gestione Magazzini</h1>
        <form method="post">
            <input type="text" name="nome_magazzino" placeholder="Nome Magazzino" required>
            <input type="text" name="ubicazione" placeholder="Ubicazione" required>
            <button type="submit">Aggiungi Magazzino</button>
        </form>
        <h2>Elenco Magazzini</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Ubicazione</th>
            </tr>
            <?php foreach ($magazzini as $magazzino): ?>
            <tr>
                <td><?= $magazzino['ID_Magazzino'] ?></td>
                <td><?= $magazzino['Nome_Magazzino'] ?></td>
                <td><?= $magazzino['Ubicazione'] ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- Pulsante Indietro -->
    <button class="menu-button" onclick="goBack()">Indietro</button>

    <script>
        function goBack() {
            window.history.back(); // Torna alla pagina precedente
        }
    </script>
</body>
</html>
