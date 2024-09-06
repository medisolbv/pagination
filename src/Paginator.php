<?php

declare(strict_types=1);

namespace Medisol\Pagination;

final class Paginator
{
    private const LAST_PAGE_FOR_FULL_RANGE = 10;
    private const PAGES_ON_EITHER_SIDE = 2;

    private int $lastLeftSidePageToShow = 2;
    private int $firstRightSidePageToShow;

    /** @var array<PaginationLink> */
    private array $links = [];

    private function __construct(
        private readonly int $currentPage,
        private readonly int $totalPages,
    ) {
        $this->firstRightSidePageToShow = $this->totalPages - 1;
    }

    public static function create(int $currentPage, int $totalPages): Paginator
    {
        return new self(
            currentPage: $currentPage,
            totalPages: $totalPages,
        );
    }

    public function generate(): self
    {
        if ($this->totalPages <= 1) {
            return $this;
        }

        if ($this->totalPages <= self::LAST_PAGE_FOR_FULL_RANGE) {
            return $this->generateForFullRange();
        } else {
            return $this->generateForExtendedRange();
        }
    }

    /**
     * @return array<PaginationLink>
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    private function addLinkForPage(?int $page): void
    {
        $this->links[] = new PaginationLink(
            page: $page,
            active: $page === $this->currentPage,
        );
    }

    /**
     * Disabled links are rendered as "..." and are used as unclickable separators in extended pagination
     */
    private function addDisabledLink(): void
    {
        $this->addLinkForPage(null);
    }

    /**
     * A full range paginator has links for all pages from 1 to self::totalPages,
     * as long as self::totalPages is less than self::LAST_PAGE_FOR_FULL_RANGE
     *
     * @return self
     */
    private function generateForFullRange(): self
    {
        foreach (range(1, $this->totalPages) as $page) {
            $this->addLinkForPage($page);
        }

        return $this;
    }

    /**
     * Extended range is any range beyond self::generateForFullRange
     */
    private function generateForExtendedRange(): self
    {
        $this->setLinkGenerationPageLimits();

        foreach (range(1, $this->totalPages) as $page) {
            if ($this->shouldAddLinkForPage($page)) {
                $this->addLinkForPage($page);

                continue;
            }

            if ($this->shouldAddDisabledLink($page)) {
                $this->addDisabledLink();
            }

            if ($this->shouldAddLinkBeforeCurrentPage($page) && $this->shouldAddLinkAfterCurrentPage($page)) {
                $this->addLinkForPage($page);
            }
        }

        return $this;
    }

    private function shouldAddLinkForPage(int $page): bool
    {
        return $page <= $this->lastLeftSidePageToShow || $page >= $this->firstRightSidePageToShow;
    }

    /**
     * Disabled links are added in between consecutive ranges of links, for example;
     * [1] [2] ... [4] [5] [6] [7] [9] ... [19] [20]
     */
    private function shouldAddDisabledLink(int $page): bool
    {
        // If we're not on the page after the last left page, or the first right page, don't add a disabled link
        if (!($page === $this->lastLeftSidePageToShow + 1 || $page === $this->firstRightSidePageToShow - 1)) {
            return false;
        }

        // Only add a disabled link if the last one is not a disabled link
        return !$this->lastLink()->isDisabled();
    }

    private function shouldAddLinkBeforeCurrentPage(int $page): bool
    {
        if ($this->currentPage - self::PAGES_ON_EITHER_SIDE > $page) {
            return false;
        }

        if ($this->currentPage - self::PAGES_ON_EITHER_SIDE < $this->lastLeftSidePageToShow) {
            return false;
        }

        return true;
    }

    public function shouldAddLinkAfterCurrentPage(int $page): bool
    {
        if ($this->currentPage + self::PAGES_ON_EITHER_SIDE < $page) {
            return false;
        }

        return true;
    }

    private function setLinkGenerationPageLimits(): void
    {
        if ($this->currentPage > 5 && $this->currentPage < $this->totalPages - 6) {
            $this->lastLeftSidePageToShow = 2;
            $this->firstRightSidePageToShow = $this->totalPages - 1;
        } elseif ($this->currentPage < $this->totalPages - 4 && $this->currentPage > 5) {
            $this->lastLeftSidePageToShow = 2;
            $this->firstRightSidePageToShow = $this->totalPages - 1;
        } elseif ($this->currentPage <= 7) {
            $this->lastLeftSidePageToShow = 7;
            $this->firstRightSidePageToShow = $this->totalPages - 1;
        } else {
            $this->lastLeftSidePageToShow = 2;
            $this->firstRightSidePageToShow = $this->totalPages - 6;
        }
    }

    private function lastLink(): PaginationLink
    {
        $lastLink = end($this->links);

        return $lastLink ?: PaginationLink::disabled();
    }
}
