<?php
namespace App;

use App\Core\Router;

require __DIR__ . '/vendor/autoload.php';

/*
 * Autoloader maison pour les classes du projet :
 * App\Helper\X       -> Helpers/X.php
 * App\Core\X         -> Core/X.php
 * App\Models\X       -> Models/X.php
 * App\Controller\X   -> Controllers/X.php
 */
spl_autoload_register(function ($class) {
    // Exemple de $class :
    //   App\Core\Router
    //   App\Controller\Base
    $namespaceArray = [
        "namepace" => [
            "App\\Helper\\",
            "App\\Core\\",
            "App\\Models\\",
            "App\\Controller\\"
        ],
        "path" => [
            "Helpers/",
            "Core/",
            "Models/",
            "Controllers/"
        ],
    ];

    $filename = str_ireplace($namespaceArray['namepace'], $namespaceArray['path'], $class) . ".php";

    if (file_exists($filename)) {
        require $filename;
    }
});

// 1. Récupération et nettoyage de l'URI
$uri = $_SERVER["REQUEST_URI"];
$uriExploded = explode("?", $uri);
if (is_array($uriExploded)) {
    $uri = $uriExploded[0];
}
if (strlen($uri) > 1) {
    $uri = rtrim($uri, "/");
}
// À ce stade : $uri = "/ma-super-page"

// 2. Démarrer la session
session_start();

// 3. Définir les routes publiques (pas besoin d'être connecté)
$publicRoutes = [
    '/loginForm',
    '/login',
    '/registerForm',
    '/register',
    '/forgetPassword',
    '/resetPassword',
    '/activation',
    '/updatePassword',
];

// Autoriser les assets statiques (CSS, JS, images, etc.)
$isAsset = str_starts_with($uri, '/Public');

// 4. Si pas connecté et route privée -> redirection vers login
if (!isset($_SESSION['id']) && !in_array($uri, $publicRoutes, true) && !$isAsset) {
    header('Location: /loginForm');
    exit;
}

// 5. Créer le router avec l'URI nettoyée
$router = new Router($uri);

// 6. Charger les routes depuis le fichier YAML
$router->loadRoutes("routes.yml");

// 7. Dispatcher la route (lance le bon contrôleur + action)
$router->dispatch();
