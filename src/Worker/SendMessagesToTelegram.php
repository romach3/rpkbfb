<?php

namespace App\Worker;

use GuzzleHttp\Client;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\HttpClients\GuzzleHttpClient;

class SendMessagesToTelegram
{
    protected $api;

    public function __construct()
    {
        try {
            $client = new Client(['proxy' => getenv('GUZZLE_CLIENT_PROXY') ?? null]);
            $httpClient = new GuzzleHttpClient($client);
            $this->api = new Api(getenv('BOT_TOKEN'), false, $httpClient);
        } catch (TelegramSDKException $e) {
            /* @todo log */
        }
    }

    public function handle(): void
    {
        $messages = json_decode(file_get_contents('./storage/messages.json'), true);
        $counter = 0;
        foreach ($messages as $message) {
            if (!$this->isSended($message)) {
                try {
                    $this->send($message);
                } catch (TelegramSDKException $exception) {
                    echo $message['id'] . PHP_EOL;
                    echo $message['link'] . PHP_EOL;
                    echo $exception->getMessage() . PHP_EOL;
                    /* @todo log */
                    /* Ошибка возникает, при отправке слишком больших сообщений */
                    $this->api->sendMessage([
                        'chat_id' => '@rpikabufeed_nsfw',
                        'parse_mode' => 'Markdown',
                        'disable_web_page_preview' => true,
                        'text' => "*{$message['title']}*" . PHP_EOL . PHP_EOL
                            . 'Пост не отправился в TG, повод зайти на r/Pikabu )' . PHP_EOL . PHP_EOL
                            . $this->getMessageStatus($message)
                    ]);
                    $counter++;
                }
                $this->saveId($message);
                $counter++;
            }
            if ($counter >= 25) {
                break;
            }
        }
    }

    protected function isSended(array $message): bool
    {
        $ids = [];
        if (file_exists('./storage/sended.json')) {
            $ids = json_decode(file_get_contents('./storage/sended.json'), true);
        }

        return in_array($message['id'], $ids, true);
    }

    protected function saveId(array $message): void
    {
        $ids = [];
        if (file_exists('./storage/sended.json')) {
            $ids = json_decode(file_get_contents('./storage/sended.json'), true);
        }
        $ids[] = $message['id'];
        $ids = array_slice($ids, -200);
        file_put_contents('./storage/sended.json', json_encode($ids));
    }

    protected function send(array $message): void
    {
        if ($message['hint'] === 'self') {
            $this->api->sendMessage([
                'chat_id' => '@rpikabufeed_nsfw',
                'parse_mode' => 'Markdown',
                'disable_web_page_preview' => true,
                'text' => "*{$message['title']}*" . PHP_EOL
                    . $message['text'] . PHP_EOL
                    . $this->getMessageStatus($message)
            ]);
        } else if ($message['hint'] === 'image') {
            $this->api->sendPhoto([
                'chat_id' => '@rpikabufeed_nsfw',
                'photo' => $message['url'],
                'parse_mode' => 'Markdown',
                'caption' => "*{$message['title']}*" . PHP_EOL . PHP_EOL
                    . $this->getMessageStatus($message)
            ]);
        } else if ($message['hint'] === 'hosted:video') {
            if ($message['url'] !== null) {
                $this->api->sendVideo([
                    'chat_id' => '@rpikabufeed_nsfw',
                    'photo' => $message['thumbnail'],
                    'parse_mode' => 'Markdown',
                    'video' => $message['url'],
                    'caption' => "*{$message['title']}*" . PHP_EOL . PHP_EOL
                        . $this->getMessageStatus($message)
                ]);
            } else {
                $this->api->sendPhoto([
                    'chat_id' => '@rpikabufeed_nsfw',
                    'photo' => $message['thumbnail'],
                    'parse_mode' => 'Markdown',
                    'caption' => "*{$message['title']}*" . PHP_EOL . PHP_EOL
                        . '* Просмотр этого видео возможен только на Reddit *' . PHP_EOL . PHP_EOL
                        . $this->getMessageStatus($message)
                ]);
            }
        } else if ($message['hint'] === 'gif:video') {
            $this->api->sendVideo([
                'chat_id' => '@rpikabufeed_nsfw',
                'photo' => $message['thumbnail'],
                'parse_mode' => 'Markdown',
                'video' => $message['url'],
                'caption' => "*{$message['title']}*" . PHP_EOL . PHP_EOL
                    . $this->getMessageStatus($message)
            ]);
        } else if ($message['hint'] === 'rich:video') {
            $this->api->sendPhoto([
                'chat_id' => '@rpikabufeed_nsfw',
                'photo' => $message['thumbnail'],
                'parse_mode' => 'Markdown',
                'caption' => "*{$message['title']}*" . PHP_EOL . PHP_EOL
                    . '* Просмотр этого видео возможен только на Reddit *' . PHP_EOL . PHP_EOL
                    . $this->getMessageStatus($message)
            ]);
        }
    }

    protected function getMessageStatus(array $message): string
    {
        return "⬆ ️{$message['score']} 📝 {$message['num_comments']} 🔗 [r/Pikabu]({$message['link']}) 🔗 [{$message['author']}]({$message['author_url']})";
    }

}
