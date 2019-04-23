<?php

namespace App\Bot\Commands;

class GetPostsFromReddit
{

    public function handle(): void
    {
        $response = json_decode(file_get_contents('https://www.reddit.com/r/Pikabu/hot/.json?limit=100'), true);
        $posts = $response['data']['children'];
        $after = $response['data']['after'];
        for ($i = 0; $i < 1; $i++) {
            $response = json_decode(file_get_contents("https://www.reddit.com/r/Pikabu/hot/.json?limit=100&after={$after}"), true);
            $posts = array_merge($posts, $response['data']['children']);
        }
        file_put_contents('./storage/posts.json', json_encode($posts, JSON_UNESCAPED_UNICODE));
    }

}
