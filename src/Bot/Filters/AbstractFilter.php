<?php

namespace App\Bot\Filters;

abstract class AbstractFilter
{
    protected $type = null;

    abstract public function transform(array $message, array $item): array;

    public function isMyType(array $message): bool
    {
        if ($this->type === null) {
            return true;
        } elseif (is_string($this->type)) {
            return $message['hint'] === $this->type;
        } elseif (is_array($this->type)) {
            return in_array($message['hint'], $this->type, true);
        }
        return false;
    }

}
