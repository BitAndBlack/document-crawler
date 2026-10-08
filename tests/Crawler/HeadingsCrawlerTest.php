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

use BitAndBlack\DocumentCrawler\Crawler\HeadingsCrawler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;

final class HeadingsCrawlerTest extends TestCase
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
                <h2>Some subtitle</h2>
                <h3>Some sub-subtitle</h3>
                <h2>Another subtitle</h2>
            </body>
        </html>
        HTML;

        $crawler = new Crawler($html);

        $headingsCrawler = new HeadingsCrawler($crawler);
        $headingsCrawler->crawlContent();

        $headings = $headingsCrawler->getHeadings();

        self::assertCount(
            4,
            $headings
        );

        self::assertSame(
            1,
            $headings[0]->getLevel()
        );

        self::assertSame(
            'Hello world',
            $headings[0]->getText()
        );

        self::assertSame(
            3,
            $headings[2]->getLevel()
        );

        self::assertSame(
            'Some sub-subtitle',
            $headings[2]->getText()
        );
    }

    public function testCrawlContentWithMultipleH1AndMissingLevels(): void
    {
        $html = <<<'HTML'
        <!doctype html>
        <html lang="en">
            <head>
                <title>Test</title>
            </head>
            <body>
                <h1>First h1</h1>
                <h1>Second h1</h1>
                <h4>Level four</h4>
            </body>
        </html>
        HTML;

        $crawler = new Crawler($html);

        $headingsCrawler = new HeadingsCrawler($crawler);
        $headingsCrawler->crawlContent();

        $headings = $headingsCrawler->getHeadings();

        self::assertCount(
            3,
            $headings
        );

        self::assertSame(
            [
                1 => 2,
                4 => 1,
            ],
            $headingsCrawler->getHeadingCounts()
        );
    }

    public function testCrawlContentWithEmptyHeadingText(): void
    {
        $html = <<<'HTML'
        <!doctype html>
        <html lang="en">
            <head>
                <title>Test</title>
            </head>
            <body>
                <h1></h1>
                <h2>Some subtitle</h2>
            </body>
        </html>
        HTML;

        $crawler = new Crawler($html);

        $headingsCrawler = new HeadingsCrawler($crawler);
        $headingsCrawler->crawlContent();

        $headings = $headingsCrawler->getHeadings();

        self::assertCount(
            2,
            $headings
        );

        self::assertSame(
            '',
            $headings[0]->getText()
        );
    }

    public function testGetHeadingCountsOnEmptyDocument(): void
    {
        $html = <<<'HTML'
        <!doctype html>
        <html lang="en">
            <head>
                <title>Test</title>
            </head>
            <body>
                <p>No headings at all.</p>
            </body>
        </html>
        HTML;

        $crawler = new Crawler($html);

        $headingsCrawler = new HeadingsCrawler($crawler);
        $headingsCrawler->crawlContent();

        self::assertCount(
            0,
            $headingsCrawler->getHeadings()
        );

        self::assertSame(
            [],
            $headingsCrawler->getHeadingCounts()
        );
    }
}
