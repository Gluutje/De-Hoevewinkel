<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contributie Instellingen - Ledenadministratie</title>
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
    <div class="familie-container">
        <a href="/dashboard/penningmeester" class="btn-back">Terug naar Dashboard</a>
        <h1>Contributie Instellingen</h1>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if (!$actiefBoekjaar): ?>
            <div class="alert alert-danger">Er is geen actief boekjaar. Activeer eerst een boekjaar.</div>
        <?php else: ?>
            <h2>Contributie instellingen voor boekjaar <?php echo htmlspecialchars($actiefBoekjaar['jaar']); ?></h2>

            <form action="/contributies/instellingen" method="post" class="familie-form">
                <div class="form-group">
                    <label for="basisbedrag">Basisbedrag:</label>
                    <input type="number" id="basisbedrag" name="basisbedrag" class="form-control" required min="0" step="0.01" value="100.00">
                    <small class="form-text text-muted">Dit is het standaard contributiebedrag zonder kortingen</small>
                </div>

                <div class="contributie-overzicht">
                    <h3>Huidige Contributiebedragen</h3>
                    <table class="familie-table">
                        <thead>
                            <tr>
                                <th>Soort Lid</th>
                                <th>Leeftijdscategorie</th>
                                <th>Korting</th>
                                <th>Bedrag</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($contributies as $contributie): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($contributie['soort_lid']); ?></td>
                                    <td><?php 
                                        if ($contributie['soort_lid'] === 'oudere') {
                                            echo "Vanaf 51 jaar";
                                        } else {
                                            echo "tot " . htmlspecialchars($contributie['leeftijd']) . " jaar";
                                        }
                                    ?></td>
                                    <td><?php 
                                        $korting = (100 - ($contributie['bedrag'] / $basisbedrag * 100));
                                        echo number_format($korting, 0) . '%';
                                    ?></td>
                                    <td>€ <?php echo number_format($contributie['bedrag'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <button type="submit" class="btn">Instellingen Opslaan en Contributies Berekenen</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
