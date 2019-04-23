<?php

namespace App\Bot\Commands;

class ClearCache
{

    public function handle(): void
    {
        unlink('./storage/messages.json');
        unlink('./storage/posts.json');
    }
}
