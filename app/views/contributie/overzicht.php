<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contributie Overzicht - Ledenadministratie</title>
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
    <div class="familie-container">
        <a href="/dashboard/penningmeester" class="btn-back">Terug naar Dashboard</a>
        <h1>Contributie Overzicht <?php echo htmlspecialchars($actiefBoekjaar['jaar']); ?></h1>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <table class="familie-table">
            <thead>
                <tr>
                    <th>Familie</th>
                    <th>Naam</th>
                    <th>Geboortedatum</th>
                    <th>Soort Lid</th>
                    <th>Contributie</th>
                    <th>Status</th>
                    <th>Acties</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contributies as $contributie): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($contributie['familie_naam']); ?></td>
                        <td><?php echo htmlspecialchars($contributie['lid_naam']); ?></td>
                        <td><?php echo htmlspecialchars($contributie['geboortedatum']); ?></td>
                        <td><?php echo htmlspecialchars($contributie['soort_lid']); ?></td>
                        <td>€ <?php echo number_format($contributie['bedrag'], 2); ?></td>
                        <td>
                            <?php if ($contributie['betaald']): ?>
                                <span class="badge badge-success">Betaald op <?php echo date('d-m-Y', strtotime($contributie['betaaldatum'])); ?></span>
                            <?php else: ?>
                                <span class="badge badge-warning">Openstaand</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!$contributie['betaald']): ?>
                                <form action="/contributies/verwerk-betaling" method="post" style="display: inline;">
                                    <input type="hidden" name="familielid_id" value="<?php echo $contributie['familielid_id']; ?>">
                                    <input type="hidden" name="boekjaar_id" value="<?php echo $actiefBoekjaar['id']; ?>">
                                    <button type="submit" class="btn btn-small">Verwerk Betaling</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html> 