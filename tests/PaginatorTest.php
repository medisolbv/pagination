<?php

declare(strict_types=1);

namespace Medisol\Pagination\Tests;

use Medisol\Pagination\Paginator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PaginatorTest extends TestCase
{
    #[Test]
    public function itRendersNoLinksWhenThereAreFewerThanTwoPages(): void
    {
        $links = Paginator::create(
            currentPage: 1,
            totalPages: 1
        )
            ->generate()
            ->getLinks();

        $this->assertEmpty($links);

        $links = Paginator::create(
            currentPage: 1,
            totalPages: 0,
        )
            ->generate()
            ->getLinks();

        $this->assertEmpty($links);
    }

    #[Test]
    public function itGeneratesLinksForAllPagesUnderTenTotalPages(): void
    {
        $links = Paginator::create(
            currentPage: 1,
            totalPages: 10
        )
            ->generate()
            ->getLinks();

        $this->assertCount(10, $links);

        foreach ($links as $key => $link) {
            $this->assertEquals($key + 1, $link->getPage());
        }
    }

    #[Test]
    public function itGeneratesTheCorrectLinksForMoreThanTenTotalPages(): void
    {
        $links = Paginator::create(
            currentPage: 1,
            totalPages: 11,
        )
            ->generate()
            ->getLinks();

        $this->assertCount(10, $links);

        $expectedPages = [...range(1, 7), null, 10, 11];

        foreach ($expectedPages as $key => $page) {
            $this->assertEquals($links[$key]->getPage(), $page);
        }
    }

    #[Test]
    public function itGeneratesTheCorrectLinksAfterTheFifthPage(): void
    {
        $links = Paginator::create(
            currentPage: 6,
            totalPages: 20
        )
            ->generate()
            ->getLinks();

        $this->assertCount(11, $links);

        $expectedPages = [1, 2, null, ...range(4, 8), null, 19, 20];

        foreach ($expectedPages as $key => $page) {
            $this->assertEquals($links[$key]->getPage(), $page);
        }
    }

    #[Test]
    public function itGeneratesTheCorrectLinksBeforeTheFifthLastPage(): void
    {
        $links = Paginator::create(
            currentPage: 15,
            totalPages: 20
        )
            ->generate()
            ->getLinks();

        $this->assertCount(11, $links);

        $expectedPages = [1, 2, null, ...range(13, 17), null, 19, 20];

        foreach ($expectedPages as $key => $page) {
            $this->assertEquals($links[$key]->getPage(), $page);
        }
    }

    #[Test]
    public function itGeneratesTheCorrectLinksWhenOnTheFirstPage(): void
    {
        $links = Paginator::create(
            currentPage: 1,
            totalPages: 20
        )
            ->generate()
            ->getLinks();

        $expectedPages = [...range(1, 7), null, 19, 20];

        $this->assertCount(10, $links);

        foreach ($expectedPages as $key => $page) {
            $this->assertEquals($links[$key]->getPage(), $page);
        }
    }

    #[Test]
    public function itGeneratesTheCorrectLinksWhenOnTheLastPage(): void
    {
        $links = Paginator::create(
            currentPage: 20,
            totalPages: 20
        )
            ->generate()
            ->getLinks();

        $expectedPages = [1, 2, null, ...range(14, 20)];

        $this->assertCount(10, $links);

        foreach ($expectedPages as $key => $page) {
            $this->assertEquals($links[$key]->getPage(), $page);
        }
    }

    #[Test]
    public function itGeneratesTheCorrectLinksWhenTheCurrentPageIsInTheMiddle(): void
    {
        $links = Paginator::create(
            currentPage: 25,
            totalPages: 50
        )
            ->generate()
            ->getLinks();

        $this->assertCount(11, $links);

        $expectedPages = [1, 2, null, ...range(23, 27), null, 49, 50];

        foreach ($expectedPages as $key => $page) {
            $this->assertEquals($links[$key]->getPage(), $page);
        }
    }

    #[Test]
    public function itMarksTheCurrentPageAsActive(): void
    {
        $links = Paginator::create(
            currentPage: 3,
            totalPages: 5
        )
            ->generate()
            ->getLinks();

        $this->assertCount(5, $links);

        $pages = array_map(function (int $page) {
            return [
                'page' => $page,
                'active' => false,
            ];
        }, range(1, 5));

        $pages[2]['active'] = true;

        foreach ($pages as $key => $page) {
            $this->assertEquals($page['active'], $links[$key]->isActive());
        }
    }
}
