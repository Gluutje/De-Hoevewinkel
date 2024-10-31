<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inloggen - Ledenadministratie</title>
    <!-- Laad de stylesheet -->
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
    <!-- Container voor het login formulier -->
    <div class="container">
        <!-- Login kaart met formulier -->
        <div class="card">
            <h1>Inloggen Ledenadministratie</h1>
            
            <!-- Toon eventuele foutmeldingen -->
            <?php if (isset($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>

            <!-- Login formulier -->
            <form action="/login" method="post">
                <!-- Gebruikersnaam veld -->
                <div class="form-group">
                    <label for="username">Gebruikersnaam:</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <!-- Wachtwoord veld -->
                <div class="form-group">
                    <label for="password">Wachtwoord:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <!-- Submit knop -->
                <button type="submit">Inloggen</button>
            </form>
        </div>
    </div>
</body>
</html>
