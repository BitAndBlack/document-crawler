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

use BitAndblack\DocumentCrawler\Crawler\IconsCrawler;
use BitAndblack\DocumentCrawler\Tests\ResourceDownloader\TestResourceHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;

final class IconsCrawlerTest extends TestCase
{
    public function testCrawlContent(): void
    {
        $html = <<<'HTML'
        <!doctype html>
        <html lang="en">
            <head>
                <link rel="alternate" href="https://www.tobiaskoengeter.de" hreflang="de">
                <link rel="apple-touch-icon" sizes="180x180" href="/build/images/apple-touch-icon.png">
                <link rel="icon" type="image/png" sizes="32x32" href="/build/images/favicon-32x32.png">
                <link rel="icon" type="image/png" sizes="16x16" href="/build/images/favicon-16x16.png">
                <link rel="manifest" href="/build/images/site.webmanifest">
                <link rel="mask-icon" href="/build/images/safari-pinned-tab.svg" color="#6cb377">
                <link rel="shortcut icon" href="/build/images/favicon.ico">
                <title>Test</title>
            </head>
            <body>
                <h1>Hello world</h1>
            </body>
        </html>
        HTML;

        $crawler = new Crawler($html);

        $iconsCrawler = new IconsCrawler($crawler);
        $iconsCrawler->setResourceHandler(new TestResourceHandler());
        $iconsCrawler->crawlContent();

        $icons = $iconsCrawler->getIcons();

        self::assertCount(
            5,
            $icons
        );

        self::assertEquals(
            'apple-touch-icon',
            $icons[0]->getName()
        );

        self::assertEquals(
            '__TEST__%2Fbuild%2Fimages%2Fapple-touch-icon.png',
            $icons[0]->getResource()
        );

        self::assertEquals(
            'icon',
            $icons[1]->getName()
        );

        self::assertEquals(
            '__TEST__%2Fbuild%2Fimages%2Ffavicon-32x32.png',
            $icons[1]->getResource()
        );

        self::assertEquals(
            'icon',
            $icons[2]->getName()
        );

        self::assertEquals(
            '__TEST__%2Fbuild%2Fimages%2Ffavicon-16x16.png',
            $icons[2]->getResource()
        );

        self::assertEquals(
            'mask-icon',
            $icons[3]->getName()
        );

        self::assertEquals(
            '__TEST__%2Fbuild%2Fimages%2Fsafari-pinned-tab.svg',
            $icons[3]->getResource()
        );

        self::assertEquals(
            'shortcut icon',
            $icons[4]->getName()
        );

        self::assertEquals(
            '__TEST__%2Fbuild%2Fimages%2Ffavicon.ico',
            $icons[4]->getResource()
        );
    }
}
