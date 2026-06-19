<?php
require_once 'auth.php';
requireAuth(); // qualsiasi ruolo loggato
$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu — PharmaCare</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 14px;
            margin-top: 20px;
        }
        .menu-item {
            background: var(--white);
            border: 2px solid var(--green-100);
            border-radius: var(--radius-md);
            padding: 22px 16px;
            text-align: center;
            text-decoration: none;
            color: var(--green-700);
            font-weight: 600;
            font-size: .95rem;
            transition: all var(--transition);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }
        .menu-item .icon { font-size: 1.8rem; }
        .menu-item:hover {
            background: var(--green-500);
            color: var(--white);
            border-color: var(--green-500);
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
            text-decoration: none;
        }
        .menu-item.logout { border-color: var(--red-100); color: var(--red-600); }
        .menu-item.logout:hover { background: var(--red-600); border-color: var(--red-600); color: var(--white); }
        .role-badge {
            display: inline-block;
            background: rgba(255,255,255,.2);
            border: 1px solid rgba(255,255,255,.4);
            padding: 4px 12px;
            border-radius: 50px;
            font-size: .82rem;
            margin-top: 6px;
        }
    </style>
</head>
<body>
<div class="page-center" style="flex-direction:column;align-items:center;">
    <div class="card card-wide">
        <div class="card-header" style="margin-bottom:0">
            <div class="brand">💊 PharmaCare</div>
            <p style="color:var(--gray-700);margin-top:4px;">
                Bentornato, <strong><?= htmlspecialchars($_SESSION['user_id']) ?></strong>
                <span class="badge badge-success" style="margin-left:6px;"><?= htmlspecialchars($role) ?></span>
            </p>
        </div>

        <div class="menu-grid">
            <?php if (in_array($role, ['Dottore', 'Dottoressa'])): ?>
                <a href="paziente.php" class="menu-item">
                    <span class="icon">🧑‍⚕️</span> Pazienti
                </a>
                <a href="gestione_reparti.php" class="menu-item">
                    <span class="icon">🏥</span> Reparti
                </a>
                <a href="gestione_referti.php" class="menu-item">
                    <span class="icon">📋</span> Referti
                </a>
            <?php endif; ?>

            <?php if ($role === 'Farmacista'): ?>
                <a href="farmaco.php" class="menu-item">
                    <span class="icon">💊</span> Farmaci
                </a>
                <a href="ordini_fornitore.php" class="menu-item">
                    <span class="icon">📦</span> Ordini
                </a>
                <a href="gestione_fornitori.php" class="menu-item">
                    <span class="icon">🚚</span> Fornitori
                </a>
                <a href="notifiche.php" class="menu-item">
                    <span class="icon">🔔</span> Notifiche
                </a>
            <?php endif; ?>

            <?php if ($role === 'Magazziniere'): ?>
                <a href="ordini_fornitore.php" class="menu-item">
                    <span class="icon">📦</span> Ordini
                </a>
                <a href="magazzino.php" class="menu-item">
                    <span class="icon">🏭</span> Magazzini
                </a>
                <a href="stock.php" class="menu-item">
                    <span class="icon">📊</span> Stock
                </a>
            <?php endif; ?>

            <a href="logout.php" class="menu-item logout">
                <span class="icon">🚪</span> Logout
            </a>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
