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
        <!-- Sidebar met navigatieopties voor het Penningmeester Dashboard -->
        <h2>Penningmeester Dashboard</h2>
        <nav>
            <ul>
                <!-- Navigatie naar verschillende pagina's voor contributiebeheer en boekjaren -->
                <li><a href="/contributies/overzicht">Contributie Overzicht</a></li>
                <li><a href="/contributies/instellingen">Contributie instellingen</a></li>
                <li><a href="/boekjaren">Boekjaren beheren</a></li>
            </ul>
        </nav>
        <!-- Uitlogknop om de gebruiker uit te loggen -->
        <a href="/logout" class="logout-button">Uitloggen</a>
    </aside>

    <main class="main-content">
        <!-- Welkomstbericht met de naam van de gebruiker -->
        <h1>Welkom, <?php echo htmlspecialchars($username); ?>!</h1>

        <div class="section-header">
            <!-- Header voor het actieve boekjaar, inclusief een link om boekjaren te beheren -->
            <h2>Actief Boekjaar</h2>
            <a href="/boekjaren" class="btn">Boekjaren Beheren</a>
        </div>

        <?php if ($actiefBoekjaar): ?>
            <!-- Informatie over het actieve boekjaar -->
            <div class="alert alert-info">
                Actief boekjaar: <?php echo htmlspecialchars($actiefBoekjaar['jaar']); ?>
            </div>
        <?php else: ?>
            <!-- Waarschuwing als er geen actief boekjaar is ingesteld -->
            <div class="alert alert-danger">
                Er is geen actief boekjaar. Activeer eerst een boekjaar.
            </div>
        <?php endif; ?>

        <div class="section-header">
            <!-- Header voor de contributie-instellingen, inclusief een link om deze te beheren -->
            <h2>Contributie Instellingen</h2>
            <a href="/contributies/instellingen" class="btn">Instellingen Beheren</a>
        </div>

        <!-- Tabel met contributie-instellingen voor verschillende soorten leden -->
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
                            // Leeftijdscategorie weergeven afhankelijk van het soort lid
                            if ($contributie['soort_lid'] === 'oudere') {
                                echo "Vanaf 51 jaar";
                            } else {
                                echo "tot " . htmlspecialchars($contributie['leeftijd']) . " jaar";
                            }
                        ?></td>
                        <td><?php 
                            // Berekening van de korting als percentage van het basisbedrag
                            $korting = (100 - ($contributie['bedrag'] / $basisbedrag * 100));
                            echo number_format($korting, 0) . '%';
                        ?></td>
                        <td>€ <?php echo number_format($contributie['bedrag'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="section-header">
            <!-- Header voor het contributieoverzicht van leden, inclusief een link naar het volledige overzicht -->
            <h2>Contributie Overzicht Leden</h2>
            <a href="/contributies/overzicht" class="btn">Volledig Overzicht</a>
        </div>

        <!-- Tabel met een overzicht van contributies van individuele leden -->
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
                                <!-- Badge voor betaalde status -->
                                <span class="badge badge-success">Betaald</span>
                            <?php else: ?>
                                <!-- Badge voor openstaande status -->
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

