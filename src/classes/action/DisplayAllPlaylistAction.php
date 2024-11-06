<?php

declare(strict_types=1);

namespace iutnc\deefy\action;

session_start();

use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\auth\Authz;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\render\Renderer;
use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\user\User;

class DisplayAllPlaylistAction extends Action{
    public function executeGet(): string{
        try {
            $user = AuthnProvider::getSignedInUser();
            $authz = new Authz($user);
            $authz->checkRole(User::$STANDARD);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        $r = DeefyRepository::getInstance();
        $playlists = $r->getPlaylistsByUser($user);

        $html = "";
        if (count($playlists) === 0){
            $html = "Vous n'avez pas encore de playlist, <a href='?action=add-playlist'>créez-en une dès maintenant !</a>";
        }
        else {
            $html .= <<<FIN
                <div class="all-playlist">
                <h2>Toutes vos playlists (cliquez pour en choisir une)</h2>
                FIN;
            foreach ($playlists as $pl) {
                $nbPistes = $pl->nbPistes;
                $duree = $pl->duree;
                $titre = $pl->titre;
                $id = $pl->id;
                $html .= <<<FIN
            <a href="?action=display-playlist&id={$id}"> - {$titre} - {$nbPistes} pistes - Durée totale: {$duree}<br></a>
            FIN;
            }
            $html .= '</div>';
        }
        return $html;
    }

    public function executePost(): string{
        return $this->executeGet();
    }
}