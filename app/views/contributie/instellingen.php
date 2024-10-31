<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contributie Instellingen - Ledenadministratie</title>
    <!-- Link naar de externe CSS-stylesheet voor de opmaak van de pagina -->
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
    <div class="familie-container">
        <!-- Terugknop om naar het penningmeester-dashboard te gaan -->
        <a href="/dashboard/penningmeester" class="btn-back">Terug naar Dashboard</a>
        <h1>Contributie Instellingen</h1>

        <!-- Toon een foutmelding als deze is ingesteld -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Toon een succesmelding als deze is ingesteld -->
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Toon een waarschuwing als er geen actief boekjaar is ingesteld -->
        <?php if (!$actiefBoekjaar): ?>
            <div class="alert alert-danger">Er is geen actief boekjaar. Activeer eerst een boekjaar.</div>
        <?php else: ?>
            <!-- Titel en informatie over het actieve boekjaar -->
            <h2>Contributie instellingen voor boekjaar <?php echo htmlspecialchars($actiefBoekjaar['jaar']); ?></h2>

            <!-- Formulier om de contributie-instellingen te bewerken -->
            <form action="/contributies/instellingen" method="post" class="familie-form">
                <div class="form-group">
                    <!-- Invoerveld voor het basisbedrag met uitleg -->
                    <label for="basisbedrag">Basisbedrag:</label>
                    <input type="number" id="basisbedrag" name="basisbedrag" class="form-control" required min="0" step="0.01" value="100.00">
                    <small class="form-text text-muted">Dit is het standaard contributiebedrag zonder kortingen</small>
                </div>

                <!-- Overzicht van de huidige contributiebedragen met kortingen -->
                <div class="contributie-overzicht">
                    <h3>Huidige Contributiebedragen</h3>
                    <table class="familie-table" id="contributieTable">
                        <thead>
                            <tr>
                                <th>Soort Lid</th>
                                <th>Leeftijdscategorie</th>
                                <th>Korting</th>
                                <th>Bedrag</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Rijen voor elke soort lid met de respectievelijke kortingen en bedragen -->
                            <tr data-korting="0.50" data-soort="jeugd">
                                <td>Jeugd</td>
                                <td>tot 7 jaar</td>
                                <td>50%</td>
                                <td class="bedrag">€ 0.00</td>
                            </tr>
                            <tr data-korting="0.40" data-soort="aspirant">
                                <td>Aspirant</td>
                                <td>tot 12 jaar</td>
                                <td>40%</td>
                                <td class="bedrag">€ 0.00</td>
                            </tr>
                            <tr data-korting="0.25" data-soort="junior">
                                <td>Junior</td>
                                <td>tot 17 jaar</td>
                                <td>25%</td>
                                <td class="bedrag">€ 0.00</td>
                            </tr>
                            <tr data-korting="0.00" data-soort="senior">
                                <td>Senior</td>
                                <td>tot 50 jaar</td>
                                <td>0%</td>
                                <td class="bedrag">€ 0.00</td>
                            </tr>
                            <tr data-korting="0.45" data-soort="oudere">
                                <td>Oudere</td>
                                <td>Vanaf 51 jaar</td>
                                <td>45%</td>
                                <td class="bedrag">€ 0.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Submitknop om de instellingen op te slaan en de bedragen te berekenen -->
                <button type="submit" class="btn">Instellingen Opslaan en Contributies Berekenen</button>
            </form>
        <?php endif; ?>
    </div>

    <!-- JavaScript om de contributiebedragen automatisch bij te werken -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const basisbedragInput = document.getElementById('basisbedrag');
            const contributieRows = document.querySelectorAll('#contributieTable tbody tr');

            // Functie om de bedragen te berekenen op basis van het basisbedrag en de kortingen
            function updateBedragen() {
                const basisbedrag = parseFloat(basisbedragInput.value) || 0;
                
                contributieRows.forEach(row => {
                    const korting = parseFloat(row.dataset.korting);
                    const bedrag = basisbedrag * (1 - korting);
                    row.querySelector('.bedrag').textContent = `€ ${bedrag.toFixed(2)}`;
                });
            }

            // Update bedragen bij het laden van de pagina
            updateBedragen();

            // Update bedragen wanneer het basisbedrag wordt aangepast
            basisbedragInput.addEventListener('input', updateBedragen);
        });
    </script>
</body>
</html>
