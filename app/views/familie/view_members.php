<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Familieleden van <?php echo htmlspecialchars($family['naam']); ?> - Ledenadministratie</title>
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
    <div class="familie-container">
        <!-- Terugknop naar familie overzicht -->
        <a href="/families" class="btn-back">Terug naar Overzicht</a>
        <h1>Familieleden van <?php echo htmlspecialchars($family['naam']); ?></h1>
        
        <!-- Toon familie adresgegevens -->
        <div class="family-info">
            <p><strong>Adres:</strong> <?php echo htmlspecialchars($family['straatnaam'] . ' ' . $family['huisnummer'] . ', ' . $family['postcode'] . ' ' . $family['plaats']); ?></p>
        </div>

        <?php if (empty($familieleden)): ?>
            <!-- Toon melding als er geen familieleden zijn -->
            <div class="alert alert-info">
                Er zijn nog geen familieleden aan deze familie toegevoegd.
            </div>
        <?php else: ?>
            <!-- Toon tabel met familieleden -->
            <table class="familie-table">
                <thead>
                    <tr>
                        <th>Naam</th>
                        <th>Geboortedatum</th>
                        <th>Familie Relatie</th>
                        <th>Acties</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($familieleden as $familielid): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($familielid['naam']); ?></td>
                            <td><?php echo htmlspecialchars($familielid['geboortedatum']); ?></td>
                            <td><?php echo htmlspecialchars($familielid['familie_relatie']); ?></td>
                            <td>
                                <!-- Actieknoppen voor bewerken en verwijderen -->
                                <a href="/familieleden/edit/<?php echo $familielid['id']; ?>" class="btn">Bewerken</a>
                                <a href="/familieleden/delete/<?php echo $familielid['id']; ?>" class="btn btn-danger">Verwijderen</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <!-- Knop voor toevoegen nieuw familielid -->
        <div class="button-group">
            <a href="/familieleden/add" class="btn-add">Nieuw Familielid Toevoegen</a>
        </div>
    </div>
</body>
</html> 