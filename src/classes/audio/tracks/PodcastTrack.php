<?php
declare(strict_types=1);

namespace iutnc\deefy\audio\tracks;

class PodcastTrack extends AudioTrack
{
    protected string $auteur;
    protected string $date;

    public function __construct(string $titre, string $nomFichierAudio, string $auteur, string $date)
    {
        parent::__construct($titre, $nomFichierAudio);
        $this->auteur = $auteur;
        $this->date = $date;
    }
}