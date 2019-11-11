<?php

namespace App\Bot\Commands;

class GetPostsFromReddit
{
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function handle(): void
    {
        $posts = [];
        $context = stream_context_create([
            "http" => [
                "method" => "GET",
                "header" => "User-Agent: rpikabufeed_bot\r\n"
            ]
        ]);
        foreach ($this->config['subreddits'] as $uri) {
            $response = json_decode(file_get_contents($uri.'.json?limit=100', false, $context), true);
            $posts = array_merge($posts, $response['data']['children']);
            $after = $response['data']['after'];
            for ($i = 2; $i <= $this->config['pages']; $i++) {
                $response = json_decode(file_get_contents($uri.".json?limit=100&after={$after}", false, $context), true);
                $posts = array_merge($posts, $response['data']['children']);
            }
        }
        file_put_contents('./storage/posts.json', json_encode($posts, JSON_UNESCAPED_UNICODE));
    }

}
