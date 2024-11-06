<?php

declare(strict_types=1);

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\audio\tracks\PodcastTrack;
use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\auth\Authz;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\render\Renderer;
use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\user\User;

session_start();

class AddTrackAction extends Action{
    public function executeGet(): string{
        try {
            $user = AuthnProvider::getSignedInUser();
        }catch (\Exception $e){
            return $e->getMessage();
        }

        if (!isset($_SESSION['playlist'])) {
            return "Aucune playlist n'a été sélectionnée";
        }
        $playlist = $_SESSION['playlist'];
        $id = $playlist->id;
        try {
            $authz = new Authz($user);
            $authz->checkRole(User::$STANDARD);
            $authz->checkPlaylistOwner($id);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return <<<END
                <div class="add-track">
                <h2>Ajouter une piste</h2>
                <form method='post' action='?action=add-track' enctype='multipart/form-data'>
                <input type="hidden" name="MAX_FILE_SIZE" value="100000000" />
                
                <input type="radio" name="type" value="A" id="M" required> <label for="M">Musique</label>
                <input type="radio" name="type" value="P" id="P" required> <label for="P">Podcast</label>            
                </br></br>              
                
                <label>Titre:
                <input type='text' name='titre' required>
                </label>               
                </br>
                </br>             
                <label>Genre:
                <input type='text' name='genre' required>
                </label>
                <label>Durée:
                <input type='number' name='duree' required>
                </label>
                </br>
                </br>
                <div id = "musique" style="display: none">
                <label>Artiste:
                <input type='text' name='artiste' required>
                </label>
                <label>Année:
                <input type='number' name='annee' required>
                </label>
                </br></br>
                <label>Album:
                <input type='text' name='album' required>
                </label>
                </div>
                
                <div id = "podcast" style="display: none">
                <label>Auteur:
                <input type='text' name='auteur' required>
                </label>
                <label>Date:
                <input type='date' name='date' required>
                </label>
                </div>
                </br>
                <label>Upload du fichier:
                <input name='nomFichier' type='file' required>
                </label>
                </br></br>             
                <input type='submit' value='Ajouter'>
                </form>         
                </div>                    

                <script>
                // Sélectionnez tous les éléments radio avec le nom "type"
                const typeRadios = document.querySelectorAll('input[name="type"]');
                // Ajoutez un écouteur d'événement à chaque radio
                typeRadios.forEach(radio => {
                    radio.addEventListener('click', function() {
                        const musicFields = document.querySelectorAll('#musique input');
                        const podcastFields = document.querySelectorAll('#podcast input');   
                        if (this.value === 'A') {
                            document.getElementById('musique').style.display = 'block';
                            document.getElementById('podcast').style.display = 'none';                                      
                            musicFields.forEach(field => field.required = true);
                            podcastFields.forEach(field => field.required = false);
                        } else {
                            document.getElementById('musique').style.display = 'none';
                            document.getElementById('podcast').style.display = 'block';
                            musicFields.forEach(field => field.required = false);   
                            podcastFields.forEach(field => field.required = true);
                        }
                    });
                });
                </script>
                END;
    }

    public function executePost(): string{
        $uploadDir = 'audio/';

        // Vérification des erreurs d'upload
        if ($_FILES['nomFichier']['error'] !== UPLOAD_ERR_OK) {
            return "Erreur lors de l'upload du fichier: " . $_FILES['nomFichier']['error'];
        }

        // Récupération du nom et chemin temporaire du fichier
        $nomFichier = $_FILES['nomFichier']['name'];
        $tmpName = $_FILES['nomFichier']['tmp_name'];

        // Vérification de l'extension du fichier
        if (substr($nomFichier, -4) !== ".mp3") {
            return "Le fichier doit être un mp3";
        }

        $nomFichier = bin2hex(random_bytes(10)) . '.mp3';

        // Nettoyage et déplacement du fichier
        $nomFichier = $uploadDir . basename($nomFichier);
        $nomFichier = filter_var($nomFichier, FILTER_SANITIZE_STRING);
        if (!move_uploaded_file($tmpName, $nomFichier)) {
            return "Erreur lors du déplacement du fichier.";
        }

        $type = filter_var($_POST['type'], FILTER_SANITIZE_SPECIAL_CHARS);
        $titre = $_POST['titre'];
        $genre = filter_var($_POST['genre'], FILTER_SANITIZE_SPECIAL_CHARS);
        $duree = filter_var($_POST['duree'], FILTER_SANITIZE_NUMBER_INT);
        $duree = intval($duree);

        if ($type === 'A') {
            $artiste = filter_var($_POST['artiste'], FILTER_SANITIZE_SPECIAL_CHARS);
            $annee = filter_var($_POST['annee'], FILTER_SANITIZE_NUMBER_INT);
            $annee = intval($annee);
            $album = filter_var($_POST['album'], FILTER_SANITIZE_STRING);
            $track = new AlbumTrack($titre, $nomFichier, $album, $artiste, $annee);
        }
        else {
            $auteur = filter_var($_POST['auteur'], FILTER_SANITIZE_SPECIAL_CHARS);
            $date = filter_var($_POST['date'], FILTER_SANITIZE_SPECIAL_CHARS);
            $track = new PodcastTrack($titre, $nomFichier,$auteur, $date);
        }

        $track->setGenre($genre);
        $track->setDuree($duree);

        $r = DeefyRepository::getInstance();
        $track = $r->insertTrack($track);

        $playlist = $_SESSION['playlist'];
        $playlist->ajouterPiste($track);
        $_SESSION['playlist'] = $playlist;

        $r -> addTrackPlaylist($playlist, $track);

        $renderer = new AudioListRenderer($playlist);
        return $renderer->render(Renderer::long) . '<a href="?action=add-track"><button>Ajouter une piste</button></a></div>';
    }
}