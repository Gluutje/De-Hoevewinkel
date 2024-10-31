<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Families Overzicht - Ledenadministratie</title>
    <!-- Link naar de externe CSS-stylesheet voor de opmaak van de pagina -->
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
    <!-- Includen van de header-partial voor consistente navigatie en opmaak -->
    <?php include 'app/views/partials/header.php'; ?>

    <div class="familie-container">
        <!-- Terugknop om naar het secretaris-dashboard te gaan -->
        <a href="/dashboard/secretaris" class="btn-back">Terug naar Dashboard</a>
        <h1>Families Overzicht</h1>

        <!-- Knop om een nieuwe familie toe te voegen -->
        <a href="/families/add" class="btn-add">Nieuwe Familie Toevoegen</a>

        <!-- Tabel om het overzicht van families weer te geven -->
        <table class="familie-table">
            <thead>
                <tr>
                    <th>Naam</th>
                    <th>Adres</th>
                    <th>Acties</th>
                </tr>
            </thead>
            <tbody>
                <!-- Loop door alle families en geef de gegevens weer in de tabel -->
                <?php foreach ($families as $family): ?>
                    <tr>
                        <!-- Toon de familienaam en het volledige adres -->
                        <td><?php echo htmlspecialchars($family['naam']); ?></td>
                        <td>
                            <?php echo htmlspecialchars($family['straatnaam'] . ' ' . $family['huisnummer'] . ', ' . $family['postcode'] . ' ' . $family['plaats']); ?>
                        </td>
                        <td>
                            <!-- Knoppen om de leden van de familie te bekijken, de familie te bewerken of te verwijderen -->
                            <a href="/families/members/<?php echo $family['id']; ?>" class="btn">Bekijk Leden</a>
                            <a href="/families/edit/<?php echo $family['id']; ?>" class="btn">Bewerken</a>
                            <a href="/families/delete/<?php echo $family['id']; ?>" class="btn btn-danger">Verwijderen</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Includen van de footer-partial voor consistente navigatie en opmaak -->
    <?php include 'app/views/partials/footer.php'; ?>
</body>
</html>

