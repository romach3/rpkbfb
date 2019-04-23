<?php

namespace App\Bot;

use App\Bot\Commands\ClearCache;
use App\Bot\Commands\GetPostsFromReddit;
use App\Bot\Commands\PrepareMessagesToSend;
use App\Bot\Commands\SendMessagesToTelegram;
use App\Bot\Services\TelegramService;

class Bot
{
    protected $telegramService;
    protected $config;

    public function __construct()
    {
        $this->config = include './config.php';
        $this->telegramService = new TelegramService($this->config);
    }

    public function run()
    {
        (new GetPostsFromReddit())->handle();
        (new PrepareMessagesToSend())->handle();
        (new SendMessagesToTelegram($this->telegramService, $this->config))->handle();
        (new ClearCache())->handle();
    }

}
