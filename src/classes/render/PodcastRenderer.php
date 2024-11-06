<?php
declare (strict_types = 1);

namespace iutnc\deefy\render;

class PodcastRenderer extends AudioTrackRenderer
{
    public function court(): string
    {
        $track = $this->track;
        $titre = filter_var($track->titre, FILTER_SANITIZE_SPECIAL_CHARS);
        $auteur = filter_var($track->auteur, FILTER_SANITIZE_SPECIAL_CHARS);
        $duree = filter_var($track->duree, FILTER_SANITIZE_NUMBER_INT);
        return "{$titre} - by {$auteur} - {$duree}s";
    }

    public function long(): string
    {
        $track = $this->track;
        $titre = filter_var($track->titre, FILTER_SANITIZE_SPECIAL_CHARS);
        $auteur = filter_var($track->auteur, FILTER_SANITIZE_SPECIAL_CHARS);
        $date = filter_var($track->date, FILTER_SANITIZE_SPECIAL_CHARS);
        $duree = filter_var($track->duree, FILTER_SANITIZE_NUMBER_INT);
        $genre = filter_var($track->genre, FILTER_SANITIZE_SPECIAL_CHARS);
        $nomFichierAudio = filter_var($track->nomFichierAudio, FILTER_SANITIZE_SPECIAL_CHARS);

        return "{$titre} - by {$auteur} ({$date}) - {$duree}s : {$genre}
        <audio src='{$nomFichierAudio}' controls></audio>";
    }
}