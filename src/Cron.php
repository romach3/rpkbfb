<?php
require_once 'vendor/autoload.php';

use App\Worker\ClearCache;
use App\Worker\GetPostsFromReddit;
use App\Worker\PrepareMessagesToSend;
use App\Worker\SendMessagesToTelegram;
use Dotenv\Dotenv;
use Jobby\Jobby;

$dotenv = Dotenv::create(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR);
$dotenv->load();

$jobby = new Jobby();

try {
    $jobby->add('LoadNewPostsFromReddit', [
        'closure' => function () {
            (new GetPostsFromReddit())->handle();
            (new PrepareMessagesToSend())->handle();
            (new SendMessagesToTelegram())->handle();
            (new ClearCache())->handle();
        },
        'schedule' => '*/5 * * * *',
        'output'   => 'storage/LoadNewPostsFromReddit.log',
    ]);
    $jobby->run();
} catch (\Jobby\Exception $e) {
    echo $e->getMessage();
}
