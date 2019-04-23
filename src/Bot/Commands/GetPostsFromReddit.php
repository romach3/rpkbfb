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
        foreach ($this->config['subreddits'] as $uri) {
            $response = json_decode(file_get_contents($uri.'.json?limit=100'), true);
            $posts = array_merge($posts, $response['data']['children']);
            $after = $response['data']['after'];
            for ($i = 2; $i <= $this->config['pages']; $i++) {
                $response = json_decode(file_get_contents($uri.".json?limit=100&after={$after}"), true);
                $posts = array_merge($posts, $response['data']['children']);
            }
        }
        file_put_contents('./storage/posts.json', json_encode($posts, JSON_UNESCAPED_UNICODE));
    }

}
