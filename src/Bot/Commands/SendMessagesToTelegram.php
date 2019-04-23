<?php

namespace App\Bot\Commands;

use App\Bot\Services\TelegramService;
use Telegram\Bot\Exceptions\TelegramSDKException;

class SendMessagesToTelegram
{
    protected $telegramService;
    protected $config;


    public function __construct(TelegramService $telegramService, array $config)
    {
        $this->telegramService = $telegramService;
        $this->config = $config;
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
                    $this->telegramService->sendMessage($message, [
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
            if ($counter >= 15) {
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
        $ids = array_slice($ids, -1 * $this->config['sendedListSize']);
        file_put_contents('./storage/sended.json', json_encode($ids));
    }

    protected function send(array $message): void
    {
        if ($message['hint'] === 'self') {
            $this->telegramService->sendMessage($message, [
                'parse_mode' => 'Markdown',
                'disable_web_page_preview' => true,
                'text' => "*{$message['title']}*" . PHP_EOL . PHP_EOL
                    . $message['text'] . PHP_EOL . PHP_EOL
                    . $this->getMessageStatus($message)
            ]);
        } else if ($message['hint'] === 'image') {
            $this->telegramService->sendPhoto($message, [
                'photo' => $message['url'],
                'parse_mode' => 'Markdown',
                'caption' => "*{$message['title']}*" . PHP_EOL . PHP_EOL
                    . $this->getMessageStatus($message)
            ]);
        } else if ($message['hint'] === 'hosted:video') {
            if ($message['url'] !== null) {
                $this->telegramService->sendVideo($message, [
                    'photo' => $message['thumbnail'],
                    'parse_mode' => 'Markdown',
                    'video' => $message['url'],
                    'caption' => "*{$message['title']}*" . PHP_EOL . PHP_EOL
                        . $this->getMessageStatus($message)
                ]);
            } else {
                $this->telegramService->sendPhoto($message, [
                    'photo' => $message['thumbnail'],
                    'parse_mode' => 'Markdown',
                    'caption' => "*{$message['title']}*" . PHP_EOL . PHP_EOL
                        . '* Просмотр этого видео возможен только на Reddit *' . PHP_EOL . PHP_EOL
                        . $this->getMessageStatus($message)
                ]);
            }
        } else if ($message['hint'] === 'gif:video') {
            $this->telegramService->sendVideo($message, [
                'photo' => $message['thumbnail'],
                'parse_mode' => 'Markdown',
                'video' => $message['url'],
                'caption' => "*{$message['title']}*" . PHP_EOL . PHP_EOL
                    . $this->getMessageStatus($message)
            ]);
        } else if ($message['hint'] === 'rich:video') {
            $this->telegramService->sendPhoto($message, [
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
