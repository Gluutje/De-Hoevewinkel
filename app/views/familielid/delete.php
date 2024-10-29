<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Familielid Verwijderen - Ledenadministratie</title>
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
    <div class="familie-container">
        <a href="/familieleden" class="btn-back">Terug naar Overzicht</a>
        <h1>Familielid Verwijderen</h1>

        <p>Weet u zeker dat u het familielid "<?php echo htmlspecialchars($familielid['naam']); ?>" wilt verwijderen?</p>

        <div class="button-group">
            <form action="/familieleden/delete/<?php echo $familielid['id']; ?>" method="post">
                <button type="submit" class="btn btn-danger">Ja, verwijder dit familielid</button>
            </form>
            <a href="/familieleden" class="btn btn-secondary">Annuleren</a>
        </div>
    </div>
</body>
</html>
