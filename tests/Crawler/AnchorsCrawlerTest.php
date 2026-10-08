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

use BitAndBlack\DocumentCrawler\Crawler\AnchorsCrawler;
use BitAndBlack\DocumentCrawler\DTO\Anchor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;

final class AnchorsCrawlerTest extends TestCase
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
                <a href="https://www.example.org/first-page" title="Some title">Some text</a>
                <a href="https://www.example.org/second-page">Some other text</a>
                <a href="https://www.example.org/third-page"></a>
            </body>
        </html>
        HTML;

        $crawler = new Crawler($html);

        $anchorsCrawler = new AnchorsCrawler($crawler);
        $anchorsCrawler->crawlContent();

        $anchors = $anchorsCrawler->getAnchors();

        self::assertCount(
            3,
            $anchors
        );

        self::assertSame(
            'https://www.example.org/first-page',
            $anchors[0]->getHref()
        );

        self::assertSame(
            'Some text',
            $anchors[0]->getText()
        );

        self::assertSame(
            'Some title',
            $anchors[0]->getTitle()
        );

        self::assertNull(
            $anchors[2]->getText()
        );
    }

    public function testCrawlContentExtractsRel(): void
    {
        $html = <<<'HTML'
        <!doctype html>
        <html lang="en">
            <head>
                <title>Test</title>
            </head>
            <body>
                <h1>Hello world</h1>
                <a href="https://www.example.org/external-page" rel="nofollow">External page</a>
                <a href="https://www.example.org/other-page" rel="ugc sponsored">Other page</a>
                <a href="https://www.example.org/internal-page">Internal page</a>
                <a href="https://www.example.org/empty-rel" rel="">Empty rel</a>
            </body>
        </html>
        HTML;

        $crawler = new Crawler($html);

        $anchorsCrawler = new AnchorsCrawler($crawler);
        $anchorsCrawler->crawlContent();

        $anchors = $anchorsCrawler->getAnchors();

        self::assertCount(
            4,
            $anchors
        );

        self::assertSame(
            'nofollow',
            $anchors[0]->getRel()
        );

        self::assertSame(
            'ugc sponsored',
            $anchors[1]->getRel()
        );

        self::assertNull(
            $anchors[2]->getRel()
        );

        self::assertNull(
            $anchors[3]->getRel()
        );
    }

    public function testAnchorKeepsBackwardsCompatibleConstruction(): void
    {
        $anchor = new Anchor(
            'https://www.example.org/some-page',
            'Some text',
            'Some title',
        );

        self::assertSame(
            'https://www.example.org/some-page',
            $anchor->getHref()
        );

        self::assertSame(
            'Some text',
            $anchor->getText()
        );

        self::assertSame(
            'Some title',
            $anchor->getTitle()
        );

        self::assertNull(
            $anchor->getRel()
        );
    }
}
