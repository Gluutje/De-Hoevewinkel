document.addEventListener('DOMContentLoaded', function() {
    let selectedSlot = null;
    let selectedProduct = null;
    let pendingAction = null;
    
    // Laad initiële vak data
    loadSlots();

    // Event listeners voor vak interactie
    document.querySelector('.slots-grid').addEventListener('click', function(e) {
        const slot = e.target.closest('.slot');
        if (!slot) return;

        // Als er al een vak open is, sluit deze eerst
        if (selectedSlot && selectedSlot !== slot) {
            document.querySelector('.product-selection').classList.add('hidden');
        }

        selectedSlot = slot;
        showProductSelection(slot);
    });

    // Event listeners voor product selectie
    document.querySelector('.products-list').addEventListener('click', function(e) {
        const productItem = e.target.closest('.product-item');
        if (!productItem) return;

        // Verwijder vorige selectie
        document.querySelectorAll('.product-item.selected').forEach(item => {
            item.classList.remove('selected');
        });

        // Selecteer nieuw product
        productItem.classList.add('selected');
        selectedProduct = productItem.dataset.productId;
        
        // Enable vul knop
        document.querySelector('.btn-fill').disabled = false;
    });

    // Event listeners voor knoppen
    document.querySelector('.btn-cancel').addEventListener('click', closeProductSelection);
    document.querySelector('.btn-fill').addEventListener('click', fillSlot);

    // Verwijder product functionaliteit
    document.querySelectorAll('.remove-product').forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation(); // Voorkom dat het slot opent bij het klikken op verwijderen
            const slotId = this.dataset.slotId;
            
            showConfirmDialog(
                'Weet je zeker dat je dit product wilt verwijderen?',
                () => removeProductFromSlot(slotId)
            );
        });
    });

    // Event listeners voor bevestigingsdialoog
    const confirmationDialog = document.querySelector('.confirmation-dialog');
    document.querySelector('.confirmation-dialog .btn-cancel').addEventListener('click', () => {
        confirmationDialog.classList.add('hidden');
        pendingAction = null;
    });

    document.querySelector('.confirmation-dialog .btn-confirm').addEventListener('click', () => {
        confirmationDialog.classList.add('hidden');
        if (pendingAction) {
            pendingAction();
            pendingAction = null;
        }
    });

    // Functies
    function loadSlots() {
        fetch('/?route=slots/getAll')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderSlots(data.cooled, '.cooled-slots');
                    renderSlots(data.uncooled, '.uncooled-slots');
                } else {
                    showMessage(data.message || 'Er is een fout opgetreden bij het ophalen van de vakken.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('Er is een fout opgetreden bij het ophalen van de vakken.', 'error');
            });
    }

    function renderSlots(slots, containerSelector) {
        const container = document.querySelector(containerSelector);
        container.innerHTML = slots.map(slot => `
            <div class="slot ${slot.status}" 
                 data-slot-id="${slot.slot_id}"
                 data-slot-type="${slot.slot_type}">
                <div class="slot-header">
                    <span class="slot-number">${slot.slot_number}</span>
                    ${slot.slot_type === 'COOLED' ? '<span class="slot-type">❄️</span>' : ''}
                </div>
                <div class="slot-content">
                    ${slot.product_id 
                        ? `<div class="product-name">${slot.product_name}</div>
                           <div class="stock-info">
                               Voorraad: ${slot.product_stock}
                           </div>
                           <button class="remove-product" data-slot-id="${slot.slot_id}">
                               🗑️ Verwijder
                           </button>`
                        : '<div class="empty-slot">Leeg</div>'}
                </div>
            </div>
        `).join('');

        // Hervoeg event listeners voor verwijder knoppen
        container.querySelectorAll('.remove-product').forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                const slotId = this.dataset.slotId;
                showConfirmDialog(
                    'Weet je zeker dat je dit product wilt verwijderen?',
                    () => removeProductFromSlot(slotId)
                );
            });
        });
    }

    function showProductSelection(slot) {
        // Update slot nummer in titel
        document.querySelector('.selected-slot-number').textContent = 
            slot.querySelector('.slot-number').textContent;

        // Laad beschikbare producten
        const isCooledSlot = slot.dataset.slotType === 'COOLED';
        loadAvailableProducts(isCooledSlot);

        // Toon selectie interface
        document.querySelector('.product-selection').classList.remove('hidden');
    }

    function loadAvailableProducts(isCooledSlot) {
        fetch('/?route=products/getAvailable')
            .then(response => response.json())
            .then(products => {
                renderProducts(products, isCooledSlot);
            });
    }

    function renderProducts(products, isCooledSlot) {
        const container = document.querySelector('.products-list');
        container.innerHTML = products
            .filter(product => Boolean(product.requires_cooling) === isCooledSlot)
            .map(product => `
                <div class="product-item" data-product-id="${product.product_id}">
                    <div class="product-info">
                        <div class="product-name">${product.name}</div>
                        <div class="product-details">
                            €${product.price} per ${product.unit}
                            ${product.requires_cooling ? ' ❄️' : ''}
                        </div>
                    </div>
                </div>
            `).join('');
    }

    function closeProductSelection() {
        // Verberg selectie interface
        document.querySelector('.product-selection').classList.add('hidden');

        // Reset selecties
        selectedSlot = null;
        selectedProduct = null;
        document.querySelector('.btn-fill').disabled = true;
    }

    function fillSlot() {
        if (!selectedSlot || !selectedProduct) return;

        const slotId = selectedSlot.dataset.slotId;
        
        fetch('/?route=slots/fill', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                slot_id: slotId,
                product_id: selectedProduct
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Herlaad vakken voor real-time update
                loadSlots();
                // Sluit selectie interface
                closeProductSelection();
                // Toon succes melding
                showMessage(data.message, 'success');
            } else {
                // Toon foutmelding
                showMessage(data.message, 'error');
            }
        })
        .catch(error => {
            showMessage('Er is een fout opgetreden bij het vullen van het vak', 'error');
        });
    }

    async function removeProductFromSlot(slotId) {
        try {
            const response = await fetch('/?route=slots/remove', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ slot_id: slotId })
            });

            const result = await response.json();
            
            if (result.success) {
                // Herlaad vakken voor real-time update
                loadSlots();
                // Toon succes melding
                showMessage(result.message, 'success');
            } else {
                // Toon foutmelding
                showMessage(result.message || 'Er is een fout opgetreden bij het verwijderen van het product.', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showMessage('Er is een fout opgetreden bij het verwijderen van het product.', 'error');
        }
    }

    function showConfirmDialog(message, onConfirm) {
        const dialog = document.querySelector('.confirmation-dialog');
        dialog.querySelector('.dialog-message').textContent = message;
        dialog.classList.remove('hidden');
        pendingAction = onConfirm;
    }

    // Helper functie voor het tonen van meldingen
    function showMessage(message, type = 'info') {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${type}`;
        messageDiv.textContent = message;
        
        // Voeg de melding toe aan het automaat-scherm
        const automaat = document.querySelector('.vending-machine') || document.querySelector('.admin-panel');
        if (automaat) {
            automaat.appendChild(messageDiv);
        } else {
            document.body.appendChild(messageDiv);
        }
        
        // Verwijder melding na 3 seconden
        setTimeout(() => {
            messageDiv.remove();
        }, 3000);
    }
}); 