<?php
declare(strict_types=1);

namespace iutnc\deefy\render;

use \iutnc\deefy\audio\lists\AudioList;
use \iutnc\deefy\audio\tracks\PodcastTrack;

class AudioListRenderer implements Renderer
{
    private AudioList $list;

    public function __construct(AudioList $list)
    {
        $this->list = $list;
    }

    public function render(string $selector = ""): string
    {
        $titre = filter_var($this->list->titre, FILTER_SANITIZE_SPECIAL_CHARS);
        $html = "<div class='track'>";
        $html .= "<h2 class='playlist-title'>{$titre}</h2>";
        $html .= "<div class='track-container'><ul class='track-list'>";
        foreach ($this->list->pistes as $piste) {

            if ($piste instanceof PodcastTrack) {
                $renderer = new PodcastRenderer($piste);
            } else {
                $renderer = new AlbumTrackRenderer($piste);
            }

            $html .= "<li class='track-item'>";
            $html .= "<div class='track-content'>" . $renderer->render($selector) . "</div>";
            $html .= "</li>";
        }
        $html .= "</ul></div>";
        $nbPistes = filter_var($this->list->nbPistes, FILTER_SANITIZE_NUMBER_INT);
        $duree = filter_var($this->list->duree, FILTER_SANITIZE_NUMBER_INT);
        $html .= "<p class='track-info'>Nombre de pistes : {$nbPistes}</p>";
        $html .= "<p class='track-info'>Durée totale : {$duree} secondes</p>";
        return $html;
    }

}