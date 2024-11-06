<?php
declare(strict_types=1);

namespace iutnc\deefy\audio\tracks;

class AlbumTrack extends AudioTrack
{
    protected string $artiste;
    protected int $annee;
    protected string $album;
    protected int $numeroPiste;

    public function __construct(string $titre, string $nomFichierAudio, string $nomAlbum, string $artiste, int $annee)
    {
        parent::__construct($titre, $nomFichierAudio);
        $this->album = $nomAlbum;
        $this->numeroPiste = 0;
        $this->artiste = $artiste;
        $this->annee = $annee;
    }

    public function setNumeroPiste(int $numPiste): void
    {
        $this->numeroPiste = $numPiste;
    }
}