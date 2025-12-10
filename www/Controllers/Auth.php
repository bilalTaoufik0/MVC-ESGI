<?php
namespace App\Controller;

use App\Core\Render;
use App\Models\User;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Auth
{   //afficher connexion
    public function show_login(): void
    {
        $render = new Render("login", "backoffice");
        $render->render();
    }
    //aficher inscription
    public function show_register(): void
    {
        $render = new Render("register", "backoffice");
        $render->render();
    }
    //traitement de la connexion
    public function login(): void
    {   //On vérifie que la méthode est POST
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (!empty($_POST['email']) && !empty($_POST['pwd'])) { //pas vide
                //recup des données 
                $email = strtolower(trim($_POST['email']));
                $password = $_POST['pwd'];

                $userModel = new User();
                $user = $userModel->getOneBy(['email' => $email]);
                //verif
                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION = [ //creation de la session
                        'id'       => $user['id'],
                        'username' => $user['username'],
                        'email'    => $user['email'],
                    ];
                    header('Location: /'); 
                    exit;
                }
                // en cas d'erreur
                $render = new Render("login", "backoffice");
                $render->assign("error", "Identifiants incorrects");
                $render->render();
                return;
            }
        }

        header('Location: /loginForm');
        exit;
    }

    public function register(): void
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (
                !empty($_POST['username']) &&
                !empty($_POST['email']) &&
                !empty($_POST['pwd']) &&
                !empty($_POST['pwdConfirm'])
            ) {

                $username        = trim($_POST['username']);
                $email           = strtolower(trim($_POST['email']));
                $password        = $_POST['pwd'];
                $passwordConfirm = $_POST['pwdConfirm'];

                $errors = [];

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Email invalide";
                }

                if (
                    strlen($password) < 8 ||
                    !preg_match('/[a-z]/', $password) ||
                    !preg_match('/[A-Z]/', $password) ||
                    !preg_match('/[0-9]/', $password)
                ) {
                    $errors[] = "Votre mot de passe doit faire au minimum 8 caractères avec min, maj, chiffres";
                }

                if ($password !== $passwordConfirm) {
                    $errors[] = "Les mots de passe ne correspondent pas";
                }

                $userModel = new User();
                $existing  = $userModel->getOneBy(['email' => $email]);
                if ($existing) {
                    $errors[] = "Un compte existe déjà avec cet email";
                }

                if (!empty($errors)) {
                    $render = new Render("register", "backoffice");
                    $render->assign("errors", $errors);
                    $render->render();
                    return;
                }

                $token = bin2hex(random_bytes(16));
                $userModel->create($username, $email, $password, $token);

                // envoi mail de confirmation via Mailpit
                $phpmailer = new PHPMailer(true);
                try {
                    $phpmailer->isSMTP();
                    $phpmailer->Host     = 'mailpit';
                    $phpmailer->SMTPAuth = false;
                    $phpmailer->Port     = 1025;

                    $phpmailer->setFrom('no-reply@example.com', 'Mini CMS');
                    $phpmailer->addAddress($email, $username);

                    $phpmailer->isHTML(true);
                    $phpmailer->Subject = 'Confirmation de votre inscription';
                    $phpmailer->Body    = '<h1>Bienvenue !</h1><p>Votre compte a bien été créé.</p>';
                    $phpmailer->AltBody = 'Votre compte a bien été créé.';

                    $phpmailer->send();
                } catch (Exception $e) {
                    // pour le projet, on ignore l'erreur
                }

                header('Location: /loginForm');
                exit;
            }
        }

        header('Location: /registerForm');
        exit;
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        header('Location: /loginForm');
        exit;
    }

    public function forgetPassword(): void
    {
        $render = new Render("forgetPassword", "backoffice");
        $render->render();
    }

    public function resetPassword(): void
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (!empty($_POST['email'])) {

                $email = strtolower(trim($_POST['email']));

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors = ["Votre email n'est pas correct"];
                    $render = new Render("forgetPassword", "backoffice");
                    $render->assign("errors", $errors);
                    $render->render();
                    return;
                }

                $userModel = new User();
                $user      = $userModel->getOneBy(["email" => $email]);

                if ($user) {
                    $token = bin2hex(random_bytes(16));
                    $userModel->updateTokenEmail($email, $token);

                    $activationLink = "http://localhost:8080/activation?email=" . urlencode($email) . "&token=" . urlencode($token);

                    $phpmailer = new PHPMailer(true);
                    try {
                        $phpmailer->isSMTP();
                        $phpmailer->Host     = 'mailpit';
                        $phpmailer->SMTPAuth = false;
                        $phpmailer->Port     = 1025;

                        $phpmailer->setFrom('no-reply@example.com', 'Mini CMS');
                        $phpmailer->addAddress($email);

                        $phpmailer->isHTML(true);
                        $phpmailer->Subject = 'Réinitialisation de mot de passe';
                        $phpmailer->Body    = '<p>Cliquez sur ce lien pour réinitialiser votre mot de passe :</p><p><a href="' . $activationLink . '">Réinitialiser</a></p>';
                        $phpmailer->AltBody = 'Copiez/collez ce lien dans votre navigateur : ' . $activationLink;

                        $phpmailer->send();
                    } catch (Exception $e) {
                        // ignore pour le projet
                    }
                }

                $render = new Render("forgetPassword", "backoffice");
                $render->assign("info", "Si un compte existe avec cet email, un lien a été envoyé.");
                $render->render();
                return;
            }
        }

        header('Location: /forgetPassword');
        exit;
    }

    public function linkResetPassword(): void
    {
        if (isset($_GET["email"]) && isset($_GET["token"])) {
            $email = $_GET["email"];
            $token = $_GET["token"];

            $userModel = new User();
            $user      = $userModel->getOneBy(["email" => $email]);

            if ($user && $user["token"] === $token) {
                $render = new Render("linkResetPassword", "backoffice");
                $render->assign("email", $email);
                $render->render();
                return;
            }
        }

        header('Location: /forgetPassword');
        exit;
    }

    public function updatePassword(): void
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (!empty($_POST["email"]) && !empty($_POST["pwd"]) && !empty($_POST["pwdConfirm"])) {

                $email           = strtolower(trim($_POST["email"]));
                $password        = $_POST["pwd"];
                $passwordConfirm = $_POST["pwdConfirm"];

                $errors = [];

                if (
                    strlen($password) < 8 ||
                    !preg_match('/[a-z]/', $password) ||
                    !preg_match('/[A-Z]/', $password) ||
                    !preg_match('/[0-9]/', $password)
                ) {
                    $errors[] = "Votre mot de passe doit faire au minimum 8 caractères avec min, maj, chiffres";
                }

                if ($password !== $passwordConfirm) {
                    $errors[] = "Votre mot de passe de confirmation ne correspond pas";
                }

                if (!empty($errors)) {
                    $render = new Render("linkResetPassword", "backoffice");
                    $render->assign("email", $email);
                    $render->assign("errors", $errors);
                    $render->render();
                    return;
                }

                $userModel = new User();
                $userModel->updatePasswordEmail($email, $password);
                header("Location: /loginForm");
                exit;
            }
        }

        header("Location: /forgetPassword");
        exit;
    }
}
