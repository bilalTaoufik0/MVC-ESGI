<?php
namespace App;

use App\Core\Router;


require __DIR__ . '/vendor/autoload.php';

/*
 * Faire en sorte que toutes les URLS possibles
 * pointent sur ce fichier index.php
 *
 * http://localhost:8080/toto -> index.php
 * http://localhost:8080/ -> index.php
 * http://localhost:8080/article/312 -> index.php
 */

//Faire en sorte de créer l'instance du controller associé à l'URL
//Appeler l'action associée à l'URL

//ex : /contact
//Appel du controller Base
//Action contact

/*
 *
 * Dans le cas ou l'url ne correspond à aucune route dans routes.yml
 * Afficher une veritable pas 404
 *
 */




spl_autoload_register(function ($class){
        // App\Helper\Errors
        //Créer le code permettant d'aller chercher dans
        //le dossier Helpers la classe qui a engendré une erreur
        $namespaceArray = [
                            "namepace"=> ["App\\Helper\\", "App\\Core\\","App\\Models\\","App\\Controller\\"],
                            "path"=> ["Helpers/", "Core/","Models/","Controllers/"],
                        ];
        $filname = str_ireplace($namespaceArray['namepace'],$namespaceArray['path'], $class  ). ".php";
        if(file_exists($filname)) {
            include $filname;
        }

    }
);



// 1. Récupération et nettoyage de l'URI
$uri = $_SERVER["REQUEST_URI"];
$uriExploded = explode("?",$uri);
if(is_array($uriExploded)){
    $uri = $uriExploded[0];
}
if(strlen($uri)>1){
    $uri = rtrim($uri, "/");
}
// À ce stade : $uri = "/ma-super-page"

// 2. Démarrer la session
session_start();

// 3. Créer le router avec l'URI nettoyée
$router = new Router($uri);
// ↑ APPEL : Router->__construct("/ma-super-page")

// 4. Charger les routes depuis le fichier YAML
$router->loadRoutes("routes.yml");
// ↑ APPEL : Router->loadRoutes("routes.yml")
//   Cette fonction lit le fichier et stocke les routes dans $this->routes

// 5. Dispatcher la route (lance tout le processus)
$router->dispatch();
// ↑ APPEL : Router->dispatch()
//   C'est ICI que tout commence !