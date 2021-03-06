<?php

namespace App\Bot\Filters;

class LongTextFilter extends AbstractFilter
{

    protected $type = 'self';

    public function transform(array $message, array $item): array
    {
        if (mb_strlen($message['text']) >= 2048) {
            $message['text'] = mb_substr($message['text'], 0, 1536).'...';
            $message['text'] .= PHP_EOL . PHP_EOL . "* продолжение на r/{$message['subreddit']} *";
        }
        $message['text'] = str_replace("\n&amp;#x200B;\n", '', $message['text']);
        $message['text'] = str_replace("&amp;#x200B;\n", '', $message['text']);
        $message['text'] = str_replace("\n&amp;#x200B;", '', $message['text']);

        return ['message' => $message, 'item' => $item];
    }

}
