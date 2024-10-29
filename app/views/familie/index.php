<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Families Overzicht - Ledenadministratie</title>
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
    <?php include 'app/views/partials/header.php'; ?>

    <div class="familie-container">
        <a href="/dashboard/secretaris" class="btn-back">Terug naar Dashboard</a>
        <h1>Families Overzicht</h1>

        <a href="/families/add" class="btn-add">Nieuwe Familie Toevoegen</a>

        <table class="familie-table">
            <thead>
                <tr>
                    <th>Naam</th>
                    <th>Adres</th>
                    <th>Acties</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($families as $family): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($family['naam']); ?></td>
                        <td>
                            <?php echo htmlspecialchars($family['straatnaam'] . ' ' . $family['huisnummer'] . ', ' . $family['postcode'] . ' ' . $family['plaats']); ?>
                        </td>
                        <td>
                            <a href="/families/members/<?php echo $family['id']; ?>" class="btn">Bekijk Leden</a>
                            <a href="/families/edit/<?php echo $family['id']; ?>" class="btn">Bewerken</a>
                            <a href="/families/delete/<?php echo $family['id']; ?>" class="btn btn-danger">Verwijderen</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php include 'app/views/partials/footer.php'; ?>
</body>
</html>
