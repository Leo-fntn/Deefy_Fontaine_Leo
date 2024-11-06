<?php

namespace iutnc\deefy\repository;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\audio\tracks\AudioTrack;
use iutnc\deefy\audio\tracks\PodcastTrack;
use iutnc\deefy\exception\AuthnException;
use iutnc\deefy\exception\AuthzException;
use iutnc\deefy\user\User;

class DeefyRepository
{
    private \PDO $pdo;
    private static ?DeefyRepository $instance = null;
    private static array $config = [];

    private function __construct(array $conf)
    {
        $this->pdo = new \PDO($conf['dsn'], $conf['user'], $conf['pass'],
            [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4'"]);
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new DeefyRepository(self::$config);
        }
        return self::$instance;
    }

    public static function setConfig(string $file)
    {
        $conf = parse_ini_file($file);
        if ($conf === false) {
            throw new \Exception("Error reading configuration file");
        }
        $driver = $conf['driver'];
        $host = $conf['host'];
        $database = $conf['database'];
        $dsn = "$driver:host=$host;dbname=$database";
        self::$config = ['dsn' => $dsn, 'user' => $conf['user'], 'pass' => $conf['pass']];
    }

    public function findPlaylistById(int $id): Playlist{

        $id=filter_var($id, FILTER_VALIDATE_INT);

        $requestPlaylist = $this->pdo->prepare("SELECT * FROM playlist WHERE id = ?");
        $requestPlaylist->bindParam(1, $id);
        $requestPlaylist->execute();
        $pl = $requestPlaylist->fetch();

        if ($pl === false) {
            throw new \Exception("Playlist not found");
        }

        $id = $pl['id'];
        $nom = $pl['nom'];
        $pl = new Playlist($nom);
        $pl->setId($id);

        $requestTracks = $this->pdo->prepare("SELECT * FROM track inner join playlist2track on id = id_track 
         WHERE id_pl = ?");
        $requestTracks->bindParam(1, $id);
        $requestTracks->execute();
        while ($track = $requestTracks->fetch()) {
            $idt = $track['id'];
            $titre = $track['titre'];
            $genre = $track['genre'];
            $duree = $track['duree'];
            $filename = $track['filename'];
            $filename = "audio/".$filename;
            if ($track['type'] == 'A') {
                $artiste = $track['artiste_album'];
                $nomAlbum = $track['titre_album'];
                $annee = $track['annee_album'];
                $nbPiste = $track['numero_album'];

                $track = new AlbumTrack($titre, $filename, $nomAlbum, $artiste, $annee);
                $track->setNumeroPiste($nbPiste);
            } else {
                $auteur = $track['auteur_podcast'];
                $date = $track['date_podcast'];
                $track = new PodcastTrack($titre, $filename, $auteur, $date);
            }
            $track->setGenre($genre);
            $track->setDuree($duree);
            $track->setId($idt);

            $pl->ajouterPiste($track);
        }

        return $pl;
    }

    public function saveEmptyPlaylist(Playlist $pl): Playlist{

        $insertRequest = $this->pdo->prepare("INSERT INTO playlist (nom) VALUES (?)");

        $nom=filter_var($pl->titre, FILTER_SANITIZE_SPECIAL_CHARS);

        $insertRequest->bindParam(1, $nom);
        $insertRequest->execute();

        // recupere l'id de la playlist
        $id = $this->pdo->lastInsertId();
        $pl->setId($id);

        return $pl;
    }

    public function getUserFromMail(string $mail) : User{
        $mail = filter_var($mail, FILTER_VALIDATE_EMAIL);
        $request = $this->pdo->prepare("SELECT * FROM user WHERE email = ?");
        $request->bindParam(1, $mail);
        $request->execute();
        $user = $request->fetch();
        if ($user === false) {
            throw new AuthnException("Aucun utilisateur trouvé");
        }

        $id = $user['id'];
        $role = $user['role'];

        $user = new User($user['email'], $user['passwd']);
        $user->setId($id);
        $user->setRole($role);
        return $user;
    }

    public function insertUser(User $user) :User{
//        $id = $this->pdo->lastInsertId();
        $mail = filter_var($user->email, FILTER_SANITIZE_EMAIL);
        $passwd = $user->password;

        $insertRequest = $this->pdo->prepare("INSERT INTO user (email, passwd) VALUES (?, ?)");
//        $insertRequest->bindParam(1, $id);
        $insertRequest->bindParam(1, $mail);
        $insertRequest->bindParam(2, $passwd);
        $insertRequest->execute();

        $id = $this->pdo->lastInsertId();
        $user->setId($id);
        $user->setRole(User::$STANDARD);

        return $user;

    }


    public function allPlaylists() : array{
        $request = $this->pdo->prepare("SELECT * FROM playlist");
        $request->execute();
        $playlists = [];
        while ($pl = $request->fetch()) {
            $id = $pl['id'];
            $nom = $pl['nom'];
            $pl = new Playlist($nom);
            $pl->setId($id);

            $playlists[] = $pl;
        }
        return $playlists;
    }


    public function getPlaylistsByUser(User $user) : array{
        $role = $user->role;
        if ($role === User::$ADMIN) {
            $request = $this->pdo->prepare("SELECT * FROM user2playlist");
        }
        else{
            $id = $user->id;
            $request = $this->pdo->prepare("SELECT * FROM user2playlist WHERE id_user = ?");
            $request->bindParam(1, $id);
        }
        $request->execute();
        $playlists = [];
        while ($pl = $request->fetch()) {
            $id = $pl['id_pl'];
            $pl = $this->findPlaylistById($id);
            $playlists[] = $pl;
        }
        return $playlists;
    }


    public function insertTrack(AudioTrack $track) : AudioTrack{
        $titre = $track->titre;
        $genre = filter_var($track->genre, FILTER_SANITIZE_SPECIAL_CHARS);
        $duree = filter_var($track->duree, FILTER_VALIDATE_INT);
        $filename = filter_var($track->nomFichierAudio, FILTER_SANITIZE_SPECIAL_CHARS);
        $filename = substr($filename, 6);

        if ($track instanceof AlbumTrack) {
            $artiste = filter_var($track->artiste, FILTER_SANITIZE_SPECIAL_CHARS);
            $nomAlbum = filter_var($track->album, FILTER_SANITIZE_STRING);
            $anneeAlbum = filter_var($track->annee, FILTER_VALIDATE_INT);

            // trouver le numéro dans l'album
            $request = $this->pdo->prepare("SELECT max(numero_album) as max FROM track WHERE artiste_album = ? AND titre_album = ?");
            $request->bindParam(1, $artiste);
            $request->bindParam(2, $nomAlbum);
            $request->execute();
            $max = $request->fetch();
            $numeroPiste = $max['max'] + 1;

            $track->setNumeroPiste($numeroPiste);

            $insertRequest = $this->pdo->prepare("INSERT INTO track (titre, genre, duree, filename, type, artiste_album, titre_album, annee_album, numero_album) VALUES (?,?,?,?,'A',?,?,?,?)");
            $insertRequest->bindParam(5, $artiste);
            $insertRequest->bindParam(6, $nomAlbum);
            $insertRequest->bindParam(7, $anneeAlbum);
            $insertRequest->bindParam(8, $numeroPiste);
        }
        else{
            $auteur = filter_var($track->auteur, FILTER_SANITIZE_SPECIAL_CHARS);
            $date = filter_var($track->date, FILTER_SANITIZE_SPECIAL_CHARS);

            $insertRequest = $this->pdo->prepare("INSERT INTO track (titre, genre, duree, filename, type, auteur_podcast, date_podcast) VALUES (?,?,?,?,'P',?,?)");
            $insertRequest->bindParam(5, $auteur);
            $insertRequest->bindParam(6, $date);
        }
        $insertRequest->bindParam(1, $titre);
        $insertRequest->bindParam(2, $genre);
        $insertRequest->bindParam(3, $duree);
        $insertRequest->bindParam(4, $filename);

        $insertRequest->execute();

        $id = $this->pdo->lastInsertId();
        $track->setId($id);

        return $track;
    }


    public function addTrackPlaylist(Playlist $pl, AudioTrack $track){
        $id_pl = $pl->id;
        $id_track = $track->id;
        // numero dans la playlist
        $numero = $pl->nbPistes;

        $insertRequest = $this->pdo->prepare("INSERT INTO playlist2track VALUES (?, ?, ?)");
        $insertRequest->bindParam(1, $id_pl);
        $insertRequest->bindParam(2, $id_track);
        $insertRequest->bindParam(3, $numero);
        $insertRequest->execute();
    }


    public function getIdUserFromPlaylist(int $id): int{
        $request = $this->pdo->prepare("SELECT * FROM user2playlist WHERE id_pl = ?");
        $request->bindParam(1, $id);
        $request->execute();
        $user = $request->fetch();
        if ($user === false) {
            throw new AuthzException("User not found");
        }
        return $user['id_user'];
    }

    public function getUserFromId(int $id): User{
        $request = $this->pdo->prepare("SELECT * FROM user WHERE id = ?");
        $request->bindParam(1, $id);
        $request->execute();
        $user = $request->fetch();
        if ($user === false) {
            throw new AuthzException("User not found");
        }

        $id = $user['id'];
        $role = $user['role'];

        $user = new User($user['email'], $user['passwd']);
        $user->setId($id);
        $user->setRole($role);
        return $user;
    }


    public function putPlaylist2User(Playlist $pl, User $user){
        $id_pl = $pl->id;
        $id_user = $user->id;

        $insertRequest = $this->pdo->prepare("INSERT INTO user2playlist VALUES (?, ?)");
        $insertRequest->bindParam(1, $id_user);
        $insertRequest->bindParam(2, $id_pl);
        $insertRequest->execute();
    }
}