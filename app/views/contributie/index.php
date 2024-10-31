<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contributies Overzicht - Ledenadministratie</title>
    <!-- Link naar de externe CSS-stylesheet voor de opmaak van de pagina -->
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
    <!-- Includen van de header-partial voor consistente navigatie en opmaak -->
    <?php include 'app/views/partials/header.php'; ?>

    <div class="familie-container">
        <h1>Contributies Overzicht</h1>

        <!-- Knoppen om terug te gaan naar het dashboard en een nieuwe contributie toe te voegen -->
        <a href="/dashboard/penningmeester" class="btn btn-secondary">Terug naar Dashboard</a>
        <a href="/contributies/add" class="btn">Nieuwe Contributie Toevoegen</a>

        <!-- Tabel om alle contributies weer te geven -->
        <table class="familie-table">
            <thead>
                <tr>
                    <th>Leeftijd</th>
                    <th>Soort Lid</th>
                    <th>Bedrag</th>
                    <th>Boekjaar</th>
                    <th>Acties</th>
                </tr>
            </thead>
            <tbody>
                <!-- Loop door alle contributies en geef de gegevens weer in de tabel -->
                <?php foreach ($contributies as $contributie): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($contributie['leeftijd']); ?></td>
                        <td><?php echo htmlspecialchars($contributie['soort_lid']); ?></td>
                        <td>€ <?php echo htmlspecialchars(number_format($contributie['bedrag'], 2)); ?></td>
                        <td><?php echo htmlspecialchars($contributie['boekjaar']); ?></td>
                        <td>
                            <!-- Knoppen om de contributie te bewerken of te verwijderen -->
                            <a href="/contributies/edit/<?php echo $contributie['id']; ?>" class="btn">Bewerken</a>
                            <a href="/contributies/delete/<?php echo $contributie['id']; ?>" class="btn btn-danger">Verwijderen</a>
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
