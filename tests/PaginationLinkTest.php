<?php

declare(strict_types=1);

namespace Medisol\Pagination\Tests;

use Medisol\Pagination\PaginationLink;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PaginationLinkTest extends TestCase
{
    #[Test]
    public function itReturnsADisabledLink(): void
    {
        $link = PaginationLink::disabled();

        $this->assertNull($link->getPage());
        $this->assertFalse($link->isActive());
    }

    #[Test]
    public function itCanBeSerialisedToJson(): void
    {
        $link = new PaginationLink(
            page: 2, active: false,
        );

        $this->assertEquals('{"page":2,"active":false,"disabled":false}', json_encode($link));
    }

    #[Test]
    public function itReturnsTheCurrentPage(): void
    {
        $link = new PaginationLink(
            page: 2
        );

        $this->assertEquals(2, $link->getPage());
    }

    #[Test]
    #[DataProvider('activeStates')]
    public function itCorrectlyDeterminesActiveState(bool $state): void
    {
        $link = new PaginationLink(
            page: 1, active: $state,
        );

        $this->assertEquals($state, $link->isActive());
    }

    #[Test]
    #[DataProvider('disabledState')]
    public function itCorrectlyDeterminesDisabledState(?int $page, bool $expectedResult): void
    {
        $link = new PaginationLink(
            page: $page
        );

        $this->assertEquals($expectedResult, $link->isDisabled());
    }

    public static function activeStates(): array
    {
        return [
            [true],
            [false],
        ];
    }

    public static function disabledState(): array
    {
        return [
            [1, false],
            [null, true],
        ];
    }
}
