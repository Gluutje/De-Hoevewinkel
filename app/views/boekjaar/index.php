<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boekjaren Overzicht - Ledenadministratie</title>
    <!-- Link naar de externe CSS-stylesheet voor de opmaak van de pagina -->
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
    <div class="familie-container">
        <!-- Terugknop om naar het penningmeester-dashboard te gaan -->
        <a href="/dashboard/penningmeester" class="btn-back">Terug naar Dashboard</a>
        <h1>Boekjaren Overzicht</h1>

        <!-- Knop om een nieuw boekjaar toe te voegen -->
        <a href="/boekjaren/add" class="btn-add">Nieuw Boekjaar Toevoegen</a>

        <!-- Tabel om alle boekjaren weer te geven -->
        <table class="familie-table">
            <thead>
                <tr>
                    <th>Jaar</th>
                    <th>Status</th>
                    <th>Acties</th>
                </tr>
            </thead>
            <tbody>
                <!-- Loop door alle boekjaren en geef de gegevens weer in de tabel -->
                <?php foreach ($boekjaren as $boekjaar): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($boekjaar['jaar']); ?></td>
                        <td>
                            <!-- Controleer of het boekjaar actief is en toon de juiste status -->
                            <?php if ($boekjaar['is_actief']): ?>
                                <span class="badge badge-success">Actief</span>
                            <?php else: ?>
                                <!-- Knop om het boekjaar actief te maken -->
                                <a href="/boekjaren/setActief/<?php echo $boekjaar['id']; ?>" class="btn btn-small">Activeer</a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <!-- Knoppen om het boekjaar te bewerken of te verwijderen -->
                            <a href="/boekjaren/edit/<?php echo $boekjaar['id']; ?>" class="btn">Bewerken</a>
                            <a href="/boekjaren/delete/<?php echo $boekjaar['id']; ?>" class="btn btn-danger">Verwijderen</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
