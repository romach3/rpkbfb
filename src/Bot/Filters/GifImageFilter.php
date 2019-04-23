<?php

namespace App\Bot\Filters;

class GifImageFilter extends AbstractFilter
{

    protected $type = 'image';

    public function transform(array $message, array $item): array
    {
        if (mb_substr($message['url'], -4) === '.gif' || mb_substr($message['url'], -5) === '.gifv') {
            $message['hint'] = 'gif:video';
        }
        if (mb_substr($message['url'], -5) === '.gifv') {
            $message['url'] = mb_substr($message['url'], 0, mb_strlen($message['url']) - 1);
        }

        return ['message' => $message, 'item' => $item];
    }

}
