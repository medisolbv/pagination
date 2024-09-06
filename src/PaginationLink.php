<?php

declare(strict_types=1);

namespace Medisol\Pagination;

use JsonSerializable;

final readonly class PaginationLink implements JsonSerializable
{
    public function __construct(
        private ?int $page = null,
        private bool $active = false,
    ) {
    }

    public static function disabled(): self
    {
        return new self();
    }

    /**
     * @return array{page: int|null, active: bool}
     */
    public function jsonSerialize(): array
    {
        return [
            'page' => $this->page,
            'active' => $this->active,
            'disabled' => $this->isDisabled(),
        ];
    }

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function isDisabled(): bool
    {
        return $this->page === null;
    }
}
