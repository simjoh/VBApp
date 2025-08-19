<?php

namespace App\common\Repository;

/**
 * Pagination result container
 */
class PaginationResult
{
    public function __construct(
        public readonly array $data,
        public readonly int $total,
        public readonly int $page,
        public readonly int $perPage,
        public readonly int $totalPages,
        public readonly bool $hasNextPage,
        public readonly bool $hasPreviousPage,
        public readonly ?string $nextCursor = null,
        public readonly ?string $previousCursor = null
    ) {}

    public function toArray(): array
    {
        return [
            'data' => $this->data,
            'pagination' => [
                'total' => $this->total,
                'page' => $this->page,
                'per_page' => $this->perPage,
                'total_pages' => $this->totalPages,
                'has_next_page' => $this->hasNextPage,
                'has_previous_page' => $this->hasPreviousPage,
                'next_cursor' => $this->nextCursor,
                'previous_cursor' => $this->previousCursor
            ]
        ];
    }
} 