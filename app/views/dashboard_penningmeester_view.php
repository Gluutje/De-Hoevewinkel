<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penningmeester Dashboard - Ledenadministratie</title>
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar">
            <h2>Penningmeester Dashboard</h2>
            <nav>
                <ul>
                    <li><a href="/contributies/overzicht">Contributie Overzicht</a></li>
                    <li><a href="/contributies/instellingen">Contributie instellingen</a></li>
                    <li><a href="/boekjaren">Boekjaren beheren</a></li>
                </ul>
            </nav>
            <a href="/logout" class="logout-button">Uitloggen</a>
        </aside>
        <main class="main-content">
            <h1>Welkom, <?php echo htmlspecialchars($username); ?>!</h1>
            
            <div class="section-header">
                <h2>Actief Boekjaar</h2>
                <a href="/boekjaren" class="btn">Boekjaren Beheren</a>
            </div>
            <?php if ($actiefBoekjaar): ?>
                <div class="alert alert-info">
                    Actief boekjaar: <?php echo htmlspecialchars($actiefBoekjaar['jaar']); ?>
                </div>
            <?php else: ?>
                <div class="alert alert-danger">
                    Er is geen actief boekjaar. Activeer eerst een boekjaar.
                </div>
            <?php endif; ?>

            <div class="section-header">
                <h2>Contributie Instellingen</h2>
                <a href="/contributies/instellingen" class="btn">Instellingen Beheren</a>
            </div>
            <table class="dashboard-table">
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

            <div class="section-header">
                <h2>Contributie Overzicht Leden</h2>
                <a href="/contributies/overzicht" class="btn">Volledig Overzicht</a>
            </div>
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Familie</th>
                        <th>Naam</th>
                        <th>Soort Lid</th>
                        <th>Bedrag</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ledenContributies as $contributie): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($contributie['familie_naam']); ?></td>
                            <td><?php echo htmlspecialchars($contributie['lid_naam']); ?></td>
                            <td><?php echo htmlspecialchars($contributie['soort_lid']); ?></td>
                            <td>€ <?php echo number_format($contributie['bedrag'], 2); ?></td>
                            <td>
                                <?php if ($contributie['betaald']): ?>
                                    <span class="badge badge-success">Betaald</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Openstaand</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
