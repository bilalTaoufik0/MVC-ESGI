<html>
    <head>
        <title>Frontoffice</title>
        <link rel="stylesheet" href="/Public/css/style.css">
    </head>
    <body class="front">
        <header class="front-header">
            <h1>Site public</h1>

            <nav class="front-nav">
                <a class="btn" href="/">Accueil</a>
                <a class="btn" href="/mon-portfolio">Portfolio</a>
                <a class="btn" href="/contact">Contact</a>

                <?php if (!empty($_SESSION['id'])): ?>
                    <a class="btn" href="/createPageForm">Créer une page</a>
                    <a class="btn" href="/showPages">Mes pages</a>
                    <a class="btn" href="/logout">Déconnexion</a>
                <?php else: ?>
                    <a class="btn" href="/loginForm">Connexion</a>
                    <a class="btn" href="/registerForm">Inscription</a>
                <?php endif; ?>
            </nav>
        </header>

        <main class="front-main">
            <?php include $pathView; ?>
        </main>
    </body>
</html>
