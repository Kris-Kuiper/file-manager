<?php

declare(strict_types=1);

namespace KrisKuiper\FileManager\Contracts;

interface OwnerInterface
{
    /**
     * Sets the id of the owner of a directory or file
     */
    public function setOwnerId(int $id): bool;

    /**
     * Returns the owner id of the current directory or file
     */
    public function getOwnerId(): bool|int;

    /**
     * Sets the id of the owner of a directory or file
     */
    public function setOwner(string $name): bool;

    /**
     * Returns the owner id of the current directory or file
     */
    public function getOwner(): bool|string;

    /**
     * Returns the group id of the current directory or file
     */
    public function getGroupId(): bool|int;

    /**
     * Sets the group id of a directory or file
     */
    public function setGroupId(int $id): bool;

    /**
     * Changes the current directory or file permissions level
     */
    public function chmod(int|string $mode): bool;

    /**
     * Changes the owner of the current directory or file
     */
    public function chown(int $userId): bool;
}