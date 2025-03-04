<?php
// Check of gebruiker is ingelogd
if (!isset($_SESSION['admin'])) {
    header('Location: /?route=admin/login');
    exit;
}
?>

<!-- Laad slot-specifieke CSS -->
<link rel="stylesheet" href="public/css/slots.css">

<div class="admin-slots">
    <h2>Vakken Beheren</h2>
    
    <div class="slots-grid">
        <!-- Gekoelde vakken sectie -->
        <div class="section-header">Gekoelde Vakken ❄️</div>
        <div class="cooled-slots">
            <?php 
            $cooledSlots = array_filter($slots, function($slot) {
                return $slot['slot_type'] === 'COOLED';
            });
            foreach ($cooledSlots as $slot): ?>
                <div class="slot <?php echo $slot['status']; ?>" 
                     data-slot-id="<?php echo $slot['slot_id']; ?>"
                     data-slot-type="<?php echo $slot['slot_type']; ?>">
                    <div class="slot-header">
                        <span class="slot-number"><?php echo $slot['slot_number']; ?></span>
                        <span class="slot-type">❄️</span>
                    </div>
                    <div class="slot-content">
                        <?php if ($slot['product_id']): ?>
                            <div class="product-name"><?php echo htmlspecialchars($slot['product_name']); ?></div>
                            <div class="stock-info">
                                Voorraad: <?php echo $slot['product_stock']; ?>
                            </div>
                            <button class="remove-product" data-slot-id="<?php echo $slot['slot_id']; ?>">
                                🗑️ Verwijder
                            </button>
                        <?php else: ?>
                            <div class="empty-slot">Leeg</div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Ongekoelde vakken sectie -->
        <div class="section-header">Ongekoelde Vakken</div>
        <div class="uncooled-slots">
            <?php 
            $uncooledSlots = array_filter($slots, function($slot) {
                return $slot['slot_type'] === 'UNCOOLED';
            });
            foreach ($uncooledSlots as $slot): ?>
                <div class="slot <?php echo $slot['status']; ?>"
                     data-slot-id="<?php echo $slot['slot_id']; ?>"
                     data-slot-type="<?php echo $slot['slot_type']; ?>">
                    <div class="slot-header">
                        <span class="slot-number"><?php echo $slot['slot_number']; ?></span>
                    </div>
                    <div class="slot-content">
                        <?php if ($slot['product_id']): ?>
                            <div class="product-name"><?php echo htmlspecialchars($slot['product_name']); ?></div>
                            <div class="stock-info">
                                Voorraad: <?php echo $slot['product_stock']; ?>
                            </div>
                            <button class="remove-product" data-slot-id="<?php echo $slot['slot_id']; ?>">
                                🗑️ Verwijder
                            </button>
                        <?php else: ?>
                            <div class="empty-slot">Leeg</div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Product selectie formulier -->
    <div class="product-selection hidden">
        <div class="selection-header">
            <h3>Selecteer Product voor Vak <span class="selected-slot-number"></span></h3>
            <div class="filter-options">
                <label>
                    <input type="checkbox" id="cooled-only" class="cooled-filter">
                    Alleen gekoelde producten
                </label>
            </div>
        </div>
        
        <div class="products-list">
            <!-- Wordt dynamisch gevuld met beschikbare producten -->
        </div>

        <div class="selection-actions">
            <button class="btn-cancel">Annuleren</button>
            <button class="btn-fill" disabled>Vak Vullen</button>
        </div>
    </div>

    <!-- Bevestigingsdialoog -->
    <div class="confirmation-dialog hidden">
        <div class="dialog-content">
            <p class="dialog-message"></p>
            <div class="dialog-buttons">
                <button class="btn-cancel">Annuleren</button>
                <button class="btn-confirm">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- Laad slot-specifieke JavaScript -->
<script src="public/js/slots.js"></script> 