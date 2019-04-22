<?php
require_once 'vendor/autoload.php';

use Telegram\Bot\Api;
use Dotenv\Dotenv;

$dotenv = Dotenv::create(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR);
$dotenv->load();

try {
    $telegram = new Api(getenv('BOT_TOKEN'));
    /* @todo stuff */
    /* @see https://telegram-bot-sdk.readme.io/docs/commands-system */
    $telegram->commandsHandler(true);
} catch (\Telegram\Bot\Exceptions\TelegramSDKException $e) {
    /* @todo log */
}
