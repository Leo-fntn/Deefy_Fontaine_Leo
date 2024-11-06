<?php

namespace iutnc\deefy\auth;

use iutnc\deefy\exception\AuthzException;
use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\user\User;

class Authz{

    private User $user;

    public function __construct(User $user){
        $this->user = $user;
    }

    public function checkRole(int $role)
    {
        if ($this->user->role < $role) {
            throw new AuthzException("Accès refusé");
        }
    }

    public function checkPlaylistOwner(int $idPl){
        if ($this->user->role === User::$ADMIN) {
            return;
        }
        $r = DeefyRepository::getInstance();
        $idOwner = $r->getIdUserFromPlaylist($idPl);
        $owner = $r->getUserFromId($idOwner);

        if ($this->user->id!= $owner->id) {
            throw new AuthzException("Accès refusé : ce n'est pas le propriétaire de la playlist");
        }
    }
}