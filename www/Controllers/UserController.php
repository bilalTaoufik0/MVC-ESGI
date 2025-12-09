<?php

namespace App\Controller;

use App\Core\Render;
use App\Models\User;

class UserController
{
    public function index(): void
    {
        $userModel = new User();
        $users     = $userModel->findAll();

        $render = new Render("users", "backoffice");
        $render->assign("users", $users);
        $render->render();
    }
}
