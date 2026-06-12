<?php

namespace App\Exceptions\FileManager;

use RuntimeException;

class DuplicateFileException extends RuntimeException
{
    /**
     * @param array<string, mixed> $conflict
     */
    public function __construct(
        private readonly array $conflict,
        string $message = 'A file with this name already exists.',
    ) {
        parent::__construct($message);
    }

    /**
     * @return array<string, mixed>
     */
    public function conflict(): array
    {
        return $this->conflict;
    }
}
