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

use BitAndblack\DocumentCrawler\Exception;
use BitAndblack\DocumentCrawler\HolisticDocumentCrawler;
use PHPUnit\Framework\TestCase;

final class HolisticDocumentCrawlerTest extends TestCase
{
    public function testInitialiseWithDocument(): void
    {
        $document = <<<'HTML'
        <!doctype html>
        <html lang="en">
            <head>
                <title>Example Domain</title>
            </head>
            <body>
                <h1>Hello world</h1>
            </body>
        </html>
        HTML;

        $holisticPageContentCrawler = new HolisticDocumentCrawler($document);

        self::assertSame(
            'Example Domain',
            $holisticPageContentCrawler->getTitle()
        );
    }

    /**
     * @throws Exception
     */
    public function testInitialiseWithUrl(): void
    {
        $holisticPageContentCrawler = HolisticDocumentCrawler::createFromUrl('https://www.example.org');

        self::assertSame(
            'Example Domain',
            $holisticPageContentCrawler->getTitle()
        );
    }
}
