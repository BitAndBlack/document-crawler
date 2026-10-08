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

namespace BitAndBlack\DocumentCrawler\Tests;

use BitAndBlack\DocumentCrawler\Exception;
use BitAndBlack\DocumentCrawler\HolisticDocumentCrawler;
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

    public function testCrawlsNewSectionsOfDocument(): void
    {
        $document = <<<'HTML'
        <!doctype html>
        <html lang="en">
            <head>
                <title>Example Domain</title>
                <link rel="canonical" href="https://www.example.org/">
                <script type="application/ld+json">{"@type": "Organization", "name": "Example"}</script>
            </head>
            <body>
                <h1>Hello world</h1>
                <h2>Some subtitle</h2>
                <iframe src="https://www.example.org/embedded-content" title="Some embed"></iframe>
            </body>
        </html>
        HTML;

        $holisticPageContentCrawler = new HolisticDocumentCrawler($document);

        self::assertSame(
            'https://www.example.org/',
            $holisticPageContentCrawler->getCanonicalUrl()
        );

        $structuredData = $holisticPageContentCrawler->getStructuredData();

        self::assertCount(
            1,
            $structuredData
        );

        self::assertSame(
            ['Organization'],
            $structuredData[0]->getTypes()
        );

        $headings = $holisticPageContentCrawler->getHeadings();

        self::assertCount(
            2,
            $headings
        );

        self::assertSame(
            'Hello world',
            $headings[0]->getText()
        );

        $iframes = $holisticPageContentCrawler->getIframes();

        self::assertCount(
            1,
            $iframes
        );

        self::assertSame(
            'https://www.example.org/embedded-content',
            $iframes[0]->getSrc()
        );
    }

    public function testCrawlsNewSectionsOfDocumentWithoutContent(): void
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

        self::assertNull(
            $holisticPageContentCrawler->getCanonicalUrl()
        );

        self::assertCount(
            0,
            $holisticPageContentCrawler->getStructuredData()
        );

        self::assertCount(
            1,
            $holisticPageContentCrawler->getHeadings()
        );

        self::assertCount(
            0,
            $holisticPageContentCrawler->getIframes()
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
