<?php
use App\Bot\Filters\GifImageFilter;
use App\Bot\Filters\HostedVideoFilter;
use App\Bot\Filters\LongTextFilter;
use App\Bot\Filters\MFilter;
use App\Bot\Filters\NSFWThumbnailFilter;
use App\Bot\Filters\PrimaryLinkFilter;
use App\Bot\Filters\RichVideoFilter;

return [
    'tg' => [
        'proxy' => env('GUZZLE_CLIENT_PROXY'),
        'token' => env('BOT_TOKEN'),
    ],
    'filters' => [
        PrimaryLinkFilter::class,
        LongTextFilter::class,
        NSFWThumbnailFilter::class,
        GifImageFilter::class,
        HostedVideoFilter::class,
        RichVideoFilter::class,
        MFilter::class
    ],
    'channels' => [
        [
            'name' => '@rpikabufeed',
            'nsfw' => false,
        ],
        [
            'name' => '@rpikabufeed_nsfw',
            'nsfw' => true,
        ],
    ],
    'subreddits' => [
        'https://www.reddit.com/r/Pikabu/hot/',
    ],
    'sendedListSize' => 500,
    'disableNotification' => true,
    'maxMessagesOnSession' => 15,
    'pages' => 1
];
