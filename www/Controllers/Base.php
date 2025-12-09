<?php

namespace App\Controller;

use App\Core\Render;
use App\Helper\Errors;

class Base
{
    public function index(array $data = []): void
    {
        $render = new Render("home", "frontoffice");

        // Toujours définir "name", même si aucun username n'est fourni
        $render->assign("name", $data['username'] ?? '');

        $render->render();
    }

    public function contact(): void
    {
        new Render("contact");
    }

    public function portfolio(): void
    {
        new Render("portfolio");
    }
}
