<?php
// Check of gebruiker is ingelogd
if (!isset($_SESSION['admin'])) {
    header('Location: /?route=admin/login');
    exit;
}
?>

<!-- Laad wisselgeld-specifieke CSS -->
<link rel="stylesheet" href="public/css/change.css">

<div class="admin-change">
    <h2>Wisselgeld Beheren</h2>

    <!-- Totaal wisselgeld -->
    <div class="total-amount">
        <div class="amount-label">Totaal wisselgeld in automaat:</div>
        <div class="amount-value">€<?php echo number_format($totalAmount, 2); ?></div>
    </div>

    <!-- Wisselgeld overzicht -->
    <div class="change-management">
        <div class="money-units-grid">
            <?php foreach ($moneyUnits as $unit): ?>
                <div class="money-unit" data-id="<?php echo $unit['cash_id']; ?>" 
                     data-denomination="<?php echo $unit['denomination']; ?>"
                     data-min="<?php echo $unit['minimum_required']; ?>"
                     data-max="<?php echo $unit['maximum_allowed']; ?>"
                     data-current="<?php echo $unit['current_stock']; ?>">
                    <div class="unit-value">
                        €<?php echo number_format($unit['denomination'], 2); ?>
                    </div>
                    <div class="unit-stock">
                        <div class="stock-display">
                            <span class="current-stock"><?php echo $unit['current_stock']; ?></span>
                            <span class="stock-label">stuks</span>
                        </div>
                        <div class="stock-limits">
                            <small>Min: <?php echo $unit['minimum_required']; ?></small>
                            <small>Max: <?php echo $unit['maximum_allowed']; ?></small>
                        </div>
                        <button class="manage-btn">Beheren</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Beheer modal -->
    <div class="modal hidden">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Wisselgeld Beheren - €<span class="selected-denomination"></span></h3>
            </div>
            <div class="modal-body">
                <div class="step step-1">
                    <label>Aantal:</label>
                    <input type="number" class="amount-input" min="1" max="100" value="10">
                    <div class="stock-info">
                        <p>Huidige voorraad: <span class="current-amount"></span> stuks</p>
                        <p>Minimum: <span class="min-amount"></span> stuks</p>
                        <p>Maximum: <span class="max-amount"></span> stuks</p>
                    </div>
                </div>
                <div class="step step-2">
                    <label>Actie:</label>
                    <div class="action-buttons">
                        <button class="action-btn remove-btn" data-action="remove">
                            <span class="icon">↓</span> Legen
                        </button>
                        <button class="action-btn add-btn" data-action="add">
                            <span class="icon">↑</span> Vullen
                        </button>
                    </div>
                </div>
                <div class="step step-3 hidden">
                    <div class="confirmation-message"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="cancel-btn">Annuleren</button>
                <button class="confirm-btn hidden">Bevestigen</button>
            </div>
        </div>
    </div>
</div>

<style>
.admin-change {
    padding: 20px;
}

.admin-change h2 {
    color: var(--primary-color);
    margin-bottom: 20px;
    text-align: center;
}

.total-amount {
    background: white;
    padding: 20px;
    border-radius: var(--border-radius);
    margin-bottom: 20px;
    text-align: center;
}

.amount-label {
    font-size: 16px;
    color: var(--text-color);
    margin-bottom: 5px;
}

.amount-value {
    font-size: 28px;
    font-weight: bold;
    color: var(--primary-color);
}

.change-management {
    background: white;
    padding: 20px;
    border-radius: var(--border-radius);
    margin-bottom: 20px;
}

.money-units-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 15px;
    padding: 10px;
}

.money-unit {
    background: var(--background-color);
    padding: 15px;
    border-radius: var(--border-radius);
    text-align: center;
}

.unit-value {
    font-size: 24px;
    font-weight: bold;
    color: var(--primary-color);
    margin-bottom: 10px;
}

.unit-stock {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}

.stock-display {
    display: flex;
    align-items: baseline;
    gap: 5px;
}

.current-stock {
    font-size: 20px;
    font-weight: bold;
    color: var(--text-color);
}

.stock-label {
    font-size: 14px;
    color: var(--text-color);
}

.stock-limits {
    display: flex;
    gap: 10px;
    font-size: 12px;
    color: var(--text-color);
}

.manage-btn {
    background: var(--primary-color);
    color: white;
    border: none;
    padding: 8px 20px;
    border-radius: var(--border-radius);
    cursor: pointer;
    transition: all 0.2s ease;
}

.manage-btn:hover {
    background: var(--hover-color);
    transform: translateY(-2px);
}

/* Modal styles */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal.hidden {
    display: none;
}

.modal-content {
    background: white;
    border-radius: var(--border-radius);
    width: 90%;
    max-width: 400px;
}

.modal-header {
    padding: 15px 20px;
    border-bottom: 1px solid #ddd;
}

.modal-header h3 {
    margin: 0;
    color: var(--primary-color);
}

.modal-body {
    padding: 20px;
}

.modal-footer {
    padding: 15px 20px;
    border-top: 1px solid #ddd;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.step {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.step.hidden {
    display: none;
}

.amount-input {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: var(--border-radius);
    text-align: center;
    font-size: 16px;
}

.stock-info {
    background: var(--background-color);
    padding: 10px;
    border-radius: var(--border-radius);
}

.stock-info p {
    margin: 5px 0;
    font-size: 14px;
}

.action-buttons {
    display: flex;
    gap: 10px;
}

.action-btn {
    flex: 1;
    padding: 10px;
    border: none;
    border-radius: var(--border-radius);
    background: var(--primary-color);
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    transition: all 0.2s ease;
}

.action-btn:hover {
    transform: translateY(-2px);
}

.action-btn.remove-btn {
    background: #dc3545;
}

.action-btn.remove-btn:hover {
    background: #c82333;
}

.cancel-btn, .confirm-btn {
    padding: 8px 20px;
    border: none;
    border-radius: var(--border-radius);
    cursor: pointer;
    transition: all 0.2s ease;
}

.cancel-btn {
    background: #6c757d;
    color: white;
}

.confirm-btn {
    background: var(--primary-color);
    color: white;
}

.cancel-btn:hover, .confirm-btn:hover {
    transform: translateY(-2px);
}

.confirmation-message {
    text-align: center;
    padding: 20px;
    font-size: 16px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.querySelector('.modal');
    const moneyUnits = document.querySelectorAll('.money-unit');
    let selectedUnit = null;
    let selectedAction = null;
    
    // Event listeners voor beheer knoppen
    moneyUnits.forEach(unit => {
        const manageBtn = unit.querySelector('.manage-btn');
        manageBtn.addEventListener('click', () => openModal(unit));
    });

    // Modal sluiten
    document.querySelector('.cancel-btn').addEventListener('click', closeModal);

    // Actie knoppen
    document.querySelectorAll('.action-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            selectedAction = this.dataset.action;
            showConfirmation();
        });
    });

    // Bevestig knop
    document.querySelector('.confirm-btn').addEventListener('click', executeAction);

    function openModal(unit) {
        selectedUnit = unit;
        const denomination = unit.dataset.denomination;
        const current = unit.dataset.current;
        const min = unit.dataset.min;
        const max = unit.dataset.max;

        // Update modal inhoud
        modal.querySelector('.selected-denomination').textContent = denomination;
        modal.querySelector('.current-amount').textContent = current;
        modal.querySelector('.min-amount').textContent = min;
        modal.querySelector('.max-amount').textContent = max;

        // Reset modal state
        modal.querySelector('.step-3').classList.add('hidden');
        modal.querySelector('.confirm-btn').classList.add('hidden');
        modal.querySelector('.step-1').classList.remove('hidden');
        modal.querySelector('.step-2').classList.remove('hidden');

        // Toon modal
        modal.classList.remove('hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
        selectedUnit = null;
        selectedAction = null;
    }

    function showConfirmation() {
        const amount = modal.querySelector('.amount-input').value;
        const denomination = selectedUnit.dataset.denomination;
        const current = parseInt(selectedUnit.dataset.current);
        const max = parseInt(selectedUnit.dataset.max);
        
        // Valideer actie
        if (selectedAction === 'remove' && amount > current) {
            alert('Er kunnen niet meer munten/biljetten worden verwijderd dan er aanwezig zijn.');
            return;
        }
        
        if (selectedAction === 'add' && (current + parseInt(amount)) > max) {
            alert('De nieuwe voorraad zou het maximum overschrijden.');
            return;
        }

        // Update bevestigingsbericht
        const action = selectedAction === 'add' ? 'vullen met' : 'legen van';
        const message = `Weet u zeker dat u €${denomination} wilt ${action} ${amount} stuks?`;
        modal.querySelector('.confirmation-message').textContent = message;

        // Toon bevestigingsstap
        modal.querySelector('.step-1').classList.add('hidden');
        modal.querySelector('.step-2').classList.add('hidden');
        modal.querySelector('.step-3').classList.remove('hidden');
        modal.querySelector('.confirm-btn').classList.remove('hidden');
    }

    async function executeAction() {
        const amount = parseInt(modal.querySelector('.amount-input').value);
        const current = parseInt(selectedUnit.dataset.current);
        const cashId = selectedUnit.dataset.id;
        const denomination = selectedUnit.dataset.denomination;
        
        // Bereken nieuwe voorraad
        const newStock = selectedAction === 'add' ? current + amount : current - amount;

        try {
            const response = await fetch('/?route=admin/updateCashStock', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    cash_id: cashId,
                    new_stock: newStock
                })
            });

            const result = await response.json();
            
            if (result.success) {
                const action = selectedAction === 'add' ? 'gevuld' : 'geleegd';
                const message = `€${denomination} succesvol ${action} met ${amount} stuks.`;
                modal.querySelector('.confirmation-message').textContent = message;
                
                // Verberg bevestigingsknop na succesvolle actie
                modal.querySelector('.confirm-btn').classList.add('hidden');
                
                // Ververs de pagina na 2 seconden
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                alert(result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Er is een fout opgetreden bij het bijwerken van de voorraad');
        }
    }
});
</script> 