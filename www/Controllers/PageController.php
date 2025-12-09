<?php
namespace App\Controller;

use App\Core\Render;
use App\Helper\Errors;
use App\Models\Page;


class PageController
{
    public function createForm(): void
    {

        $render = new Render("createPage", "backoffice");
        $render->render();
    }

    public function createPage(): void{
        $page = new Page();
        $data = [
            "title"       => $_POST["title"],
            "description" => $_POST["description"],
            "slug"        => $_POST["slug"],
            "user_id"     => $_SESSION["id"],          
            "status"      => "draft",         
        ];

        if($page->slugExists($data["slug"])){
            die("slug existe déjà");
        }
        $page->insert($data);
        $this->showPages();
    }

    public function showPages():void {
        $page = new Page();
        $pages = $page->findAll();
        $render = new Render("showPages","frontoffice");
        $render->assign("pages",$pages);
        $render->render();
    }

    public function show(string $slug): void {
        $pageModel = new Page();
        $page = $pageModel->findBySlug($slug);
        
        if(!$page){
            http_response_code(404);
            die("Page non publiée");
        }
        
        $render = new Render("page/show", "frontoffice");
        $render->assign("page", $page);
        $render->assign("title", $page->getTitle());
        $render->assign("description", $page->getDescription());
        $render->render();
    }

    public function edit(){
        if (empty($_GET['id'])) {
            die("ID manquant");
        }

        $id = (int) $_GET['id'];

        $pageModel = new Page();
        $page = $pageModel->getOneBy(["id" => $id]);

        if (!$page) {
            http_response_code(404);
            die("Page introuvable");
        }

        if ($page['user_id'] != $_SESSION['id']) {
            http_response_code(403);
            die("Accès interdit c'est pas ta page");
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $data = [
            "title" => $_POST["title"],
            "description" => $_POST["description"],
            "slug" => $_POST["slug"],
            "status" => $_POST["status"]
        ];

        $pageModel->update($id, $data);
        header("Location: /showPages");
        exit;
    }

        $render = new Render("editPage", "backoffice");
        $render->assign("page", $page);
        $render->render();
    }


    public function delete(){
        if (empty($_GET['id'])) {
            die("ID manquant");
        }

        $id = (int) $_GET['id'];

        $pageModel = new Page();
        $page = $pageModel->getOneBy(["id" => $id]);


        if (!$page) {
            die("Page introuvable");
        }

        if ($page['user_id'] != $_SESSION['id']) {
            http_response_code(403);
            die("Accès interdit");
        }

        $pageModel->delete($id);
        $this->showPages();
    }



}