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

namespace BitAndBlack\DocumentCrawler\Tests\Crawler;

use BitAndBlack\DocumentCrawler\Crawler\StructuredDataCrawler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;

final class StructuredDataCrawlerTest extends TestCase
{
    public function testCrawlContent(): void
    {
        $html = <<<'HTML'
        <!doctype html>
        <html lang="en">
            <head>
                <title>Test</title>
                <script type="application/ld+json">
                {
                    "@context": "https://schema.org",
                    "@type": "Organization",
                    "name": "Example"
                }
                </script>
            </head>
            <body>
                <h1>Hello world</h1>
            </body>
        </html>
        HTML;

        $crawler = new Crawler($html);

        $structuredDataCrawler = new StructuredDataCrawler($crawler);
        $structuredDataCrawler->crawlContent();

        $structuredData = $structuredDataCrawler->getStructuredData();

        self::assertCount(
            1,
            $structuredData
        );

        self::assertTrue(
            $structuredData[0]->isValidJson()
        );

        self::assertSame(
            ['Organization'],
            $structuredData[0]->getTypes()
        );

        self::assertSame(
            'Example',
            $structuredData[0]->getDecoded()['name']
        );

        self::assertStringContainsString(
            '"@type": "Organization"',
            $structuredData[0]->getJson()
        );
    }

    public function testCrawlContentWithMultipleBlocks(): void
    {
        $html = <<<'HTML'
        <!doctype html>
        <html lang="en">
            <head>
                <title>Test</title>
                <script type="application/ld+json">{"@type": "Organization", "name": "Example"}</script>
                <script type="application/ld+json">{"@type": "WebSite", "name": "Example"}</script>
            </head>
            <body>
                <h1>Hello world</h1>
            </body>
        </html>
        HTML;

        $crawler = new Crawler($html);

        $structuredDataCrawler = new StructuredDataCrawler($crawler);
        $structuredDataCrawler->crawlContent();

        $structuredData = $structuredDataCrawler->getStructuredData();

        self::assertCount(
            2,
            $structuredData
        );

        self::assertSame(
            ['Organization'],
            $structuredData[0]->getTypes()
        );

        self::assertSame(
            ['WebSite'],
            $structuredData[1]->getTypes()
        );
    }

    public function testCrawlContentWithInvalidJson(): void
    {
        $html = <<<'HTML'
        <!doctype html>
        <html lang="en">
            <head>
                <title>Test</title>
                <script type="application/ld+json">{"@type": "Organization",</script>
            </head>
            <body>
                <h1>Hello world</h1>
            </body>
        </html>
        HTML;

        $crawler = new Crawler($html);

        $structuredDataCrawler = new StructuredDataCrawler($crawler);
        $structuredDataCrawler->crawlContent();

        $structuredData = $structuredDataCrawler->getStructuredData();

        self::assertCount(
            1,
            $structuredData
        );

        self::assertFalse(
            $structuredData[0]->isValidJson()
        );

        self::assertSame(
            [],
            $structuredData[0]->getDecoded()
        );

        self::assertSame(
            [],
            $structuredData[0]->getTypes()
        );

        self::assertSame(
            '{"@type": "Organization",',
            $structuredData[0]->getJson()
        );
    }

    public function testCrawlContentWithTypesAsList(): void
    {
        $html = <<<'HTML'
        <!doctype html>
        <html lang="en">
            <head>
                <title>Test</title>
                <script type="application/ld+json">{"@type": ["Article", "BlogPosting"]}</script>
            </head>
            <body>
                <h1>Hello world</h1>
            </body>
        </html>
        HTML;

        $crawler = new Crawler($html);

        $structuredDataCrawler = new StructuredDataCrawler($crawler);
        $structuredDataCrawler->crawlContent();

        $structuredData = $structuredDataCrawler->getStructuredData();

        self::assertSame(
            ['Article', 'BlogPosting'],
            $structuredData[0]->getTypes()
        );
    }

    public function testCrawlContentWithGraph(): void
    {
        $html = <<<'HTML'
        <!doctype html>
        <html lang="en">
            <head>
                <title>Test</title>
                <script type="application/ld+json">
                {
                    "@context": "https://schema.org",
                    "@graph": [
                        {"@type": "Organization", "name": "Example"},
                        {"@type": ["WebSite", "Organization"], "name": "Example"}
                    ]
                }
                </script>
            </head>
            <body>
                <h1>Hello world</h1>
            </body>
        </html>
        HTML;

        $crawler = new Crawler($html);

        $structuredDataCrawler = new StructuredDataCrawler($crawler);
        $structuredDataCrawler->crawlContent();

        $structuredData = $structuredDataCrawler->getStructuredData();

        self::assertSame(
            ['Organization', 'WebSite'],
            $structuredData[0]->getTypes()
        );
    }

    public function testCrawlContentWithItemListElement(): void
    {
        $html = <<<'HTML'
        <!doctype html>
        <html lang="en">
            <head>
                <title>Test</title>
                <script type="application/ld+json">
                {
                    "@type": "ItemList",
                    "itemListElement": [
                        {"@type": "ListItem", "position": 1},
                        {"@type": "ListItem", "position": 2}
                    ]
                }
                </script>
            </head>
            <body>
                <h1>Hello world</h1>
            </body>
        </html>
        HTML;

        $crawler = new Crawler($html);

        $structuredDataCrawler = new StructuredDataCrawler($crawler);
        $structuredDataCrawler->crawlContent();

        $structuredData = $structuredDataCrawler->getStructuredData();

        self::assertSame(
            ['ItemList', 'ListItem'],
            $structuredData[0]->getTypes()
        );
    }
}
