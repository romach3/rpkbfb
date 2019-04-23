<?php

namespace App\Bot\Filters;

class GifImageFilter extends AbstractFilter
{

    protected $type = 'image';

    public function transform(array $message, array $item): array
    {
        if (mb_substr($message['url'], -4) === '.gif') {
            $message['hint'] = 'gif:video';
        }

        return ['message' => $message, 'item' => $item];
    }

}
