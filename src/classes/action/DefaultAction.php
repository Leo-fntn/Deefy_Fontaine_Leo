<?php

declare(strict_types=1);

namespace iutnc\deefy\action;
session_start();
class DefaultAction extends Action{
    public function executeGet() : string{
        if (!isset($_SESSION['user'])){
            return "<p>Bienvenue sur l'application Deefy !</p>"."<p><a href='index.php?action=sign-in'>Connectez-vous</a> pour pouvoir utiliser toutes nos fonctionnalités.</p>"
                ."<p>Passez un bon moment sur notre application !</p>";
        }
        else {
            $user = unserialize($_SESSION['user']);
            return "<p>Bienvenue sur l'application Deefy !</p>"."<p>Vous êtes connecté en tant que ".$user->email.".</p>"
                ."<p>Passez un bon moment sur notre application !</p>";
        }
    }

    public function executePost() : string{
        return $this->executeGet();
    }
}