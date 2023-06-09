<?php

namespace FSA\FNS;

use Iterator;

class Check implements Iterator
{
    private array $properties;
    private array $items = [];

    public function __construct(private object $raw)
    {
        if (!isset($raw->_id)) {
            throw new CheckFormatException("Check items not found. Wrong file?");
        }
        if (!isset($raw->ticket->document->receipt->items)) {
                throw new CheckFormatException("Check items not found in {$raw->_id}");
        }
        foreach ($raw->ticket->document->receipt->items as $item) {
            $this->items[] = new CheckItem($raw->_id, $item);
        }
        foreach ($raw as $key => $value) {
            if (is_int($value) or is_float($value) or is_string($value)) {
                $this->properties[$key] = $value;
            }
        }
    }

    public function current()
    {
        return current($this->items);
    }

    public function key()
    {
        return key($this->items);
    }

    public function next(): void
    {
        next($this->items);
    }

    public function rewind(): void
    {
        reset($this->items);
    }

    public function valid(): bool
    {
        return null !== key($this->items);
    }

}
