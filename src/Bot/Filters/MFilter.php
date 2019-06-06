<?php

namespace App\Bot\Filters;

class MFilter extends AbstractFilter
{

    protected $type = 'image';

    public function transform(array $message, array $item): array
    {
        if ($this->isExistTags($message, ['[M]', '[М]', '[m]', '[м]'])) {
            $message['hint'] = 'nsfw';
        }
        return ['message' => $message, 'item' => $item];
    }

}
