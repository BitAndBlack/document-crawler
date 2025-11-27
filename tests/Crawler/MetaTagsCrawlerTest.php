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

use BitAndblack\DocumentCrawler\Crawler\MetaTagsCrawler;
use BitAndblack\DocumentCrawler\Tests\ResourceDownloader\TestResourceHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;

final class MetaTagsCrawlerTest extends TestCase
{
    public function testCrawlContent(): void
    {
        $html = <<<'HTML'
        <!doctype html>
        <html lang="en">
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
                <meta name="title" content="My document">
                <meta property="og:image" content="https://example.com/rock1.jpg" />
                <meta property="og:image" content="https://example.com/rock2.jpg" />
                <title>Test</title>
            </head>
            <body>
                <h1>Hello world</h1>
            </body>
        </html>
        HTML;

        $crawler = new Crawler($html);

        $metaTagsCrawler = new MetaTagsCrawler($crawler);
        $metaTagsCrawler->setResourceHandler(new TestResourceHandler());
        $metaTagsCrawler->crawlContent();

        $metaTags = $metaTagsCrawler->getMetaTags();

        self::assertCount(
            4,
            $metaTags
        );

        self::assertCount(
            2,
            $metaTags['og:image']
        );

        self::assertSame(
            'charset',
            $metaTags['charset'][0]->getName()
        );

        self::assertSame(
            'utf-8',
            $metaTags['charset'][0]->getResource()
        );

        self::assertSame(
            'viewport',
            $metaTags['viewport'][0]->getName()
        );

        self::assertSame(
            'width=device-width, initial-scale=1, shrink-to-fit=no',
            $metaTags['viewport'][0]->getResource()
        );

        self::assertSame(
            'title',
            $metaTags['title'][0]->getName()
        );

        self::assertSame(
            'My document',
            $metaTags['title'][0]->getResource()
        );

        self::assertSame(
            'og:image',
            $metaTags['og:image'][0]->getName()
        );

        self::assertSame(
            '__TEST__https%3A%2F%2Fexample.com%2Frock1.jpg',
            $metaTags['og:image'][0]->getResource()
        );

        self::assertSame(
            'og:image',
            $metaTags['og:image'][1]->getName()
        );

        self::assertSame(
            '__TEST__https%3A%2F%2Fexample.com%2Frock2.jpg',
            $metaTags['og:image'][1]->getResource()
        );
    }
}
