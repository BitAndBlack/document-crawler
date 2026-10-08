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
use BitAndBlack\DocumentCrawler\DTO\Image;
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

    public function testCrawlContentExtractsRicherImageAttributes(): void
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
                <img src="/build/images/my-image-1.jpg" alt="Some alt text" loading="lazy" width="1280" height="720" srcset="/build/images/my-image-1.jpg 1x, /build/images/my-image-1@2x.jpg 2x">
                <img src="/build/images/my-image-2.jpg" alt="Another alt text" loading="" width="" height="" srcset="">
            </body>
        </html>
        HTML;

        $crawler = new Crawler($html);

        $imagesCrawler = new ImagesCrawler($crawler);
        $imagesCrawler->setResourceHandler(new TestResourceHandler());
        $imagesCrawler->crawlContent();

        $images = $imagesCrawler->getImages();

        self::assertCount(
            2,
            $images
        );

        self::assertSame(
            'lazy',
            $images[0]->getLoading()
        );

        self::assertSame(
            '1280',
            $images[0]->getWidth()
        );

        self::assertSame(
            '720',
            $images[0]->getHeight()
        );

        self::assertSame(
            '/build/images/my-image-1.jpg 1x, /build/images/my-image-1@2x.jpg 2x',
            $images[0]->getSrcset()
        );

        self::assertNull(
            $images[1]->getLoading()
        );

        self::assertNull(
            $images[1]->getWidth()
        );

        self::assertNull(
            $images[1]->getHeight()
        );

        self::assertNull(
            $images[1]->getSrcset()
        );
    }

    public function testImageKeepsBackwardsCompatibleConstruction(): void
    {
        $image = new Image(
            '/build/images/my-image-1.jpg',
            'Some alt text',
            'Some title text',
        );

        self::assertSame(
            '/build/images/my-image-1.jpg',
            $image->getResource()
        );

        self::assertSame(
            'Some alt text',
            $image->getAlt()
        );

        self::assertSame(
            'Some title text',
            $image->getTitle()
        );

        self::assertNull(
            $image->getLoading()
        );

        self::assertNull(
            $image->getWidth()
        );

        self::assertNull(
            $image->getHeight()
        );

        self::assertNull(
            $image->getSrcset()
        );
    }
}
