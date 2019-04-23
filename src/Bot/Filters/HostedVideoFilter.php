<?php

namespace App\Bot\Filters;

class HostedVideoFilter extends AbstractFilter
{

    protected $type = 'hosted:video';

    public function transform(array $message, array $item): array
    {
        $message['url'] = $item['data']['media']['reddit_video']['fallback_url'] ?? null;

        return ['message' => $message, 'item' => $item];
    }

}
