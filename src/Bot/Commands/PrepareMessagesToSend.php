<?php

namespace App\Bot\Commands;

class PrepareMessagesToSend
{

    public function handle(): void
    {
        $items = json_decode(file_get_contents('./storage/posts.json'), true);
        $messages = [];
        $sended = $this->getSendedPosts();
        foreach ($items as $item) {
            if (in_array($item['data']['id'], $sended, true)) {
                continue;
            }
            $message = [
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
            if (mb_strlen($message['text']) >= 4096) {
                $message['text'] = mb_substr($message['text'], 0, 3072);
                $message['text'] .= PHP_EOL . PHP_EOL . '* продолжение на r/Pikabu *' . PHP_EOL;
            }
            if ($message['thumbnail'] === 'nsfw' && $message['hint'] === 'image') {
                $message['thumbnail'] = $message['url'];
            }
            if ($message['hint'] === 'image' && mb_substr($message['url'], -4) === '.gif') {
                $message['hint'] = 'gif:video';
            }
            if ($message['hint'] === 'hosted:video') {
                $message['url'] = $item['data']['media']['reddit_video']['fallback_url'] ?? null;
            } else if ($message['hint'] === 'rich:video') {
                if ($item['data']['media']['oembed']['provider_url'] === 'https://www.youtube.com/') {
                    $message['thumbnail'] = $item['data']['media']['oembed']['thumbnail_url'];
                } else if ($item['data']['media']['oembed']['provider_url'] === 'https://gfycat.com') {
                    $message['thumbnail'] = $item['data']['media']['oembed']['thumbnail_url'];
                }
            }
            $messages[] = $message;
        }
        file_put_contents('./storage/messages.json', json_encode($messages, JSON_UNESCAPED_UNICODE));
    }

    protected function getSendedPosts(): array
    {
        if (file_exists('./storage/sended.json')) {
            return json_decode(file_get_contents('./storage/sended.json'), true);
        }

        return [];
    }

}
