<?php
declare (strict_types = 1);

namespace iutnc\deefy\audio\lists;

use \iutnc\deefy\exception\InvalidPropertyNameException;

class AudioList
{
    private int $id;
    private string $titre;
    private int $nbPistes;
    private int $duree;
    protected array $pistes;

    public function __construct(string $nom, array $pistes = [])
    {
        $this->id = 0; //id = 0 -> absent de la bd
        $this->titre = $nom;
        $this->pistes = $pistes;
        $this->maj();
    }

    public function maj(){
        $this->nbPistes = count($this->pistes);
        $this->duree = 0;
        foreach ($this->pistes as $piste) {
            $this->duree += $piste->duree;
        }
    }

    public function __get(string $attribut): mixed
    {
        if (property_exists($this, $attribut)) {
            return $this->$attribut;
        }
        throw new InvalidPropertyNameException("invalid property : $attribut");
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}