<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Familielid Bewerken - Ledenadministratie</title>
    <!-- Link naar de externe CSS-stylesheet voor de opmaak van de pagina -->
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
    <div class="familie-container">
        <!-- Terugknop om naar het overzicht van familieleden te gaan -->
        <a href="/familieleden" class="btn-back">Terug naar Overzicht</a>
        <h1>Familielid Bewerken</h1>

        <!-- Toon een foutmelding als deze is ingesteld -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Formulier om een bestaand familielid bij te werken -->
        <form action="/familieleden/edit/<?php echo $familielid['id']; ?>" method="post" class="familie-form">
            <div class="form-group">
                <!-- Invoerveld voor de naam van het familielid, met vooraf ingevulde waarde -->
                <label for="naam">Naam:</label>
                <input type="text" id="naam" name="naam" class="form-control" value="<?php echo htmlspecialchars($familielid['naam']); ?>" required>
            </div>
            <div class="form-group">
                <!-- Invoerveld voor de geboortedatum van het familielid, met vooraf ingevulde waarde -->
                <label for="geboortedatum">Geboortedatum:</label>
                <input type="date" id="geboortedatum" name="geboortedatum" class="form-control" value="<?php echo htmlspecialchars($familielid['geboortedatum']); ?>" required>
            </div>
            <div class="form-group">
                <!-- Dropdownmenu om de familie relatie te selecteren, met de juiste optie geselecteerd -->
                <label for="familie_relatie_id">Familie Relatie:</label>
                <select id="familie_relatie_id" name="familie_relatie_id" class="form-control" required>
                    <?php foreach ($familieRelaties as $relatie): ?>
                        <option value="<?php echo $relatie['id']; ?>" <?php echo ($relatie['id'] == $familielid['soort_lid_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($relatie['omschrijving']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <!-- Dropdownmenu om de familie te selecteren, met de juiste optie geselecteerd -->
                <label for="familie_id">Familie:</label>
                <select id="familie_id" name="familie_id" class="form-control" required>
                    <?php foreach ($families as $familie): ?>
                        <option value="<?php echo $familie['id']; ?>" <?php echo ($familie['id'] == $familielid['familie_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($familie['naam']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Submitknop om het formulier in te dienen en het familielid bij te werken -->
            <button type="submit" class="btn">Familielid Bijwerken</button>
        </form>
    </div>
</body>
</html>

