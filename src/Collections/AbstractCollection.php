<?php

declare(strict_types=1);

namespace KrisKuiper\FileManager\Collections;

use Iterator;

abstract class AbstractCollection implements Iterator
{
    protected array $items = [];

    public function key(): string|int|null
    {
        return key($this->items);
    }

    public function valid(): bool
    {
        return null !== $this->key();
    }

    public function next(): void
    {
        next($this->items);
    }

    public function rewind(): void
    {
        reset($this->items);
    }

    public function toArray(): array
    {
        return $this->items;
    }

    public function count(): int
    {
        return count($this->items);
    }
}
