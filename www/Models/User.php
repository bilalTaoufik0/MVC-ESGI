<?php

namespace App\Models;

use App\Core\DB;

class User{

    protected string $table;

    public function __construct(){
        $this->setTable();
    }

    private function getPdo(): \PDO {
        return DB::getInstance()->getPdo();
    }
    
    public function setTable(): void{
        $this->table = 'user';
    }

    public function create(string $username, string $email, string $password, string $token): bool
    {
        $sql = 'INSERT INTO "user" (username, email, password, token) VALUES (:username, :email, :password, :token)';
        $query = $this->getPdo()->prepare($sql);

        return $query->execute([
            ":username" => $username,
            ":email"    => $email,
            ":password" => password_hash($password, PASSWORD_DEFAULT),
            ":token"    => $token
        ]);
    }

    public function updatePasswordEmail(string $email, string $password): bool{
        $sql = 'UPDATE "user" SET password = :password WHERE email = :email';
        $query = $this->getPdo()->prepare($sql);

        return $query->execute([
            ":password" => password_hash($password, PASSWORD_DEFAULT),
            ":email"    => $email
        ]);
    }

    public function updateTokenEmail(string $email, string $token): bool{
        $sql = 'UPDATE "user" SET token = :token WHERE email = :email';
        $query = $this->getPdo()->prepare($sql);

        return $query->execute([
            ":token" => $token,
            ":email" => $email
        ]);
    }
    
    public function getOneBy(array $data) {
        $field = array_key_first($data); 
        $value = $data[$field];          
        $sql   = 'SELECT * FROM "' . $this->table . '" WHERE "' . $field . '" = :value LIMIT 1';
        $query = $this->getPdo()->prepare($sql);
        $query->execute(['value' => $value]);
        return $query->fetch(\PDO::FETCH_ASSOC);
    }

    public function findAll(): array
    {
        $sql   = 'SELECT id, username, email FROM "' . $this->table . '" ORDER BY id';
        $query = $this->getPdo()->query($sql);
        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }
}
