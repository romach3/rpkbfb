<?php

namespace App\Worker;

class ClearCache
{

    public function handle(): void
    {
        unlink('./storage/messages.json');
        unlink('./storage/posts.json');
    }
}
