<?php

namespace FSA\FNS;

use Exception;
use Iterator;

class CheckDecoder implements Iterator
{
    private $checks_json;
    private $checks;

    public function __construct()
    {
    }

    public function load(string $json): void
    {
        $this->checks_json = $json;
        try {
            $this->checks = json_decode($json, null, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $ex) {
            throw new CheckFormatException('Decode error: broken JSON.');
        }
        if (!is_array($this->checks)) {
            throw new CheckFormatException('Format error: array required.');
        }
    }

    public function current(): Check
    {
        return new Check(current($this->checks));
    }

    public function key(): int
    {
        return key($this->checks);
    }

    public function next(): void
    {
        next($this->checks);
    }

    public function rewind(): void
    {
        reset($this->checks);
    }

    public function valid(): bool
    {
        return null !== key($this->checks);
    }
}
