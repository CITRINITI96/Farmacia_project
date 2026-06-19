<?php
// sidebar_admin.php — incluso in tutte le pagine admin
// Richiede che session sia già avviata e requireAuth(['Admin']) sia già chiamato.
$current = basename($_SERVER['PHP_SELF']);
function navLink(string $href, string $label, string $current): string {
    $active = ($current === $href) ? ' active' : '';
    return "<a href=\"{$href}\" class=\"{$active}\">{$label}</a>";
}
?>
<div class="sidebar">
    <div class="sidebar-logo">
        <div class="brand">💊 PharmaCare</div>
        <div style="font-size:.78rem;color:rgba(255,255,255,.7);margin-top:4px;">
            Admin: <?= htmlspecialchars($_SESSION['user_id']) ?>
        </div>
    </div>
    <nav>
        <?= navLink('dashboard_Admin.php', '🏠 Dashboard',        $current) ?>
        <?= navLink('gestione_utenti.php', '👥 Gestione Utenti',  $current) ?>
        <?= navLink('farmaco.php',         '💊 Farmaci',          $current) ?>
        <?= navLink('gestione_reparti.php','🏥 Reparti',          $current) ?>
        <?= navLink('gestione_referti.php','📋 Referti',          $current) ?>
        <?= navLink('paziente.php',        '🧑‍⚕️ Pazienti',        $current) ?>
        <?= navLink('gestione_fornitori.php','🚚 Fornitori',      $current) ?>
        <?= navLink('ordini_fornitore.php', '📦 Ordini',          $current) ?>
        <?= navLink('magazzino.php',       '🏭 Magazzini',        $current) ?>
        <?= navLink('stock.php',           '📊 Stock',            $current) ?>
        <?= navLink('notifiche.php',       '🔔 Notifiche',        $current) ?>
        <hr class="sidebar-divider">
        <?= navLink('logout.php',          '🚪 Esci',             $current) ?>
    </nav>
</div>
