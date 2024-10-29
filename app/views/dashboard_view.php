<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Ledenadministratie</title>
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
    <div class="dashboard-container">
        <h1>Welkom bij het Dashboard</h1>
        <p>Hallo, <?php echo htmlspecialchars($username); ?>! Je bent ingelogd als <?php echo htmlspecialchars($role); ?>.</p>
        <nav>
            <ul>
                <li><a href="/families">Families beheren</a></li>
                <li><a href="/leden">Leden beheren</a></li>
                <li><a href="/contributies">Contributies beheren</a></li>
            </ul>
        </nav>
        <a href="/logout" class="logout-button">Uitloggen</a>
    </div>
</body>
</html>
