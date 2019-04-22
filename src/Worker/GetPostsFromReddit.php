<?php

namespace App\Worker;

class GetPostsFromReddit
{

    public function handle(): void
    {
        $response = json_decode(file_get_contents('https://www.reddit.com/r/Pikabu/hot/.json?limit=100'), true);
        file_put_contents('./storage/posts.json', json_encode($response['data']['children'], JSON_UNESCAPED_UNICODE));
    }

}
