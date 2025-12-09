<?php

namespace App\Core;

class DB {
    
    protected ?object $pdo = null;
    private static ?self $instance = null;

    // Le constructeur est privé pour empêcher l'instanciation directe
    private function __construct(){
        try{
            $this->pdo = new \PDO("pgsql:host=db;port=5432;dbname=devdb","devuser", "devpass");
        }catch(\Exception $e){
            die("Erreur ".$e->getMessage());
        }
    }

    // Méthode statique pour obtenir l'instance unique
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Empêche le clonage de l'instance
    private function __clone() {}

    // Empêche la désérialisation de l'instance
    public function __wakeup() {
        throw new \Exception("Cannot unserialize singleton");
    }

    // Méthode pour accéder à PDO
    public function getPdo(): ?\PDO {
        return $this->pdo;
    }
}