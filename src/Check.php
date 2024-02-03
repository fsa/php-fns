<?php

namespace FSA\FNS;

use DateTimeImmutable;
use DateTimeZone;
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
        if (isset($raw->ticket->document->receipt->items)) {
            foreach ($raw->ticket->document->receipt->items as $item) {
                $this->items[] = new CheckItem($raw->_id, $item);
            }
            foreach ($raw->ticket->document->receipt as $key => $value) {
                if (is_int($value) or is_float($value) or is_string($value)) {
                    $this->properties[$key] = $value;
                }
            }
        } else if (isset($raw->ticket->document->bso->items)) {
            // Бланк строгой отчётности
            foreach ($raw->ticket->document->bso->items as $item) {
                $this->items[] = new CheckItem($raw->_id, $item);
            }
            foreach ($raw->ticket->document->bso as $key => $value) {
                if (is_int($value) or is_float($value) or is_string($value)) {
                    $this->properties[$key] = $value;
                }
            }
        } else {
            throw new CheckFormatException("Check items not found in {$raw->_id}");
        }
    }

    public function current(): CheckItem
    {
        return current($this->items);
    }

    public function key(): int
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

    public function getUser(): string
    {
        isset($this->properties['user']) or throw new CheckFormatException('user not set');

        return $this->properties['user'];
    }

    public function getUserInn(): string
    {
        isset($this->properties['userInn']) or throw new CheckFormatException('userInn not set');

        return trim($this->properties['userInn']);
    }

    public function getDateTime(): DateTimeImmutable
    {
        isset($this->properties['dateTime']) or throw new CheckFormatException('dateTime not set');

        return new DateTimeImmutable($this->properties['dateTime'], new DateTimeZone('UTC'));
    }

    public function getRetailPlace(): ?string
    {
        return isset($this->properties['retailPlace']) ? $this->properties['retailPlace'] : null;
    }

    public function getRetailPlaceAddress(): ?string
    {
        return isset($this->properties['retailPlaceAddress']) ? $this->properties['retailPlaceAddress'] : null;
    }

    public function getOperator(): ?string
    {
        return isset($this->properties['operator']) ? $this->properties['operator'] : null;
    }

    public function getTotalSum(): string
    {
        isset($this->properties['totalSum']) or throw new CheckFormatException('totalSum not set');

        return $this->properties['totalSum'];
    }

    public function getFiscalDriveNumber(): string
    {
        isset($this->properties['fiscalDriveNumber']) or throw new CheckFormatException('fiscalDriveNumber not set');

        return $this->properties['fiscalDriveNumber'];
    }

    public function getFiscalDocumentNumber(): int
    {
        isset($this->properties['fiscalDocumentNumber']) or throw new CheckFormatException('fiscalDocumentNumber not set');

        return $this->properties['fiscalDocumentNumber'];
    }

    public function getFiscalSign(): int
    {
        isset($this->properties['fiscalSign']) or throw new CheckFormatException('fiscalSign not set');

        return $this->properties['fiscalSign'];
    }

    public function __toString()
    {
        return json_encode($this->raw, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
    }
}
