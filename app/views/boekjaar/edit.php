<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boekjaar Bewerken - Ledenadministratie</title>
    <!-- Link naar de externe CSS-stylesheet voor de opmaak van de pagina -->
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
    <div class="familie-container">
        <!-- Terugknop om naar het overzicht van de boekjaren te gaan -->
        <a href="/boekjaren" class="btn-back">Terug naar Overzicht</a>
        <h1>Boekjaar Bewerken</h1>

        <!-- Toon een foutmelding als deze is ingesteld -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Formulier om een bestaand boekjaar bij te werken -->
        <form action="/boekjaren/edit/<?php echo $boekjaar['id']; ?>" method="post" class="familie-form">
            <div class="form-group">
                <!-- Label en invoerveld voor het jaartal, met vooraf ingevulde waarde -->
                <label for="jaar">Jaar:</label>
                <input type="number" id="jaar" name="jaar" class="form-control" value="<?php echo htmlspecialchars($boekjaar['jaar']); ?>" required min="2000" max="2100">
            </div>
            <!-- Submitknop om de wijzigingen op te slaan -->
            <button type="submit" class="btn">Boekjaar Bijwerken</button>
        </form>
    </div>
</body>
</html>
