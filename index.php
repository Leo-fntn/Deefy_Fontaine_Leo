<?php

declare(strict_types=1);
require_once 'vendor/autoload.php';
use iutnc\deefy\dispatch\Dispatcher;

iutnc\deefy\repository\DeefyRepository::setConfig( 'config.db.ini' );

$dispatcher = new Dispatcher();

$dispatcher->run();