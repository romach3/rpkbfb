<?php

require_once 'vendor/autoload.php';

use App\Bot\Bot;
use Dotenv\Dotenv;

$dotenv = Dotenv::create(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR);
$dotenv->load();

(new Bot())->run();
