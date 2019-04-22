<?php

require_once 'vendor/autoload.php';

use App\Worker\GetPostsFromReddit;
use App\Worker\PrepareMessagesToSend;
use Dotenv\Dotenv;

$dotenv = Dotenv::create(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR);
$dotenv->load();

//(new GetPostsFromReddit())->handle();
//(new PrepareMessagesToSend())->handle();

(new \App\Worker\SendMessagesToTelegram())->handle();
