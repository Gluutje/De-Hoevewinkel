<?php
// Toon error als die er is
if (isset($error)): ?>
    <div class="error-message">
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<div class="admin-login">
    <h2>Admin Login</h2>
    
    <form method="POST" class="login-form">
        <div class="form-group">
            <label for="username">Gebruikersnaam</label>
            <input type="text" id="username" name="username" required 
                   autocomplete="off" autofocus>
        </div>
        
        <div class="form-group">
            <label for="password">Wachtwoord</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <button type="submit" class="login-button">
            Inloggen
        </button>
    </form>
</div>

<style>
.admin-login {
    max-width: 300px;
    margin: 40px auto;
    background: white;
    border-radius: var(--border-radius);
    padding: 20px;
}

.admin-login h2 {
    color: var(--primary-color);
    text-align: center;
    margin-bottom: 30px;
    font-size: 24px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: var(--text-color);
    font-size: 16px;
}

.form-group input {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: var(--border-radius);
    font-size: 16px;
    background-color: #f8f9fa;
}

.form-group input:focus {
    outline: none;
    border-color: var(--primary-color);
    background-color: white;
}

.login-button {
    width: 100%;
    padding: 12px;
    background-color: var(--accent-color);
    color: white;
    border: none;
    border-radius: var(--border-radius);
    font-size: 16px;
    cursor: pointer;
    transition: all 0.2s ease;
    margin-top: 10px;
}

.login-button:hover {
    background-color: var(--hover-color);
    transform: translateY(-2px);
}

.error-message {
    background-color: #ffebee;
    color: #c62828;
    padding: 12px;
    border-radius: var(--border-radius);
    margin-bottom: 20px;
    text-align: center;
    font-size: 14px;
}
</style> 