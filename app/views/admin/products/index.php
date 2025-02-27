<?php
// Check of gebruiker is ingelogd
if (!isset($_SESSION['admin'])) {
    header('Location: /?route=admin/login');
    exit;
}
?>

<!-- Laad product-specifieke CSS -->
<link rel="stylesheet" href="public/css/products.css">

<div class="admin-products">
    <h2>Producten Beheren</h2>

    <!-- Nieuwe product knop -->
    <button class="new-product-btn" id="btnNewProduct">
        <span>➕</span>
        <span>Nieuw Product</span>
    </button>
    
    <!-- Product lijst -->
    <div class="products-list">
        <table class="products-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Categorie</th>
                    <th>Prijs per eenheid</th>
                    <th class="actions-column">Acties</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr class="product-row" data-product-id="<?php echo $product['product_id']; ?>">
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['category']); ?></td>
                    <td class="price-unit-cell">
                        <div class="price">€<?php echo number_format($product['price'], 2); ?></div>
                        <div class="unit"><?php echo htmlspecialchars($product['unit']); ?></div>
                    </td>
                    <td class="actions-column">
                        <button class="action-btn edit" title="Bewerken">✏️</button>
                        <button class="action-btn delete" title="Verwijderen">🗑️</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Product formulier (verborgen by default) -->
    <div class="product-form hidden" id="productForm">
        <div class="form-row">
            <label for="productName">Productnaam</label>
            <input type="text" id="productName" name="name" required>
        </div>

        <div class="price-unit-row">
            <div class="form-row">
                <label for="productPrice">Prijs</label>
                <div class="price-input-group">
                    <input type="number" id="productPrice" name="price" step="0.01" min="0" required>
                </div>
            </div>
            <div class="form-row unit-group">
                <label>Eenheid</label>
                <div class="unit-inputs">
                    <input type="number" id="unitAmount" name="unit_amount" min="1" step="1" value="1" required>
                    <select id="unitType" name="unit_type" required>
                        <option value="ML">ML</option>
                        <option value="L">L</option>
                        <option value="GR">GR</option>
                        <option value="KG">KG</option>
                        <option value="DOOS">DOOS</option>
                        <option value="STUK">STUK</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-row">
            <label for="productCategory">Categorie</label>
            <select id="productCategory" name="category" required>
                <option value="Gekoeld">Gekoeld</option>
                <option value="Ongekoeld">Ongekoeld</option>
            </select>
        </div>

        <div class="form-row">
            <label for="productDescription">Beschrijving (optioneel)</label>
            <input type="text" id="productDescription" name="description">
        </div>

        <div class="form-buttons">
            <button type="button" class="cancel" id="btnCancel">Annuleren</button>
            <button type="button" class="save" id="btnSave">Opslaan</button>
        </div>
    </div>
</div>

<!-- Laad product-specifieke JavaScript -->
<script src="public/js/products.js"></script> 