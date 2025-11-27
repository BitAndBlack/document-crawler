<?php

declare(strict_types=1);

/**
 * Bit&Black Document Crawler.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

namespace BitAndblack\DocumentCrawler\Tests;

use BitAndblack\DocumentCrawler\HolisticDocumentCrawler;
use PHPUnit\Framework\TestCase;

final class HolisticDocumentCrawlerTest extends TestCase
{
    public function test__construct(): void
    {
        $holisticPageContentCrawler = new HolisticDocumentCrawler('https://www.example.org');

        self::assertEmpty(
            $holisticPageContentCrawler->getErrors()
        );

        self::assertSame(
            'Example Domain',
            $holisticPageContentCrawler->getTitle()
        );
    }
}
