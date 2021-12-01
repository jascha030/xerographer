<?php

namespace Jascha030\Xerox\Database;

interface DatabaseRemovalInterface
{
    /**
     * Method to drop mysql databases by database name.
     */
    public function dropDatabase(string $name): void;
}
