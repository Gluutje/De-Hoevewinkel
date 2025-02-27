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
    
    // Update tijd elke minuut
    updateDateTime();
    setInterval(updateDateTime, 60000);

    // Navigatie knoppen
    const btnHome = document.getElementById('btnHome');
    const btnBack = document.getElementById('btnBack');
    const btnAdmin = document.getElementById('btnAdmin');

    if (btnHome) {
        btnHome.addEventListener('click', function() {
            window.location.href = '/?route=home';
        });
    }

    if (btnBack) {
        btnBack.addEventListener('click', function() {
            window.history.back();
        });
    }

    if (btnAdmin) {
        btnAdmin.addEventListener('click', function() {
            window.location.href = '/?route=admin/login';
        });
    }

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
}); 