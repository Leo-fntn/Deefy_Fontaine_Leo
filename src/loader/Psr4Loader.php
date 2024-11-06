<?php

class Psr4Loader
{
    private string $prefixNamespace, $cheminRepertoire;

    function __construct(string $prefixNamespace, string $cheminRepertoire)
    {
        $this->prefixNamespace = $prefixNamespace;
        $this->cheminRepertoire = $cheminRepertoire;
    }

    function loadClass(string $class)
    {
        // \iutnc\deefy\audio\tracks\AlbumTrack
        $class = substr($class, strlen($this->prefixNamespace));
        // \audio\tracks\AlbumTrack
        $class = str_replace('\\', '/', $class);
        // /audio/tracks/AlbumTrack
        $class = $this->cheminRepertoire . '/' . $class . '.php';
        // /src/classes/audio/tracks/AlbumTrack.php

        if (file_exists($class)) {
            require_once $class;
        }
    }

    function register()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }
}