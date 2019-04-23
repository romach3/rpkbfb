<?php

namespace App\Bot\Filters;

class PrimaryLinkFilter extends AbstractFilter
{

    protected $type = 'link';

    public function transform(array $message, array $item): array
    {
        if (isset($item['data']['crosspost_parent_list'][0])) {
           if (!isset($item['data']['crosspost_parent_list'][0]['post_hint'])) {
               if (mb_substr($message['url'], -4) === '.gif' || mb_substr($message['url'], -5) === '.gifv') {
                   $message['hint'] = 'image';
               }
           } else if ($item['data']['crosspost_parent_list'][0]['post_hint'] !== 'link') {
               $message['thumbnail'] = $item['data']['crosspost_parent_list'][0]['thumbnail'];
               $message['text'] = $item['data']['crosspost_parent_list'][0]['selftext'];
               $message['hint'] = $item['data']['crosspost_parent_list'][0]['post_hint'] ?? 'self';
               $message['url'] = $item['data']['crosspost_parent_list'][0]['url'];
               $item['data'] = $item['data']['crosspost_parent_list'][0];
           }
        }
        return ['message' => $message, 'item' => $item];
    }

}
