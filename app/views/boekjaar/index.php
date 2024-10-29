<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boekjaren Overzicht - Ledenadministratie</title>
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
    <div class="familie-container">
        <a href="/dashboard/penningmeester" class="btn-back">Terug naar Dashboard</a>
        <h1>Boekjaren Overzicht</h1>

        <a href="/boekjaren/add" class="btn-add">Nieuw Boekjaar Toevoegen</a>

        <table class="familie-table">
            <thead>
                <tr>
                    <th>Jaar</th>
                    <th>Status</th>
                    <th>Acties</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($boekjaren as $boekjaar): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($boekjaar['jaar']); ?></td>
                        <td>
                            <?php if ($boekjaar['is_actief']): ?>
                                <span class="badge badge-success">Actief</span>
                            <?php else: ?>
                                <a href="/boekjaren/setActief/<?php echo $boekjaar['id']; ?>" class="btn btn-small">Activeer</a>
                            <?php endif; ?>
                        </td>
                        <td>
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