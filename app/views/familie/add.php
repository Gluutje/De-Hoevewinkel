<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nieuwe Familie Toevoegen - Ledenadministratie</title>
    <!-- Link naar de externe CSS-stylesheet voor de opmaak van de pagina -->
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
    <div class="familie-container">
        <!-- Terugknop om naar het overzicht van families te gaan -->
        <a href="/families" class="btn-back">Terug naar Overzicht</a>
        <h1>Nieuwe Familie Toevoegen</h1>

        <!-- Toon een foutmelding als deze is ingesteld -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Formulier om een nieuwe familie toe te voegen -->
        <form action="/families/add" method="post" class="familie-form">
            <div class="form-group">
                <!-- Invoerveld voor de familienaam, met validatie voor alleen letters, spaties en koppeltekens -->
                <label for="naam">Familienaam:</label>
                <input type="text" id="naam" name="naam" class="form-control" required pattern="[A-Za-z\s-]+" title="Alleen letters, spaties en koppeltekens zijn toegestaan">
            </div>
            <div class="form-group">
                <!-- Invoerveld voor de straatnaam, met dezelfde validatie als de familienaam -->
                <label for="straatnaam">Straatnaam:</label>
                <input type="text" id="straatnaam" name="straatnaam" class="form-control" required pattern="[A-Za-z\s-]+" title="Alleen letters, spaties en koppeltekens zijn toegestaan">
            </div>
            <div class="form-group">
                <!-- Invoerveld voor het huisnummer, met validatie voor cijfers en optionele letters -->
                <label for="huisnummer">Huisnummer:</label>
                <input type="text" id="huisnummer" name="huisnummer" class="form-control" required pattern="[0-9]+[A-Za-z]?" title="Voer een geldig huisnummer in (bijv. 42 of 42A)">
            </div>
            <div class="form-group">
                <!-- Invoerveld voor de postcode, met validatie voor een Nederlandse postcode -->
                <label for="postcode">Postcode:</label>
                <input type="text" id="postcode" name="postcode" class="form-control" required pattern="[1-9][0-9]{3}\s?[A-Za-z]{2}" title="Voer een geldige Nederlandse postcode in (bijv. 1234 AB)">
            </div>
            <div class="form-group">
                <!-- Invoerveld voor de plaats, met dezelfde validatie als de familienaam en straatnaam -->
                <label for="plaats">Plaats:</label>
                <input type="text" id="plaats" name="plaats" class="form-control" required pattern="[A-Za-z\s-]+" title="Alleen letters, spaties en koppeltekens zijn toegestaan">
            </div>
            <!-- Submitknop om het formulier in te dienen -->
            <button type="submit" class="btn">Familie Toevoegen</button>
        </form>
    </div>
</body>
</html>
