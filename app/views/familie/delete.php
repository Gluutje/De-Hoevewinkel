<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Familie Verwijderen - Ledenadministratie</title>
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
    <div class="familie-container">
        <a href="/families" class="btn-back">Terug naar Overzicht</a>
        <h1>Familie Verwijderen</h1>

        <p>Weet u zeker dat u de familie "<?php echo htmlspecialchars($family['naam']); ?>" wilt verwijderen?</p>

        <div class="button-group">
            <form action="/families/delete/<?php echo $family['id']; ?>" method="post">
                <button type="submit" class="btn btn-danger">Ja, verwijder deze familie</button>
            </form>
            <a href="/families" class="btn btn-secondary">Annuleren</a>
        </div>
    </div>
</body>
</html>
