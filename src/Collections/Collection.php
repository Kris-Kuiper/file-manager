<?php

declare(strict_types=1);

namespace KrisKuiper\FileManager\Collections;

use KrisKuiper\FileManager\Contracts\DirectoryInterface;
use KrisKuiper\FileManager\Contracts\FileInterface;

class Collection extends AbstractCollection
{
    public function current(): null|DirectoryInterface|FileInterface
    {
        return ($item = current($this->items)) ? $item : null;
    }

    public function append(DirectoryInterface|FileInterface $item): void
    {
        $this->items[] = $item;
    }
}