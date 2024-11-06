<?php

namespace iutnc\deefy\action;
session_start();

use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\exception\AuthnException;

class AddUserAction extends Action{

    public function __construct(){
        parent::__construct();
    }

    public function executeGet() : string{
        try {
            $user = AuthnProvider::getSignedInUser();
        }catch (\Exception $e){
            $user = null;
        }
        if($user!==null){
            return "<p>Vous êtes déjà connecté. <a href='index.php?action=sign-out'>Déconnectez-vous</a> pour changer de compte!</p>";
        }
        // mettre un champ password dans la version finale
        return <<<END
            <form method="post" action="?action=add-user">
            <input type="text" name="email" placeholder="Email">           
            <input type="text" name="password" placeholder="Mot de passe">                       
            <input type = "text" name = "passwordverify" placeholder = "Mot de passe confirmation">
            <input type="submit" value="Ajouter">
            </form>
            <p>Vous possedez déjà un compte ? <a href="index.php?action=sign-in">Connectez-vous</a></p>
            END;
    }

    public function executePost() : string{
        $email = filter_var($_POST["email"],FILTER_SANITIZE_EMAIL);
        // ne pas sanitize le password pour ne pas le modifier
        $password = $_POST["password"];
        $passwordverify = $_POST["passwordverify"];

        if($password != $passwordverify){
            return "Les mots de passe ne correspondent pas" . $this->executeGet();
        }

        try{
            AuthnProvider::register($email, $password);
        }catch(AuthnException $e){
            return "Erreur lors de l'ajout de l'utilisateur : ".$e->getMessage()    . $this->executeGet();
        }
        return "Utilisateur ajouté avec succès";
    }
}