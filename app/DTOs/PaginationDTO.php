<?php

namespace App\DTOs;

class PaginationDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly int $current_page,
        public readonly int $prev_page,
        public readonly int $total_items,
        public readonly int $items_per_page,
        public readonly int $total_pages,
    ) {
        //
    }

    public static function base($model): self
    {
        return new self(
            $model->currentPage(),
            $model->currentPage() - 1,
            $model->total(),
            $model->perPage(),
            $model->lastPage(),
        );
    }
}
