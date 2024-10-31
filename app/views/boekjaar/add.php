<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nieuw Boekjaar Toevoegen - Ledenadministratie</title>
    <!-- Link naar de externe CSS-stylesheet voor de pagina-opmaak -->
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
    <div class="familie-container">
        <!-- Terugknop om naar het boekjaarsoverzicht te gaan -->
        <a href="/boekjaren" class="btn-back">Terug naar Overzicht</a>
        <h1>Nieuw Boekjaar Toevoegen</h1>

        <!-- Toon een foutmelding als deze is ingesteld -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Formulier om een nieuw boekjaar toe te voegen -->
        <form action="/boekjaren/add" method="post" class="familie-form">
            <div class="form-group">
                <!-- Label en invoerveld voor het jaartal -->
                <label for="jaar">Jaar:</label>
                <input type="number" id="jaar" name="jaar" class="form-control" required min="2000" max="2100">
            </div>
            <!-- Submitknop om het formulier in te dienen -->
            <button type="submit" class="btn">Boekjaar Toevoegen</button>
        </form>
    </div>
</body>
</html>
