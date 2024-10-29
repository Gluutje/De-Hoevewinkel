<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inloggen - Ledenadministratie</title>
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>Inloggen Ledenadministratie</h1>
            <?php if (isset($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            <form action="/login" method="post">
                <div class="form-group">
                    <label for="username">Gebruikersnaam:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Wachtwoord:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit">Inloggen</button>
            </form>
        </div>
    </div>
</body>
</html>
