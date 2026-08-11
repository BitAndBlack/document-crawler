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

use BitAndBlack\DocumentCrawler\Crawler\LinkTagsCrawler;
use BitAndBlack\DocumentCrawler\Tests\ResourceDownloader\TestResourceHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;

final class LinkTagsCrawlerTest extends TestCase
{
    public function testCrawlContent(): void
    {
        $html = <<<'HTML'
        <!doctype html>
        <html lang="en">
            <head>
                <meta charset="utf-8">
                <title>Test</title>
                <link rel="stylesheet" href="print.css" media="print">
                <link rel="stylesheet" href="mobile.css" media="screen and (width <= 600px)">
                <link rel="preload" href="myFont.woff2" as="font" type="font/woff2" crossorigin="anonymous">
                <link rel="alternate" href="https://www.tobiaskoengeter.de" hreflang="de">
                <link rel="apple-touch-icon" sizes="180x180" href="/build/images/apple-touch-icon.png">
                <link rel="icon" type="image/png" sizes="32x32" href="/build/images/favicon-32x32.png">
            </head>
            <body>
                <h1>Hello world</h1>
            </body>
        </html>
        HTML;

        $crawler = new Crawler($html);

        $linkTagsCrawler = new LinkTagsCrawler($crawler);
        $linkTagsCrawler->setResourceHandler(new TestResourceHandler());
        $linkTagsCrawler->crawlContent();

        $links = $linkTagsCrawler->getLinks();

        self::assertCount(
            6,
            $links
        );

        self::assertSame(
            'stylesheet',
            $links[0]->getRel()
        );

        self::assertSame(
            '__TEST__print.css',
            $links[0]->getHref()
        );

        self::assertEquals(
            '__TEST__%2Fbuild%2Fimages%2Fapple-touch-icon.png',
            $links[4]->getHref()
        );
    }
}
