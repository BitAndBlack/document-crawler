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

use BitAndBlack\DocumentCrawler\Crawler\IframesCrawler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;

final class IframesCrawlerTest extends TestCase
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
                <iframe src="https://www.example.org/embedded-video" title="Some video" width="560" height="315"></iframe>
                <iframe src="https://www.example.org/other-embed"></iframe>
            </body>
        </html>
        HTML;

        $crawler = new Crawler($html);

        $iframesCrawler = new IframesCrawler($crawler);
        $iframesCrawler->crawlContent();

        $iframes = $iframesCrawler->getIframes();

        self::assertCount(
            2,
            $iframes
        );

        self::assertSame(
            'https://www.example.org/embedded-video',
            $iframes[0]->getSrc()
        );

        self::assertSame(
            'Some video',
            $iframes[0]->getTitle()
        );

        self::assertSame(
            '560',
            $iframes[0]->getWidth()
        );

        self::assertSame(
            '315',
            $iframes[0]->getHeight()
        );

        self::assertSame(
            'https://www.example.org/other-embed',
            $iframes[1]->getSrc()
        );

        self::assertNull(
            $iframes[1]->getTitle()
        );

        self::assertNull(
            $iframes[1]->getWidth()
        );

        self::assertNull(
            $iframes[1]->getHeight()
        );
    }

    public function testCrawlContentSkipsIframesWithoutSrc(): void
    {
        $html = <<<'HTML'
        <!doctype html>
        <html lang="en">
            <head>
                <title>Test</title>
            </head>
            <body>
                <h1>Hello world</h1>
                <iframe title="No source"></iframe>
                <iframe src=""></iframe>
                <iframe src="https://www.example.org/embedded-content"></iframe>
            </body>
        </html>
        HTML;

        $crawler = new Crawler($html);

        $iframesCrawler = new IframesCrawler($crawler);
        $iframesCrawler->crawlContent();

        $iframes = $iframesCrawler->getIframes();

        self::assertCount(
            1,
            $iframes
        );

        self::assertSame(
            'https://www.example.org/embedded-content',
            $iframes[0]->getSrc()
        );
    }

    public function testCrawlContentWithoutIframes(): void
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

        $iframesCrawler = new IframesCrawler($crawler);
        $iframesCrawler->crawlContent();

        self::assertCount(
            0,
            $iframesCrawler->getIframes()
        );
    }
}
