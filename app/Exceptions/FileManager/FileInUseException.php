<?php

namespace App\Exceptions\FileManager;

use RuntimeException;

class FileInUseException extends RuntimeException
{
    /**
     * @param array<string, mixed> $usageSummary
     */
    public function __construct(
        private readonly array $usageSummary,
    ) {
        parent::__construct('This item is used and cannot be deleted without force.');
    }

    /**
     * @return array<string, mixed>
     */
    public function usageSummary(): array
    {
        return $this->usageSummary;
    }
}
