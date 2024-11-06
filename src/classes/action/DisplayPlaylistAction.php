<?php

namespace iutnc\deefy\action;
session_start();

use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\auth\Authz;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\render\Renderer;
use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\user\User;

class DisplayPlaylistAction extends Action{

    public function executeGet(): string {
        $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
        try {
            $user = AuthnProvider::getSignedInUser();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        if ($id === 0){
            return "Aucune playlist n'a été sélectionnée";
        }
        else {

            $r = DeefyRepository::getInstance();

            try{
                $authz = new Authz($user);
                $authz->checkRole(User::$STANDARD);
                $authz->checkPlaylistOwner($id);
            }catch (\Exception $e){
                return $e->getMessage();
            }

            $playlist = $r->findPlaylistById($id);

            $_SESSION['playlist'] = $playlist;

            $renderer = new AudioListRenderer($playlist);
            $html = $renderer->render(Renderer::long);
        }
        return $html
            . '<a href="?action=add-track"><button>Ajouter une piste</button></a></div>';
    }

    public function executePost(): string
    {
        return $this->executeGet();
    }
}