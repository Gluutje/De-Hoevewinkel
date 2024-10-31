<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contributie Verwijderen - Ledenadministratie</title>
    <!-- Link naar de externe CSS-stylesheet voor de opmaak van de pagina -->
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
    <div class="familie-container">
        <!-- Terugknop om naar het overzicht van de contributies te gaan -->
        <a href="/contributies" class="btn-back">Terug naar Overzicht</a>
        <h1>Contributie Verwijderen</h1>

        <!-- Bevestigingsbericht met details over de contributie die verwijderd wordt -->
        <p>Weet u zeker dat u deze contributie wilt verwijderen?</p>
        <p>Leeftijd: <?php echo htmlspecialchars($contributie['leeftijd']); ?></p>
        <p>Soort Lid: <?php echo htmlspecialchars($contributie['soort_lid']); ?></p>
        <p>Bedrag: € <?php echo htmlspecialchars(number_format($contributie['bedrag'], 2)); ?></p>
        <p>Boekjaar: <?php echo htmlspecialchars($contributie['boekjaar']); ?></p>

        <div class="button-group">
            <!-- Formulier om de verwijdering van de contributie te bevestigen -->
            <form action="/contributies/delete/<?php echo $contributie['id']; ?>" method="post">
                <button type="submit" class="btn btn-danger">Ja, verwijder deze contributie</button>
            </form>
            <!-- Annuleerknop om terug te gaan naar het overzicht zonder te verwijderen -->
            <a href="/contributies" class="btn btn-secondary">Annuleren</a>
        </div>
    </div>
</body>
</html>

