<?php

namespace iutnc\deefy\auth;
use iutnc\deefy\exception\AuthnException;
use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\user\User;

class AuthnProvider{

    public static function signin(string $email, string $password): void
    {
        $r = DeefyRepository::getInstance();
        $user = $r->getUserFromMail($email); // recupere le mdp hashé dans la bd

        if (!password_verify($password, $user->password))
            throw new AuthnException("Mot de passe incorrect");

        $_SESSION['user'] = serialize($user);
    }

    public static function register(string $email, string $password)  // enregistre un nouvel utilisateur
    {
        $r = DeefyRepository::getInstance();
        try {
            $user = $r->getUserFromMail($email);
        } catch (AuthnException $e) {
            $user = null;
        }

        if (strlen($password) < 10){ // password trop court
            throw new AuthnException("Mot de passe trop court (10 caractères minimum)");
        }

        if($user != null){ // deja un utilisateur avec cet email
            throw new AuthnException("Email déjà utilisé");
        }

        $password = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);

        $user = new User($email, $password);
        $r->insertUser($user);

        $_SESSION['user'] = serialize($user);
    }

    public static function getSignedInUser(): User
    {
        if (!isset($_SESSION['user'])) {
            throw AuthnException::notSignedIn();
        }
        return unserialize($_SESSION['user']);
    }

    public static function signout(): void{
        AuthnProvider::getSignedInUser();
        unset($_SESSION['user']);
    }

}