<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nieuwe Contributie Toevoegen - Ledenadministratie</title>
    <!-- Link naar de externe CSS-stylesheet voor de opmaak van de pagina -->
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
    <div class="familie-container">
        <!-- Terugknop om naar het overzicht van de contributies te gaan -->
        <a href="/contributies" class="btn-back">Terug naar Overzicht</a>
        <h1>Nieuwe Contributie Toevoegen</h1>

        <!-- Toon een foutmelding als deze is ingesteld -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Formulier om een nieuwe contributie toe te voegen -->
        <form action="/contributies/add" method="post" class="familie-form">
            <div class="form-group">
                <!-- Invoerveld voor de leeftijd, met een minimale en maximale waarde -->
                <label for="leeftijd">Leeftijd:</label>
                <input type="number" id="leeftijd" name="leeftijd" class="form-control" required min="0" max="150">
            </div>
            <div class="form-group">
                <!-- Dropdownmenu om het soort lid te selecteren -->
                <label for="soort_lid_id">Soort Lid:</label>
                <select id="soort_lid_id" name="soort_lid_id" class="form-control" required>
                    <?php foreach ($soortLeden as $soortLid): ?>
                        <option value="<?php echo $soortLid['id']; ?>"><?php echo htmlspecialchars($soortLid['omschrijving']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <!-- Invoerveld voor het bedrag, met een minimale waarde en stapgrootte -->
                <label for="bedrag">Bedrag:</label>
                <input type="number" id="bedrag" name="bedrag" class="form-control" required min="0" step="0.01">
            </div>
            <div class="form-group">
                <!-- Dropdownmenu om het boekjaar te selecteren -->
                <label for="boekjaar_id">Boekjaar:</label>
                <select id="boekjaar_id" name="boekjaar_id" class="form-control" required>
                    <?php foreach ($boekjaren as $boekjaar): ?>
                        <option value="<?php echo $boekjaar['id']; ?>"><?php echo htmlspecialchars($boekjaar['jaar']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Submitknop om het formulier in te dienen -->
            <button type="submit" class="btn">Contributie Toevoegen</button>
        </form>
    </div>
</body>
</html>

