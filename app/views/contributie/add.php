<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nieuwe Contributie Toevoegen - Ledenadministratie</title>
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
    <div class="familie-container">
        <a href="/contributies" class="btn-back">Terug naar Overzicht</a>
        <h1>Nieuwe Contributie Toevoegen</h1>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="/contributies/add" method="post" class="familie-form">
            <div class="form-group">
                <label for="leeftijd">Leeftijd:</label>
                <input type="number" id="leeftijd" name="leeftijd" class="form-control" required min="0" max="150">
            </div>
            <div class="form-group">
                <label for="soort_lid_id">Soort Lid:</label>
                <select id="soort_lid_id" name="soort_lid_id" class="form-control" required>
                    <?php foreach ($soortLeden as $soortLid): ?>
                        <option value="<?php echo $soortLid['id']; ?>"><?php echo htmlspecialchars($soortLid['omschrijving']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="bedrag">Bedrag:</label>
                <input type="number" id="bedrag" name="bedrag" class="form-control" required min="0" step="0.01">
            </div>
            <div class="form-group">
                <label for="boekjaar_id">Boekjaar:</label>
                <select id="boekjaar_id" name="boekjaar_id" class="form-control" required>
                    <?php foreach ($boekjaren as $boekjaar): ?>
                        <option value="<?php echo $boekjaar['id']; ?>"><?php echo htmlspecialchars($boekjaar['jaar']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn">Contributie Toevoegen</button>
        </form>
    </div>
</body>
</html>
