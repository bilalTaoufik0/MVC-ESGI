<?php

namespace App\Controller;

use App\Core\Render;

class Base
{
    public function index(): void
    { //affiche la page 
        $render = new Render("home", "frontoffice");
        $render->assign("name", $_SESSION['username'] ?? ''); //aficher le nom sinon chaine vide 
        $render->render();
    }
    //afficher la page contact
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
