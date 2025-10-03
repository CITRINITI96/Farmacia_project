<?php
include 'db.php';  // Connessione al database

// Controlla se l'ID del paziente è stato passato tramite GET
if (isset($_GET['id_paziente'])) {
    $id_paziente = $_GET['id_paziente'];
} else {
    // Se l'ID paziente non è passato, mostra un errore
    echo "Errore: ID paziente non specificato.";
    exit;
}

// Se il modulo di aggiunta prescrizione è stato inviato
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica che 'id_farmaco' sia stato selezionato
    if (isset($_POST['id_farmaco']) && !empty($_POST['id_farmaco'])) {
        // Recupera i dati dal modulo
        $id_farmaco = $_POST['id_farmaco'];  // ID del farmaco selezionato
        $quantita = $_POST['quantita'];
        $data_prescrizione = $_POST['data_prescrizione'];

        // Controlla che la quantità e la data di prescrizione siano valide
        if (empty($quantita) || empty($data_prescrizione)) {
            echo "Errore: la quantità e la data della prescrizione sono obbligatorie.";
            exit;
        }

        // Prepara la query SQL per inserire una nuova prescrizione
        $stmt = $pdo->prepare("INSERT INTO Prescrizione (ID_Paziente, ID_Farmaco, Quantità, Data_Prescrizione) 
                               VALUES (?, ?, ?, ?)");
        $stmt->execute([$id_paziente, $id_farmaco, $quantita, $data_prescrizione]);

        // Redirect alla pagina di gestione prescrizioni
        header("Location: gestione_prescrizione.php?id_paziente=$id_paziente");
        exit;
    } else {
        // Se il farmaco non è stato selezionato
        echo "Errore: selezionare un farmaco.";
        exit;
    }
}

// Recupera tutti i farmaci dal database
function getFarmaci() {
    global $pdo;
    $stmt = $pdo->query("SELECT ID_Farmaco, Nome FROM Farmaco");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$farmaci = getFarmaci(); // Ottieni la lista dei farmaci
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Aggiungi Prescrizione</title>
    <style>
        /* Stili di base per il layout */
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom, #28a745 50%, #ffffff 50%); /* Gradiente verde e bianco */
            margin: 0;
            padding: 0;
            height: 100vh;  /* Imposta l'altezza della pagina a tutta la finestra */
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        .container {
            width: 80%;
            max-width: 600px; /* Massima larghezza del modulo */
            background-color: #ffffff; /* Bianco per il modulo */
            padding: 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            box-sizing: border-box;
            margin-bottom: 30px; /* Distanza tra i pulsanti */
        }
        h1 {
            text-align: center;
            font-size: 28px;
            color: #007bff; /* Blu principale */
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
            display: block;
        }
        input, select {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        button, .add-button, .back-button {
            background-color: #4CAF50; /* Verde per i pulsanti */
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%; /* Impostiamo la larghezza al 100% per tutti i pulsanti */
            font-size: 16px;
            text-align: center;
            display: inline-block;
        }
        button:hover, .add-button:hover, .back-button:hover {
            background-color: #45a049; /* Verde scuro al passaggio del mouse */
        }
        .add-button {
            margin-top: 20px; /* Separazione tra i pulsanti */
        }
        .back-button {
            position: absolute;
            bottom: 20px;
            left: 20px;
            width: auto; /* Non al 100% */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Aggiungi Nuova Prescrizione</h1>
        <form action="" method="post">
            <label for="id_farmaco">Seleziona Farmaco:</label>
            <select id="id_farmaco" name="id_farmaco" required>
                <option value="">Seleziona un Farmaco</option>
                <?php
                // Visualizza i farmaci recuperati dal database
                foreach ($farmaci as $farmaco) {
                    echo "<option value='{$farmaco['ID_Farmaco']}'>{$farmaco['Nome']}</option>";
                }
                ?>
            </select>

            <label for="quantita">Quantità:</label>
            <input type="number" id="quantita" name="quantita" required>

            <label for="data_prescrizione">Data Prescrizione:</label>
            <input type="date" id="data_prescrizione" name="data_prescrizione" required>

            <button type="submit">Aggiungi Prescrizione</button>
        </form>

        <!-- Pulsante per aggiungere un nuovo farmaco -->
        <a href="farmaco.php" class="add-button">Aggiungi Nuovo Farmaco</a>
    </div>

    <!-- Pulsante indietro in basso a sinistra -->
    <a href="gestione_prescrizione.php?id_paziente=<?= $id_paziente ?>" class="back-button">Indietro</a>
</body>
</html>
