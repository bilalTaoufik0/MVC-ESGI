<html>
    <head>
        <title>Backoffice</title>
        <link rel="stylesheet" href="/Public/css/stylebo.css">
    </head>
    <body class="back">
        <header class="back-header">
            <h1>Dashboard</h1>

            <nav class="back-nav">
                <a class="btn" href="/">Aller au site</a>
                <a class="btn" href="/showPages">Gérer les pages</a>
                <a class="btn" href="/createPageForm">Créer une page</a>
                <a class="btn" href="/users">Utilisateurs</a>
                <a class="btn" href="/logout">Déconnexion</a>
            </nav>
        </header>

        <main class="back-main">
            <?php include $pathView; ?>
        </main>
    </body>
</html>
