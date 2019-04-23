<?php

namespace App\Bot\Commands;

use App\Bot\Services\MessageFilterService;

class PrepareMessagesToSend
{

    public function handle(): void
    {
        $items = json_decode(file_get_contents('./storage/posts.json'), true);
        $messages = [];
        $sended = $this->getSendedPosts();
        $messagesFilter = new MessageFilterService();
        foreach ($items as $item) {
            if (in_array($item['data']['id'], $sended, true)) {
                continue;
            }
            $message = $messagesFilter->getPreparedMessage($item);
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
