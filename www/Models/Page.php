<?php

namespace App\Models;

use App\Core\DB;

class Page {

    protected string $table = "page";
    protected string $primary = "id";
    
    private ?int $id = null;
    private ?string $title = null;
    private ?string $description = null;
    private ?string $slug = null;
    private ?int $user_id = null;
    private ?string $status = null;
    private ?string $created_at = null;
    private ?string $updated_at = null;

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getTitle(): ?string { return $this->title; }
    public function getDescription(): ?string { return $this->description; }
    public function getSlug(): ?string { return $this->slug; }
    public function getUserId(): ?int { return $this->user_id; }
    public function getStatus(): ?string { return $this->status; }
    public function getCreatedAt(): ?string { return $this->created_at; }
    public function getUpdatedAt(): ?string { return $this->updated_at; }

    // Setters
    public function setId(?int $id): void { $this->id = $id; }
    public function setTitle(?string $title): void { $this->title = $title; }
    public function setDescription(?string $description): void { $this->description = $description; }
    public function setSlug(?string $slug): void { $this->slug = $slug; }
    public function setUserId(?int $user_id): void { $this->user_id = $user_id; }
    public function setStatus(?string $status): void { $this->status = $status; }
    public function setCreatedAt(?string $created_at): void { $this->created_at = $created_at; }
    public function setUpdatedAt(?string $updated_at): void { $this->updated_at = $updated_at; }

    private function getPdo(): \PDO {
        return DB::getInstance()->getPdo();
    }

    public function insert(array $data): bool {
        $columns = array_keys($data);
        $placeholders = array_map(fn($col) => ":" . $col, $columns);

        $sql = "INSERT INTO {$this->table} (" 
                . implode(", ", $columns) .
               ") VALUES (" 
                . implode(", ", $placeholders) .
               ")";

        $stmt = $this->getPdo()->prepare($sql);
        return $stmt->execute($data);
    }

    public function slugExists(string $slug): bool {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE slug = :slug";
        $stmt = $this->getPdo()->prepare($sql);
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetchColumn() > 0;
    }

    public function findAll(): array {
        $sql = 'SELECT * FROM "' . $this->table . '" ORDER BY id DESC';
        $query = $this->getPdo()->prepare($sql);
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function update(int $id, array $data): bool {
        $setParts = [];
        foreach ($data as $key => $value) {
            $setParts[] = $key . " = :" . $key;
        }

        $sql = "UPDATE {$this->table} SET " . implode(", ", $setParts) . " WHERE id = :id";
        $stmt = $this->getPdo()->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    public function delete(int $id): bool {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->getPdo()->prepare($sql);
        return $stmt->execute(["id" => $id]);
    }

    public function getOneBy(array $data) {
        $field = array_key_first($data); 
        $value = $data[$field];          
        $sql = 'SELECT * FROM "' . $this->table . '" WHERE "' . $field . '" = :value LIMIT 1';
        $query = $this->getPdo()->prepare($sql);
        $query->execute(['value' => $value]);
        return $query->fetch(\PDO::FETCH_ASSOC);
    }

    public function findBySlug(string $slug): ?Page {
        $sql = "SELECT * FROM {$this->table} WHERE slug = :slug AND status = 'published' LIMIT 1";
        $stmt = $this->getPdo()->prepare($sql);
        $stmt->bindValue(':slug', $slug);
        $stmt->execute();
        
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($result){
            $page = new Page();
            $page->setId($result['id']);
            $page->setTitle($result['title']);
            $page->setDescription($result['description']);
            $page->setSlug($result['slug']);
            $page->setUserId($result['user_id']);
            $page->setStatus($result['status']);
            $page->setCreatedAt($result['created_at'] ?? null);
            $page->setUpdatedAt($result['updated_at'] ?? null);
            return $page;
        }
        
        return null;
    }
}