<?php

namespace iutnc\deefy\action;
session_start();
use iutnc\deefy\auth\AuthnProvider;

class SignOutAction extends Action{

    public function executeGet(): string{
        try {
            AuthnProvider::signout();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return "Vous êtes déconnecté";
    }

    public function executePost(): string{
        return $this->executeGet();
    }
}