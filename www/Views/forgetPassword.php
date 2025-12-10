<h1>Réinitialiser le mot de passe</h1>

<?php if (!empty($errors)): ?>
    <ul style="color:red;">
        <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form action="/updatePassword" method="POST" class="form">
    <label>Nouveau mot de passe</label>
    <input type="password" name="pwd" required>

    <label>Confirmer le mot de passe</label>
    <input type="password" name="pwdConfirm" required>

    <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">

    <button type="submit" class="btn">Mettre à jour</button>
</form>
