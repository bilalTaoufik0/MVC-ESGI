<?php

namespace App\Core;

class Router {
    
    private array $routes = [];
    private string $uri;
    
    public function __construct(string $uri) {
        $this->uri = $uri;
    }
    
    /**
     * Charger les routes depuis le fichier YAML
     */
    public function loadRoutes(string $routeFile): void {
        if (!file_exists($routeFile)) {
            die("Le fichier de routing n'existe pas");
        }
        $this->routes = yaml_parse_file($routeFile);
    }
    
    /**
     * Résoudre la route et retourner le controller et l'action
     */
    public function resolve(): array {
        // Vérifier d'abord si c'est une route statique
        if (isset($this->routes[$this->uri])) {
            return $this->getStaticRoute();
        }
        
        // Si pas de route statique, tenter une route dynamique
        return $this->getDynamicRoute();
    }
    
    /**
     * Récupérer une route statique
     */
    private function getStaticRoute(): array {
        $route = $this->routes[$this->uri];
        
        if (empty($route["controller"]) || empty($route["action"])) {
            die("Erreur, il n'y a aucun controller ou aucune action pour cette URI");
        }
        
        return [
            'controller' => $route["controller"],
            'action'     => $route["action"],
            'params'     => []
        ];
    }
    
    /**
     * Récupérer une route dynamique (slug de page)
     */
    private function getDynamicRoute(): array {
        // Extraire le slug de l'URI
        $slug = ltrim($this->uri, '/');
        
        // Si le slug est vide, ce n'est pas une route valide
        if (empty($slug)) {
            $this->throw404();
        }
        
        // Retourner la configuration pour une page dynamique
        // Le controller vérifiera si la page existe réellement
        return [
            'controller' => 'PageController',
            'action'     => 'show',
            // On passe le slug comme premier argument positionnel
            'params'     => [$slug]
        ];
    }
    
    /**
     * Afficher une erreur 404
     */
    private function throw404(): void {
        http_response_code(404);
        
        // Vérifier si une route 404 personnalisée existe
        if (isset($this->routes['/404'])) {
            $route = $this->routes['/404'];
            if (!empty($route["controller"]) && !empty($route["action"])) {
                $this->executeRoute($route["controller"], $route["action"], []);
                exit;
            }
        }
        
        // Sinon afficher une 404 simple
        die("Page 404 - Page non trouvée");
    }
    
    /**
     * Exécuter une route
     */
    public function dispatch(): void {
        $route = $this->resolve();
        $this->executeRoute($route['controller'], $route['action'], $route['params']);
    }
    
    /**
     * Exécuter le controller et l'action
     */
    private function executeRoute(string $controller, string $action, array $params): void {
        // Vérifier que le fichier du controller existe
        if (!file_exists("Controllers/" . $controller . ".php")) {
            die("Erreur, le fichier du controller n'existe pas");
        }
        
        // Inclure le fichier controller
        include "Controllers/" . $controller . ".php";
        
        // Vérifier que la classe existe
        $controllerClass = "App\\Controller\\" . $controller;
        if (!class_exists($controllerClass)) {
            die("Erreur, la class controller " . $controllerClass . " n'existe pas");
        }
        
        // Créer une instance de la classe
        $objController = new $controllerClass();
        
        // Vérifier que la méthode (action) existe
        if (!method_exists($objController, $action)) {
            die("Erreur, l'action " . $action . " n'existe pas");
        }
        
        // Normaliser les paramètres en tableau indexé
        // (call_user_func_array ignore les clés, mais c'est plus propre)
        $arguments = array_values($params);
        
        // Appeler l'action avec les paramètres (même si le tableau est vide)
        call_user_func_array([$objController, $action], $arguments);
    }
}
