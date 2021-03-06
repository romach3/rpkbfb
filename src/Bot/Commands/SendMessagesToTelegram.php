<?php

namespace App\Bot\Commands;

use App\Bot\Services\TelegramService;
use Parsedown;
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
                    try {
                        $this->telegramService->sendMessage($message, [
                            'parse_mode' => 'Markdown',
                            'disable_web_page_preview' => true,
                            'text' => "*{$message['title']}*" . PHP_EOL . PHP_EOL
                                . "Пост не отправился в TG, повод зайти на r/{$message['subreddit']} )" . PHP_EOL . PHP_EOL
                                . $this->getMessageStatus($message)
                        ]);
                    } catch (\Exception $exception) {
                        echo 'Critical Exception' . PHP_EOL;
                        echo $exception->getMessage() . PHP_EOL;
                    }
                    $counter++;
                }
                $this->saveId($message);
                $counter++;
            }
            if ($counter >= $this->config['maxMessagesOnSession']) {
                break;
            }
        }
    }

    protected function isSended(array $message): bool
    {
        $ids = [];
        if (file_exists('./storage/sended.json')) {
            $ids = json_decode(file_get_contents('./storage/sended.json'), true) ?? [];
        }

        return isset($ids[$message['subreddit']]) && in_array($message['id'], $ids[$message['subreddit']], true);
    }

    protected function saveId(array $message): void
    {
        $ids = [];
        if (file_exists('./storage/sended.json')) {
            $ids = json_decode(file_get_contents('./storage/sended.json'), true) ?? [];
        }
        if (!isset($ids[$message['subreddit']])) {
            $ids[$message['subreddit']] = [];
        }
        $ids[$message['subreddit']][] = $message['id'];
        $ids[$message['subreddit']] = array_slice($ids[$message['subreddit']], -1 * $this->config['sendedListSize']);
        file_put_contents('./storage/sended.json', json_encode($ids));
    }

    protected function send(array $message, $recursive = false): void
    {
        if ($message['hint'] === 'self') {
            try {
                $this->telegramService->sendMessage($message, [
                    'parse_mode' => 'Markdown',
                    'disable_web_page_preview' => true,
                    'text' => "*{$message['title']}*" . PHP_EOL . PHP_EOL
                        . html_entity_decode($message['text']) . PHP_EOL . PHP_EOL
                        . $this->getMessageStatus($message)
                ]);
            } catch (TelegramSDKException $exception) {
                if (!$recursive) {
                    $message['text'] = trim(html_entity_decode(strip_tags(Parsedown::instance()
                        ->setBreaksEnabled(true)
                        ->parse($message['text']))));
                    $this->send($message, true);
                } else {
                    throw $exception;
                }
            }
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
        } else if ($message['hint'] === 'nsfw') {
            $this->telegramService->sendMessage($message, [
                'parse_mode' => 'Markdown',
                'disable_web_page_preview' => true,
                'text' => "*{$message['title']}*" . PHP_EOL . PHP_EOL
                    . '* Просмотр этого поста возможен только на Reddit *' . PHP_EOL . PHP_EOL
                    . $this->getMessageStatus($message)
            ]);
        }
    }

    protected function getMessageStatus(array $message): string
    {
        return "➕ ️{$message['score']} 🗯 {$message['num_comments']} 👤 [{$message['author']}]({$message['author_url']}) 🔗 [пост на r/{$message['subreddit']}]({$message['link']})";
    }

}
