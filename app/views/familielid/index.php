<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Familieleden Overzicht - Ledenadministratie</title>
    <!-- Link naar de externe CSS-stylesheet voor de opmaak van de pagina -->
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
    <!-- Includen van de header-partial voor consistente navigatie en opmaak -->
    <?php include 'app/views/partials/header.php'; ?>

    <div class="familie-container">
        <!-- Terugknop om naar het secretaris-dashboard te gaan -->
        <a href="/dashboard/secretaris" class="btn-back">Terug naar Dashboard</a>
        <h1>Familieleden Overzicht</h1>

        <!-- Knop om een nieuw familielid toe te voegen -->
        <a href="/familieleden/add" class="btn-add">Nieuw Familielid Toevoegen</a>

        <!-- Tabel om het overzicht van familieleden weer te geven -->
        <table class="familie-table">
            <thead>
                <tr>
                    <th>Naam</th>
                    <th>Geboortedatum</th>
                    <th>Familie</th>
                    <th>Familie Relatie</th>
                    <th>Acties</th>
                </tr>
            </thead>
            <tbody>
                <!-- Loop door alle familieleden en geef de gegevens weer in de tabel -->
                <?php foreach ($familieleden as $familielid): ?>
                    <tr>
                        <!-- Toon de naam, geboortedatum, familie en familie relatie van het familielid -->
                        <td><?php echo htmlspecialchars($familielid['naam']); ?></td>
                        <td><?php echo htmlspecialchars($familielid['geboortedatum']); ?></td>
                        <td><?php echo htmlspecialchars($familielid['familie_naam']); ?></td>
                        <td><?php echo htmlspecialchars($familielid['familie_relatie']); ?></td>
                        <td>
                            <!-- Knoppen om het familielid te bewerken of te verwijderen -->
                            <a href="/familieleden/edit/<?php echo $familielid['id']; ?>" class="btn">Bewerken</a>
                            <a href="/familieleden/delete/<?php echo $familielid['id']; ?>" class="btn btn-danger">Verwijderen</a>
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
