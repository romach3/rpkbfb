<?php

namespace App\Bot\Services;

use App\Bot\Filters\AbstractFilter;
use App\Bot\Filters\GifImageFilter;
use App\Bot\Filters\HostedVideoFilter;
use App\Bot\Filters\LongTextFilter;
use App\Bot\Filters\NSFWThumbnailFilter;
use App\Bot\Filters\PrimaryLinkFilter;
use App\Bot\Filters\RichVideoFilter;

class MessageFilterService
{
    protected $filters = [];

    public function __construct()
    {
        $this->filters = [
            new PrimaryLinkFilter(),
            new LongTextFilter(),
            new NSFWThumbnailFilter(),
            new GifImageFilter(),
            new HostedVideoFilter(),
            new RichVideoFilter(),
        ];
    }

    public function getPreparedMessage(array $item): array
    {
        $message = $this->getMessage($item);
        foreach ($this->filters as $filter) {
            /** @var AbstractFilter $filter */
            if ($filter->isMyType($message)) {
                $result = $filter->transform($message, $item);
                $message = $result['message'];
                $item = $result['item'];
            }
        }

        return $message;
    }

    protected function getMessage(array $item): array
    {
        return [
            'id' => $item['data']['id'],
            'title' => $item['data']['title'],
            'link' => "https://www.reddit.com" . $item['data']['permalink'],
            'thumbnail' => $item['data']['thumbnail'],
            'author' => $item['data']['author'],
            'author_url' => "https://www.reddit.com/user/" . $item['data']['author'],
            'num_comments' => $item['data']['num_comments'],
            'score' => $item['data']['score'],
            'text' => $item['data']['selftext'],
            'hint' => $item['data']['post_hint'] ?? 'self',
            'url' => $item['data']['url'],
            'nsfw' => $item['data']['over_18']
        ];
    }

}
