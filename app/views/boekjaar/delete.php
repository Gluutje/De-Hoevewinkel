<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boekjaar Verwijderen</title>
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Boekjaar Verwijderen</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <p>Weet u zeker dat u het boekjaar <?php echo htmlspecialchars($boekjaar['jaar']); ?> wilt verwijderen?</p>

        <div class="button-group">
            <form action="/boekjaren/delete/<?php echo $boekjaar['id']; ?>" method="post">
                <button type="submit" class="btn btn-danger">Ja, verwijder dit boekjaar</button>
            </form>
            <a href="/boekjaren" class="btn btn-secondary">Annuleren</a>
        </div>
    </div>
</body>
</html>
