<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Familielid Verwijderen</title>
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Familielid Verwijderen</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php else: ?>
            <p>Weet u zeker dat u het familielid <?php echo htmlspecialchars($familielid['naam']); ?> wilt verwijderen?</p>

            <div class="button-group">
                <form action="/familieleden/delete/<?php echo $familielid['id']; ?>" method="post">
                    <button type="submit" class="btn btn-danger">Ja, verwijder dit familielid</button>
                </form>
                <a href="/familieleden" class="btn btn-secondary">Annuleren</a>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="button-group">
                <a href="/familieleden" class="btn btn-primary">Terug naar Overzicht</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
