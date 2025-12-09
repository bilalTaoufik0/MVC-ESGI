<?php

namespace App\Controller;

use App\Core\Render;

class Base
{
    public function index(): void
    {
        $render = new Render("home", "frontoffice");
        $render->assign("name", $_SESSION['username'] ?? '');
        $render->render();
    }

    public function contact(): void
    {
        $render = new Render("contact", "frontoffice");
        $render->render();
    }

    public function portfolio(): void
    {
        $render = new Render("portfolio", "frontoffice");
        $render->render();
    }
}
