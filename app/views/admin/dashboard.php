<?php
// Admin dashboard met statistieken en menu opties
?>

<!-- Laad dashboard-specifieke CSS -->
<link rel="stylesheet" href="public/css/dashboard.css">

<div class="admin-dashboard">
    <h2>Admin Dashboard</h2>

    <!-- Statistieken -->
    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-value"><?php echo $stats['total_products']; ?></span>
            <span class="stat-label">Actieve Producten</span>
        </div>
        <div class="stat-card">
            <span class="stat-value"><?php echo $stats['empty_slots']; ?></span>
            <span class="stat-label">Lege Vakken</span>
        </div>
        <div class="stat-card warning">
            <span class="stat-value"><?php echo $stats['low_stock']; ?></span>
            <span class="stat-label">Bijna Op</span>
        </div>
    </div>

    <!-- Beheer menu -->
    <div class="admin-menu">
        <a href="/?route=products" class="menu-button">
            <span class="icon">📦</span>
            <span class="label">Producten Beheren</span>
            <span class="description">Toevoegen, wijzigen en verwijderen van producten</span>
        </a>
        
        <a href="/?route=admin/slots" class="menu-button">
            <span class="icon">🔧</span>
            <span class="label">Vakken Beheren</span>
            <span class="description">Vullen en configureren van automaatvakken</span>
        </a>
        
        <a href="/?route=admin/stock" class="menu-button">
            <span class="icon">📊</span>
            <span class="label">Voorraad Bijwerken</span>
            <span class="description">Voorraadniveaus aanpassen en bijvullen</span>
        </a>

        <a href="/?route=admin/transactions" class="menu-button">
            <span class="icon">💶</span>
            <span class="label">Transacties Inzien</span>
            <span class="description">Bekijk verkopen en omzet</span>
        </a>
    </div>

    <!-- Uitlog knop -->
    <a href="/?route=admin/logout" class="logout-button">Uitloggen</a>
</div>

<style>
.admin-dashboard {
    padding: 20px;
}

.admin-dashboard h2 {
    color: var(--primary-color);
    margin-bottom: 20px;
    text-align: center;
}

/* Statistieken grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 15px;
    border-radius: var(--border-radius);
    text-align: center;
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.stat-card.warning .stat-value {
    color: #ff9800;
}

.stat-value {
    font-size: 24px;
    font-weight: bold;
    color: var(--primary-color);
}

.stat-label {
    font-size: 14px;
    color: var(--text-color);
}

/* Beheer menu */
.admin-menu {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.menu-button {
    display: flex;
    align-items: center;
    gap: 15px;
    background: white;
    padding: 15px;
    border-radius: var(--border-radius);
    text-decoration: none;
    color: var(--text-color);
    transition: all 0.2s ease;
}

.menu-button:hover {
    transform: translateY(-2px);
    background: var(--secondary-color);
    color: white;
}

.menu-button .icon {
    font-size: 24px;
    min-width: 40px;
    text-align: center;
}

.menu-button .label {
    font-weight: bold;
    font-size: 16px;
}

.menu-button .description {
    font-size: 14px;
    margin-left: auto;
    opacity: 0.8;
}

/* Uitlog knop */
.logout-button {
    display: block;
    width: 100%;
    padding: 12px;
    background-color: #dc3545;
    color: white;
    text-align: center;
    text-decoration: none;
    border-radius: var(--border-radius);
    margin-top: 30px;
    transition: all 0.2s ease;
}

.logout-button:hover {
    background-color: #c82333;
    transform: translateY(-2px);
}
</style> 