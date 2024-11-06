<?php

namespace iutnc\deefy\user;

use iutnc\deefy\exception\InvalidPropertyNameException;

class User{
    public static int $STANDARD = 1;
    public static int $ADMIN = 100;

    protected int $id;
    protected string $email;
    protected string $password;
    protected int $role;

    public function __construct(string $email, string $password){
        $this->email = $email;
        $this->password = $password;
    }

    public function __get($attribut)
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

    public function setRole(int $role): void
    {
        $this->role = $role;
    }
}