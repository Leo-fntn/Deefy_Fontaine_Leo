<?php
declare(strict_types=1);

namespace iutnc\deefy\audio\tracks;

use iutnc\deefy\exception\InvalidPropertyNameException;
use iutnc\deefy\exception\InvalidPropertyValueException;

class AudioTrack
{
    private int $id;
    private string $titre;
    private string $genre;
    private int $duree;
    private string $nomFichierAudio;

    public function __construct(string $titre, string $nomFichierAudio)
    {
        $this->id = 0;
        $this->titre = $titre;
        $this->nomFichierAudio = $nomFichierAudio;
    }

    public function __toString(): string
    {
        return json_encode($this);
    }

    public function __get(string $attribut): mixed
    {
        if ($attribut === 'duree' && $this->duree < 0) {
            throw new InvalidPropertyValueException("invalid property value : $attribut = $this->duree");
        }
        elseif (property_exists($this, $attribut)) {
            return $this->$attribut;
        }
        throw new InvalidPropertyNameException("invalid property : $attribut");
    }

    public function setGenre(string $genre): void
    {
        $this->genre = $genre;
    }

    public function setDuree(int $duree): void
    {
        $this->duree = $duree;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}