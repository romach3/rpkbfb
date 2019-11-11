<?php
require_once 'vendor/autoload.php';

use App\Bot\Bot;
use Dotenv\Dotenv;
use Jobby\Jobby;

$dotenv = Dotenv::create(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);
$dotenv->load();

$jobby = new Jobby();

try {
    $jobby->add('LoadNewPostsFromReddit', [
        'closure' => function () {
            (new Bot())->run();

            return true;
        },
        'schedule' => '*/15 * * * *',
        'output' => 'storage/LoadNewPostsFromReddit.log',
    ]);
    $jobby->run();
} catch (\Jobby\Exception $e) {
    echo $e->getMessage();
}
