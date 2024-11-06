<?php

declare(strict_types=1);

namespace iutnc\deefy\action;


use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\auth\Authz;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\render\Renderer;
use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\user\User;

session_start();

class AddPlaylistAction extends Action{
    public function executeGet(): string{
        try {
            $user = AuthnProvider::getSignedInUser();
            $authz = new Authz($user);
            $authz->checkRole(User::$STANDARD);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return <<<END
            <div class="add-playlist">
            <h2>Ajouter une playlist</h2>
            <form method="post" action="?action=add-playlist">
            <input type="text" name="name" placeholder="Nom de la playlist">
            <input type="submit" value="Ajouter">
            </form>
            </div>
            END;
    }

    public function executePost(): string{
        $name = filter_var($_POST["name"],FILTER_SANITIZE_SPECIAL_CHARS);
        $playlist = new Playlist($name);

        $r = DeefyRepository::getInstance();

        $playlist = $r->saveEmptyPlaylist($playlist);

        $user = AuthnProvider::getSignedInUser();
        $r->putPlaylist2User($playlist, $user);

        $_SESSION["playlist"] = $playlist;  //enregistrement de la playlist

        $renderer = new AudioListRenderer($playlist);
        return $renderer->render(Renderer::long)
            . '<a href="?action=add-track"><button>Ajouter une piste</button></a></div>'
             ;
    }
}