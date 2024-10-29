<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Familieleden Overzicht - Ledenadministratie</title>
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
    <?php include 'app/views/partials/header.php'; ?>

    <div class="familie-container">
        <a href="/dashboard/secretaris" class="btn-back">Terug naar Dashboard</a>
        <h1>Familieleden Overzicht</h1>

        <a href="/familieleden/add" class="btn-add">Nieuw Familielid Toevoegen</a>

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
                <?php foreach ($familieleden as $familielid): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($familielid['naam']); ?></td>
                        <td><?php echo htmlspecialchars($familielid['geboortedatum']); ?></td>
                        <td><?php echo htmlspecialchars($familielid['familie_naam']); ?></td>
                        <td><?php echo htmlspecialchars($familielid['familie_relatie']); ?></td>
                        <td>
                            <a href="/familieleden/edit/<?php echo $familielid['id']; ?>" class="btn">Bewerken</a>
                            <a href="/familieleden/delete/<?php echo $familielid['id']; ?>" class="btn btn-danger">Verwijderen</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php include 'app/views/partials/footer.php'; ?>
</body>
</html>
