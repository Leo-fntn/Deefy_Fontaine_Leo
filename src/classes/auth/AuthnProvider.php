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

//    public function checkPasswordStrength(string $pass,
//                                          int $minimumLength): bool {
//
//        $length = (strlen($pass) < $minimumLength); // longueur minimale
//        $digit = preg_match("#[\d]#", $pass); // au moins un digit
//        $special = preg_match("#[\W]#", $pass); // au moins un car. spécial
//        $lower = preg_match("#[a-z]#", $pass); // au moins une minuscule
//        $upper = preg_match("#[A-Z]#", $pass); // au moins une majuscule
//        if (!$length || !$digit || !$special || !$lower || !$upper)return false;
//        return true;
//    }
}