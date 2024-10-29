<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secretaris Dashboard - Ledenadministratie</title>
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar">
            <h2>Secretaris Dashboard</h2>
            <nav>
                <ul>
                    <li><a href="/families">Families beheren</a></li>
                    <li><a href="/familieleden">Familieleden beheren</a></li>
                </ul>
            </nav>
            <a href="/logout" class="logout-button">Uitloggen</a>
        </aside>
        <main class="main-content">
            <h1>Welkom, <?php echo htmlspecialchars($username); ?>!</h1>
            <p>Je bent ingelogd als secretaris.</p>
            
            <div class="section-header">
                <h2>Overzicht Families</h2>
                <a href="/families/add" class="btn-add">Nieuwe Familie Toevoegen</a>
            </div>
            <table class="dashboard-table">
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
                            <td class="address-cell">
                                <?php echo htmlspecialchars($family['straatnaam'] . ' ' . $family['huisnummer'] . ', ' . $family['postcode'] . ' ' . $family['plaats']); ?>
                            </td>
                            <td>
                                <a href="/families/members/<?php echo $family['id']; ?>" class="btn btn-small">Bekijk Leden</a>
                                <a href="/families/edit/<?php echo $family['id']; ?>" class="btn btn-small">Bewerken</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="section-header">
                <h2>Overzicht Familieleden</h2>
                <a href="/familieleden/add" class="btn-add">Nieuw Familielid Toevoegen</a>
            </div>
            <table class="dashboard-table">
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
                                <a href="/familieleden/edit/<?php echo $familielid['id']; ?>" class="btn btn-small">Bewerken</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
