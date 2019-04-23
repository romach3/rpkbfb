<?php

namespace App\Bot\Filters;

class NSFWThumbnailFilter extends AbstractFilter
{

    protected $type = 'image';

    public function transform(array $message, array $item): array
    {
        if ($message['thumbnail'] === 'nsfw') {
            $message['thumbnail'] = $message['url'];
        }

        return ['message' => $message, 'item' => $item];
    }

}
