<?php

namespace iutnc\deefy\action;
session_start();

use iutnc\deefy\auth\AuthnProvider;

class SignInAction extends Action {

    public function __construct(){
        parent::__construct();
    }

    public function executeGet(): string{
        try {
            $user = AuthnProvider::getSignedInUser();
        }catch (\Exception $e){
            $user = null;
        }
        if($user!==null){
            return "<p>Vous êtes déjà connecté. <a href='index.php?action=sign-out'>Déconnectez-vous</a> pour changer de compte!</p>";
        }
        return <<<END
            <form method="post" action="?action=sign-in">
            <input type="text" name="email" placeholder="Email">           
            <input type="text" name="password" placeholder="Mot de passe">
            <input type="submit" value="Se connecter">
            </form>
            <p>Vous ne possedez pas de compte ? <a href="index.php?action=add-user">Inscrivez-vous</a></p>          
            END;
    }

    public function executePost(): string{
        $email = filter_var($_POST["email"],FILTER_SANITIZE_EMAIL);
        // ne pas sanitize le password pour ne pas le modifier
        $password = $_POST["password"];
        AuthnProvider::signin($email, $password);

        $user = $_SESSION['user'];
        $user = unserialize($user);

        return "Bienvenue " . $user->email;
    }

}