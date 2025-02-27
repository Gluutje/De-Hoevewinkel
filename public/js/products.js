document.addEventListener('DOMContentLoaded', function() {
    // Nieuwe product formulier functionaliteit
    const btnNewProduct = document.getElementById('btnNewProduct');
    const productForm = document.getElementById('productForm');
    const btnCancel = document.getElementById('btnCancel');
    const btnSave = document.getElementById('btnSave');
    const productsList = document.querySelector('.products-list');

    // Toon formulier
    btnNewProduct.addEventListener('click', () => {
        productsList.style.display = 'none';
        btnNewProduct.style.display = 'none';
        productForm.style.display = 'flex';
    });

    // Verberg formulier
    btnCancel.addEventListener('click', () => {
        productForm.style.display = 'none';
        btnNewProduct.style.display = 'flex';
        productsList.style.display = 'block';
        resetForm();
        // Reset form mode
        productForm.dataset.mode = 'create';
        delete productForm.dataset.productId;
    });

    // Sla product op
    btnSave.addEventListener('click', async () => {
        // Valideer form
        const formData = getFormData();
        if (!validateForm(formData)) {
            return;
        }

        const isEdit = productForm.dataset.mode === 'edit';
        const url = isEdit ? '/?route=products/update' : '/?route=products/create';
        
        // Voeg product ID toe bij edit
        if (isEdit) {
            formData.product_id = productForm.dataset.productId;
        }

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(formData).toString()
            });

            const result = await response.json();

            if (result.success) {
                showMessage(isEdit ? 'Product succesvol bijgewerkt' : 'Product succesvol toegevoegd');
                // Herlaad de pagina om de nieuwe data te tonen
                setTimeout(() => window.location.reload(), 2000);
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            showMessage(error.message || 'Er is een fout opgetreden', 'error');
        }
    });

    // Helper functies
    function getFormData() {
        const unitAmount = document.getElementById('unitAmount').value;
        const unitType = document.getElementById('unitType').value;
        
        return {
            name: document.getElementById('productName').value,
            price: document.getElementById('productPrice').value,
            unit: `${unitAmount}${unitType}`, // Combineer aantal en type, bijv. "200GR" of "1KG"
            category: document.getElementById('productCategory').value,
            description: document.getElementById('productDescription').value,
            requires_cooling: document.getElementById('productCategory').value === 'Gekoeld'
        };
    }

    function validateForm(data) {
        if (!data.name.trim()) {
            showMessage('Vul een productnaam in', 'error');
            return false;
        }
        if (!data.price || data.price <= 0) {
            showMessage('Vul een geldige prijs in', 'error');
            return false;
        }
        const unitAmount = document.getElementById('unitAmount').value;
        if (!unitAmount || unitAmount <= 0) {
            showMessage('Vul een geldige hoeveelheid in', 'error');
            return false;
        }
        return true;
    }

    function resetForm() {
        document.getElementById('productName').value = '';
        document.getElementById('productPrice').value = '';
        document.getElementById('unitAmount').value = '1';
        document.getElementById('unitType').value = 'STUK';
        document.getElementById('productCategory').value = 'Ongekoeld';
        document.getElementById('productDescription').value = '';
    }

    // Delete product handler
    document.querySelectorAll('.action-btn.delete').forEach(button => {
        button.addEventListener('click', async function(e) {
            e.preventDefault();
            
            const row = this.closest('.product-row');
            const productId = row.dataset.productId;
            const productName = row.querySelector('td').textContent;
            
            // Toon bevestigingsdialog
            const confirmed = await showConfirmDialog(
                `Weet je zeker dat je het product "${productName}" wilt verwijderen?`
            );
            
            if (!confirmed) {
                return;
            }
            
            // Voeg deleting class toe voor visuele feedback
            row.classList.add('deleting');
            
            try {
                const response = await fetch('/?route=products/delete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `product_id=${productId}`
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Verwijder rij met animatie
                    row.style.transition = 'all 0.3s';
                    row.style.opacity = '0';
                    row.style.height = '0';
                    setTimeout(() => row.remove(), 300);
                    
                    showMessage('Product succesvol verwijderd', 'success');
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                // Verwijder deleting class bij error
                row.classList.remove('deleting');
                showMessage(error.message || 'Er is een fout opgetreden', 'error');
            }
        });
    });
    
    // Toon bevestigingsdialog
    function showConfirmDialog(message) {
        return new Promise((resolve) => {
            const dialog = document.createElement('div');
            dialog.className = 'confirm-dialog';
            dialog.innerHTML = `
                <p>${message}</p>
                <div class="confirm-buttons">
                    <button class="confirm-button cancel">Annuleren</button>
                    <button class="confirm-button confirm">OK</button>
                </div>
            `;
            
            // Voeg toe aan screen-content
            const screenContent = document.querySelector('.screen-content');
            screenContent.appendChild(dialog);
            
            // Event handlers voor de knoppen
            dialog.querySelector('.cancel').addEventListener('click', () => {
                dialog.remove();
                resolve(false);
            });
            
            dialog.querySelector('.confirm').addEventListener('click', () => {
                dialog.remove();
                resolve(true);
            });
        });
    }
    
    // Toon feedback berichten
    function showMessage(message, type = 'success') {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${type}`;
        messageDiv.textContent = message;
        
        // Voeg toe aan de screen-content container
        const screenContent = document.querySelector('.screen-content');
        if (screenContent) {
            screenContent.appendChild(messageDiv);
            
            // Verwijder na animatie
            messageDiv.addEventListener('animationend', () => {
                messageDiv.remove();
            });
        }
    }

    // Edit product handler
    document.querySelectorAll('.action-btn.edit').forEach(button => {
        button.addEventListener('click', async function(e) {
            e.preventDefault();
            
            const row = this.closest('.product-row');
            const productId = row.dataset.productId;
            
            try {
                // Haal product details op
                const response = await fetch(`/?route=products/get&id=${productId}`);
                const result = await response.json();
                
                if (result.success) {
                    // Vul formulier met product data
                    fillProductForm(result.product);
                    
                    // Toon formulier
                    productsList.style.display = 'none';
                    btnNewProduct.style.display = 'none';
                    productForm.style.display = 'flex';
                    
                    // Update form mode
                    productForm.dataset.mode = 'edit';
                    productForm.dataset.productId = productId;
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                showMessage(error.message || 'Er is een fout opgetreden', 'error');
            }
        });
    });

    // Helper functie om formulier te vullen
    function fillProductForm(product) {
        document.getElementById('productName').value = product.name;
        document.getElementById('productPrice').value = product.price;
        
        // Split unit in aantal en type (bijv. "200GR" -> "200" en "GR")
        const unitMatch = product.unit.match(/(\d+)([A-Z]+)/);
        if (unitMatch) {
            document.getElementById('unitAmount').value = unitMatch[1];
            document.getElementById('unitType').value = unitMatch[2];
        }
        
        document.getElementById('productCategory').value = product.requires_cooling ? 'Gekoeld' : 'Ongekoeld';
        document.getElementById('productDescription').value = product.description || '';
    }
}); 