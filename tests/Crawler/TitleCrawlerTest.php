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

namespace BitAndblack\DocumentCrawler\Tests\Crawler;

use BitAndblack\DocumentCrawler\Crawler\TitleCrawler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;

final class TitleCrawlerTest extends TestCase
{
    public function testCrawlContent(): void
    {
        $html = <<<'HTML'
        <!doctype html>
        <html lang="en">
            <head>
                <title>Test</title>
            </head>
            <body>
                <h1>Hello world</h1>
            </body>
        </html>
        HTML;

        $crawler = new Crawler($html);

        $titleCrawler = new TitleCrawler($crawler);
        $titleCrawler->crawlContent();

        $title = $titleCrawler->getTitle();

        self::assertSame(
            'Test',
            $title
        );
    }
}
