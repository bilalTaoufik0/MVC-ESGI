<h1>Connexion</h1>

<?php if (!empty($error)): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form action="/login" method="POST" class="form">
    <label>Email</label>
    <input type="email" name="email" required>

    <label>Mot de passe</label>
    <input type="password" name="pwd" required>

    <button type="submit" class="btn">Se connecter</button>
</form>

<p><a href="/registerForm">Créer un compte</a></p>
<p><a href="/forgetPassword">Mot de passe oublié ?</a></p>
