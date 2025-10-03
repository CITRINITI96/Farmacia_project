<?php
// Includi la configurazione del database
require_once 'config.php';

// Inizializza la sessione
session_start();

// Controlla se l'utente è autenticato e se è un admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php"); // Reindirizza alla pagina di login se non è admin
    exit;
}

// Se è admin, procedi con il resto della pagina della dashboard
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Farmacia Ospedaliera</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #4caf50;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 20px;
            padding-left: 20px;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px 15px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .sidebar a:hover {
            background-color: #45a049;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .header {
            background-color: #4caf50;
            color: white;
            padding: 10px 20px;
            text-align: center;
            margin-bottom: 20px;
        }
        .welcome-message {
            margin-bottom: 30px;
        }
        .section {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h3>Dashboard Admin</h3>
        <a href="gestione_utenti.php">Gestione Utenti</a>
        <a href="farmaco.php">Gestione Farmaci</a>
        <a href="gestione_reparti.php">Gestione Reparti</a>
        <a href="gestione_referti.php">Gestione Referti</a>
        <a href="paziente.php">Anagrafica Paziente</a>
        <a href="ordini_fornitore.php">Gestione Ordini</a>
        <a href="gestione_fornitori.php">Gestione Fornitori</a>
        <a href="notifiche.php">Notifiche</a>
        <a href="magazzino.php">Gestione Magazzini</a>
        <a href="logout.php">Esci</a>
    </div>

    <!-- Main content -->
    <div class="main-content">
        <div class="header">
            <h1>Benvenuto, <?php echo htmlspecialchars($_SESSION['user_id']); ?>!</h1>
        </div>

        <div class="welcome-message">
            <p>Sei nella dashboard di amministrazione. Da qui puoi gestire tutte le funzionalità del sistema.</p>
        </div>

        <!-- Section 1: Gestione Utenti -->
        <div class="section">
            <h2>Gestione Utenti</h2>
            <p>Puoi visualizzare, modificare, e gestire gli utenti del sistema.</p>
            <a href="gestione_utenti.php">Vai alla gestione utenti</a>
        </div>

        <!-- Section 2: Gestione Farmaci -->
        <div class="section">
            <h2>Gestione Farmaci</h2>
            <p>Puoi aggiungere, modificare e rimuovere farmaci dal sistema.</p>
            <a href="farmaco.php">Vai alla gestione farmaci</a>
        </div>

        <!-- Section 3: Impostazioni -->
        <div class="section">
            <h2>Notifiche</h2>
            <p>Visualizza Notifiche presenti.</p>
            <a href="Notifiche.php">Vai alle Notifiche</a>
        </div>
    </div>

</body>
</html>
