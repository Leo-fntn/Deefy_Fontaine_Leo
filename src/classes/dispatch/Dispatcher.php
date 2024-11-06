<?php

namespace iutnc\deefy\dispatch;

require_once 'vendor/autoload.php';
use iutnc\deefy\action as ACT;

class Dispatcher{
    protected string $action;
    public function __construct(){
        if (!isset($_GET['action'])){
            $this->action = 'default';
        }
        else {
            $this->action = $_GET['action'];
        }
    }

    public function run() : void
    {
        switch ($this->action) {
            case 'all-playlist':
                $act = new ACT\DisplayAllPlaylistAction();
                break;
            case 'add-playlist':
                $act = new ACT\AddPlaylistAction();
                break;
            case 'add-track':
                $act = new ACT\AddTrackAction();
                break;
            case 'sign-in':
                $act = new ACT\SignInAction();
                break;
            case 'sign-out':
                $act = new ACT\SignOutAction();
                break;
            case 'add-user':
                $act = new ACT\AddUserAction();
                break;
            case 'display-playlist':
                $act = new ACT\DisplayPlaylistAction();
                break;
            default:
                $act = new ACT\DefaultAction();
                break;
        }
        $this->renderPage($act());
    }

    private function renderPage(string $html){
        if (isset($_SESSION['playlist'])){
            $id = $_SESSION['playlist']->id;
        }
        else {
            $id = 0;
        }

        if (isset($_SESSION['user'])){
            $user = true;
        }
        else {
            $user = false;
        }

        $final = <<<FIN
        <!DOCTYPE html>
        <html lang='fr'>
        <meta charset='UTF-8'>
        <head>
            <title>Deefy</title>
            <link rel="stylesheet" href="style.css">
        </head>
        <body>
                <ul>
                <!-- Left-aligned links -->
                <div class="nav-left">
                    <li><a href='index.php?action=default'>Deefy</a></li>
                    <li><a href='index.php?action=all-playlist'>Playlist</a></li>
                    <li><a href='index.php?action=add-playlist'>Add Playlist</a></li>
                    <li><a href='index.php?action=add-track'>Add Podcast Track</a></li>
                    <li><a href='index.php?action=display-playlist&id={$id}'>Afficher playlist actuelle</a></li>
                </div>
                
                <!-- Right-aligned link -->
                <div class="nav-right">
        FIN;
                    if ($user){
                        $final .= "<li><a href='index.php?action=sign-out'>Sign Out</a></li>";
                    } else {
                        $final .= "<li><a href='index.php?action=sign-in'>Sign In</a></li>";
                    }
                    $final .= <<<FIN
                </div>
            </ul>
                <div class="content">
                    $html
                </div>
        </body>
        </html>
        FIN;
        echo $final;
    }
}