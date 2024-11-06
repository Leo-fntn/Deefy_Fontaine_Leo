<?php
declare(strict_types=1);

namespace iutnc\deefy\audio\lists;

use iutnc\deefy\audio\tracks\AudioTrack;

class Playlist extends AudioList
{
    public function __construct(string $nom, array $pistes = [])
    {
        parent::__construct($nom, $pistes);
    }

    public function ajouterPiste(AudioTrack $piste): void
    {
        if (!in_array($piste, $this->pistes)) {
            $this->pistes[] = $piste;
            $this->maj();
        }
    }

    public function supprimerPiste(int $indice): void
    {
        if (array_key_exists($indice, $this->pistes)) {
            unset($this->pistes[$indice]);
            $this->maj();
        }
    }

    public function ajouterListePiste(array $pistes): void
    {
        $this->pistes = $this->pistes + $pistes;
        $this->maj();
    }
}