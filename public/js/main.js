document.addEventListener('DOMContentLoaded', function() {
    // Update datum en tijd
    function updateDateTime() {
        const now = new Date();
        const options = { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        document.getElementById('datetime').textContent = now.toLocaleDateString('nl-NL', options);
    }

    // Update automaat status - maak globaal beschikbaar
    window.updateMachineStatus = function() {
        fetch('/?route=slots/getAll')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updatePhysicalMachine(data.cooled, data.uncooled);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Update de fysieke automaat weergave
    function updatePhysicalMachine(cooledSlots, uncooledSlots) {
        // Update gekoelde vakken
        const cooledContainer = document.querySelector('.physical-machine .cooled-section');
        for (let i = 1; i <= 8; i++) {
            const slot = cooledSlots.find(s => s.slot_number == i);
            updateSlot(cooledContainer, i, slot);
        }

        // Update ongekoelde vakken
        const uncooledContainer = document.querySelector('.physical-machine .slots-container:not(.cooled-section)');
        for (let i = 9; i <= 16; i++) {
            const slot = uncooledSlots.find(s => s.slot_number == i);
            updateSlot(uncooledContainer, i, slot);
        }
    }

    // Update een individueel vak
    function updateSlot(container, slotNumber, slotData) {
        const slotElement = container.querySelector(`[data-slot-number="${slotNumber}"]`);
        if (!slotElement) return;

        const isFilled = slotData && slotData.status === 'FILLED';
        
        // Update classes
        slotElement.className = `slot ${isFilled ? 'FILLED' : 'EMPTY'}`;
        
        // Update content
        const contentElement = slotElement.querySelector('.slot-content');
        contentElement.textContent = isFilled ? slotData.product_name : 'Leeg';
        
        // Update stock info
        const stockElement = slotElement.querySelector('.slot-stock');
        stockElement.textContent = isFilled ? `${slotData.current_stock} beschikbaar` : '';
    }

    // Event listeners voor navigatie knoppen
    document.getElementById('btnHome').addEventListener('click', () => {
        window.location.href = '/';
    });

    document.getElementById('btnBack').addEventListener('click', () => {
        window.history.back();
    });

    document.getElementById('btnAdmin').addEventListener('click', () => {
        window.location.href = '/?route=admin';
    });

    // Klik feedback voor knoppen
    document.querySelectorAll('button').forEach(button => {
        button.addEventListener('mousedown', function() {
            this.style.transform = 'scale(0.95)';
        });

        button.addEventListener('mouseup', function() {
            this.style.transform = 'scale(1)';
        });

        // Ook resetten bij mouse leave
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });

    // Voorkom context menu op rechtermuisklik
    document.addEventListener('contextmenu', function(event) {
        event.preventDefault();
    });

    // Start de updates
    updateDateTime();
    setInterval(updateDateTime, 60000); // Update tijd elke minuut
    window.updateMachineStatus(); // Direct eerste update
    setInterval(window.updateMachineStatus, 2000); // Update automaat elke 2 seconden
}); 