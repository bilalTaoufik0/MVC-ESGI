<h1>Liste des pages</h1>

<?php foreach ($pages as $page): ?>
    <div>
        <div>Nom Page: <?= htmlspecialchars($page["title"]) ?></div>
        <div>
            ID : <?= $page["id"] ?> |
            Slug : <strong><?= htmlspecialchars($page["slug"]) ?></strong> |
            Statut : <em><?= htmlspecialchars($page["status"]) ?></em>
        </div>

        <p>Description: <?= nl2br(htmlspecialchars($page["description"])) ?></p>
        <?php if ($page['user_id'] == $_SESSION['id']): ?>
            <a href="/page/edit?id=<?= $page['id'] ?>">Éditer</a> |
            <a href="/page/delete?id=<?= $page['id'] ?>"
            onclick="return confirm('Supprimer cette page ?')">
            Supprimer
            </a>
        <?php endif; ?>
        <a href="/<?= htmlspecialchars($page["slug"]) ?>">Voir la page</a>
    </div>
<?php endforeach; ?>

</body>
</html>