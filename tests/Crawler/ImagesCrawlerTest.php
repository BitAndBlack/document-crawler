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

use BitAndBlack\DocumentCrawler\Crawler\ImagesCrawler;
use BitAndBlack\DocumentCrawler\Tests\ResourceDownloader\TestResourceHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;

final class ImagesCrawlerTest extends TestCase
{
    public function testCrawlContent(): void
    {
        $html = <<<'HTML'
        <!doctype html>
        <html lang="en">
            <head>
                <meta charset="utf-8">
                <title>Test</title>
            </head>
            <body>
                <h1>Hello world</h1>
                <img src="/build/images/my-image-1.jpg" alt="Some alt text" title="Some title text">
                <picture>
                    <source srcset="/build/images/my-image-2.avif" type="image/avif">
                    <source srcset="/build/images/my-image-2.webp" type="image/webp">
                    <img src="/build/images/my-image-2.jpg" alt="Another alt text" title="Another title text">
                </picture>
                <img src="https://example.org/build/images/my-image-3.jpg" alt="Again alt text" title="Again title text">
            </body>
        </html>
        HTML;

        $crawler = new Crawler($html);

        $imagesCrawler = new ImagesCrawler($crawler);
        $imagesCrawler->setResourceHandler(new TestResourceHandler());
        $imagesCrawler->crawlContent();

        $images = $imagesCrawler->getImages();

        self::assertCount(
            3,
            $images
        );

        self::assertEquals(
            '__TEST__%2Fbuild%2Fimages%2Fmy-image-1.jpg',
            $images[0]->getResource()
        );

        self::assertEquals(
            'Some alt text',
            $images[0]->getAlt()
        );

        self::assertEquals(
            'Some title text',
            $images[0]->getTitle()
        );
    }
}
