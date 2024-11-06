<?php
declare(strict_types=1);

namespace iutnc\deefy\audio\lists;

class Album extends AudioList
{
    protected string $artiste;
    protected int $annee;

    public function __construct(string $nom, array $pistes, string $artiste, int $annee)
    {
        parent::__construct($nom, $pistes);
        $this->artiste = $artiste;
        $this->annee = $annee;
    }

    public function setArtiste(string $artiste): void
    {
        $this->artiste = $artiste;
    }

    public function setAnnee(int $annee): void
    {
        $this->annee = $annee;
    }
}