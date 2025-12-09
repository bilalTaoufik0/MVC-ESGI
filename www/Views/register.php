<h1>Inscription</h1>

<?php if (!empty($errors)): ?>
    <ul style="color:red;">
        <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form action="/register" method="POST" class="form">
    <label>Pseudo</label>
    <input type="text" name="username" required>

    <label>Email</label>
    <input type="email" name="email" required>

    <label>Mot de passe</label>
    <input type="password" name="pwd" required>

    <label>Confirmer le mot de passe</label>
    <input type="password" name="pwdConfirm" required>

    <button type="submit" class="btn">Créer mon compte</button>
</form>

<p><a href="/loginForm">Retour à la connexion</a></p>
