<?php
declare(strict_types=1);

namespace iutnc\deefy\render;

class AlbumTrackRenderer extends AudioTrackRenderer
{

    public function court(): string {
        $titre = filter_var($this->track->titre, FILTER_SANITIZE_SPECIAL_CHARS);
        $artiste = filter_var($this->track->artiste, FILTER_SANITIZE_SPECIAL_CHARS);
        $album = filter_var($this->track->album, FILTER_SANITIZE_SPECIAL_CHARS);
        $duree = filter_var($this->track->duree, FILTER_SANITIZE_NUMBER_INT);
        return "{$titre} - by {$artiste} from {$album} - {$duree}s";
    }

    public function long(): string {
        $titre = filter_var($this->track->titre, FILTER_SANITIZE_SPECIAL_CHARS);
        $artiste = filter_var($this->track->artiste, FILTER_SANITIZE_SPECIAL_CHARS);
        $album = filter_var($this->track->album, FILTER_SANITIZE_SPECIAL_CHARS);
        $annee = filter_var($this->track->annee, FILTER_SANITIZE_NUMBER_INT);
        $duree = filter_var($this->track->duree, FILTER_SANITIZE_NUMBER_INT);
        $genre = filter_var($this->track->genre, FILTER_SANITIZE_SPECIAL_CHARS);
        $nomFichierAudio = filter_var($this->track->nomFichierAudio, FILTER_SANITIZE_SPECIAL_CHARS);
        return "{$titre} - by {$artiste} (from {$album}, {$annee}) - {$duree}s : {$genre}
        <audio src='{$nomFichierAudio}' controls></audio>";
    }
}