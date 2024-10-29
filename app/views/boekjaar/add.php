<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nieuw Boekjaar Toevoegen - Ledenadministratie</title>
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
    <div class="familie-container">
        <a href="/boekjaren" class="btn-back">Terug naar Overzicht</a>
        <h1>Nieuw Boekjaar Toevoegen</h1>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="/boekjaren/add" method="post" class="familie-form">
            <div class="form-group">
                <label for="jaar">Jaar:</label>
                <input type="number" id="jaar" name="jaar" class="form-control" required min="2000" max="2100">
            </div>
            <button type="submit" class="btn">Boekjaar Toevoegen</button>
        </form>
    </div>
</body>
</html> 