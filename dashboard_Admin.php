<?php
require_once 'db.php';
require_once 'auth.php';
requireAuth('Admin');

// Statistiche rapide
$n_farmaci   = $pdo->query("SELECT COUNT(*) FROM Farmaco")->fetchColumn();
$n_utenti    = $pdo->query("SELECT COUNT(*) FROM Utente")->fetchColumn();
$n_notifiche = $pdo->query("SELECT COUNT(*) FROM Notifiche")->fetchColumn();
$n_ordini    = $pdo->query("SELECT COUNT(*) FROM Ordine_Fornitore WHERE Stato = 'In_Elaborazione'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin — PharmaCare</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }
        .kpi-card {
            background: var(--white);
            border-radius: var(--radius-md);
            padding: 20px 24px;
            box-shadow: var(--shadow-sm);
            border-left: 4px solid var(--green-500);
            transition: box-shadow var(--transition), transform var(--transition);
        }
        .kpi-card:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }
        .kpi-card .value { font-size: 2rem; font-weight: 700; color: var(--green-700); }
        .kpi-card .label { font-size: .82rem; color: var(--gray-700); margin-top: 4px; }
        .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
        }
        .quick-link {
            background: var(--white);
            border-radius: var(--radius-md);
            padding: 20px;
            box-shadow: var(--shadow-sm);
            text-decoration: none;
            color: var(--gray-900);
            transition: box-shadow var(--transition), transform var(--transition);
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .quick-link:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); text-decoration: none; }
        .quick-link .icon { font-size: 1.8rem; }
        .quick-link .title { font-weight: 600; font-size: .95rem; }
        .quick-link .desc  { font-size: .8rem; color: var(--gray-700); }
    </style>
</head>
<body>
<div class="layout">
    <?php include 'sidebar_admin.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <h1>Benvenuto, <?= htmlspecialchars($_SESSION['user_id']) ?> 👋</h1>
            <p style="color:var(--gray-700)">Pannello di controllo della Farmacia Ospedaliera</p>
        </div>

        <!-- KPI Cards -->
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="value"><?= $n_farmaci ?></div>
                <div class="label">💊 Farmaci in catalogo</div>
            </div>
            <div class="kpi-card">
                <div class="value"><?= $n_utenti ?></div>
                <div class="label">👥 Utenti registrati</div>
            </div>
            <div class="kpi-card" style="border-color:var(--red-600)">
                <div class="value" style="color:var(--red-600)"><?= $n_notifiche ?></div>
                <div class="label">🔔 Notifiche attive</div>
            </div>
            <div class="kpi-card" style="border-color:#f57c00">
                <div class="value" style="color:#e65100"><?= $n_ordini ?></div>
                <div class="label">📦 Ordini in elaborazione</div>
            </div>
        </div>

        <!-- Accesso rapido -->
        <h2>Accesso Rapido</h2>
        <div class="quick-links">
            <a href="gestione_utenti.php" class="quick-link">
                <div class="icon">👥</div>
                <div><div class="title">Utenti</div><div class="desc">Gestisci gli account</div></div>
            </a>
            <a href="farmaco.php" class="quick-link">
                <div class="icon">💊</div>
                <div><div class="title">Farmaci</div><div class="desc">Catalogo farmaci</div></div>
            </a>
            <a href="notifiche.php" class="quick-link">
                <div class="icon">🔔</div>
                <div><div class="title">Notifiche</div><div class="desc">Scorte basse e scadenze</div></div>
            </a>
            <a href="gestione_fornitori.php" class="quick-link">
                <div class="icon">🚚</div>
                <div><div class="title">Fornitori</div><div class="desc">Gestione fornitori</div></div>
            </a>
            <a href="ordini_fornitore.php" class="quick-link">
                <div class="icon">📦</div>
                <div><div class="title">Ordini</div><div class="desc">Ordini ai fornitori</div></div>
            </a>
            <a href="magazzino.php" class="quick-link">
                <div class="icon">🏭</div>
                <div><div class="title">Magazzini</div><div class="desc">Gestione magazzini</div></div>
            </a>
        </div>
    </div>
</div>
</body>
</html>
