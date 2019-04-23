<?php

namespace App\Bot\Filters;

class RichVideoFilter extends AbstractFilter
{

    protected $type = 'rich:video';

    public function transform(array $message, array $item): array
    {
        // valid for https://www.youtube.com/ https://gfycat.com
        $message['thumbnail'] = $item['data']['media']['oembed']['thumbnail_url'] ?? $message['thumbnail'];

        return ['message' => $message, 'item' => $item];
    }

}
