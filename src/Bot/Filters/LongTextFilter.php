<?php

namespace App\Bot\Filters;

class LongTextFilter extends AbstractFilter
{

    protected $type = 'self';

    public function transform(array $message, array $item): array
    {
        if (mb_strlen($message['text']) >= 2048) {
            $message['text'] = mb_substr($message['text'], 0, 1536).'...';
            $message['text'] .= PHP_EOL . PHP_EOL . '* продолжение на r/Pikabu *';
        }
        $message['text'] = str_replace("\n&amp;#x200B;\n", '', $message['text']);

        return ['message' => $message, 'item' => $item];
    }

}
