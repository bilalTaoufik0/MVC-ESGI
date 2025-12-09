<?php

namespace App\Core;

class Render{

    private string $pathView;
    private string $pathTemplate;
    private array $data = [];

    public function __construct(string $view, string $template = "frontoffice"){
        $this->setView($view);
        $this->setTemplate($template);
    }

    public function setView(string $view): void{
        $this->pathView = "Views/" . $view . ".php";
    }

    public function setTemplate(string $template): void{
        $this->pathTemplate = "Views/Templates/" . $template . ".php";
    }

    public function check(): bool{
        return file_exists($this->pathTemplate) && file_exists($this->pathView);
    }

    public function assign(string $key, mixed $value): void{
        $this->data[$key] = $value;
    }

    public function render(): void{
        if ($this->check()) {
            // variables pour la vue
            extract($this->data);
            // chemin de la vue utilisé par le template
            $pathView = $this->pathView;

            include $this->pathTemplate;
        } else {
            die("Problème avec le template ou la vue");
        }
    }

}
