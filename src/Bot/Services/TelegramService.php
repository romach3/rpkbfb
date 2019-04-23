<?php

namespace App\Bot\Services;

use GuzzleHttp\Client;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\HttpClients\GuzzleHttpClient;

class TelegramService
{
    protected $api;
    protected $config;

    public function __construct(array $config)
    {
        try {
            $client = new Client(['proxy' => getenv('GUZZLE_CLIENT_PROXY') ?? null]);
            $httpClient = new GuzzleHttpClient($client);
            $this->api = new Api(getenv('BOT_TOKEN'), false, $httpClient);
            $this->config = $config;
        } catch (TelegramSDKException $e) {
            /* @todo log */
        }
    }


    public function sendMessage(array $original, array $prepared, $disableNotification = null): void
    {
        $this->prepareToSend($original, $prepared, $disableNotification, function ($prepared) {
            $this->api->sendMessage($prepared);
        });
    }

    public function sendPhoto(array $original, array $prepared, $disableNotification = null): void
    {
        $this->prepareToSend($original, $prepared, $disableNotification, function ($prepared) {
            $this->api->sendPhoto($prepared);
        });
    }

    public function sendVideo(array $original, array $prepared, $disableNotification = null): void
    {
        $this->prepareToSend($original, $prepared, $disableNotification, function ($prepared) {
            $this->api->sendVideo($prepared);
        });
    }

    protected function prepareToSend(array $original, array $prepared, ?bool $disableNotification, callable $callback): void
    {
        foreach ($this->getMessageChannels($original) as $channel) {
            $prepared['chat_id'] = $channel;
            $prepared['disable_notification'] = $disableNotification ?? $this->config['disableNotification'];
            $callback($prepared);
        }
    }

    protected function getMessageChannels(array $original): array
    {
        $allowed = [];
        foreach ($this->config['channels'] as $channel) {
            if ($channel['nsfw']) {
                $allowed[] = $channel['name'];
            } else if (!$channel['nsfw'] && !$original['nsfw']) {
                $allowed[] = $channel['name'];
            }
        }

        return $allowed;
    }

}
