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

use BitAndBlack\DocumentCrawler\Crawler\CanonicalCrawler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;

final class CanonicalCrawlerTest extends TestCase
{
    public function testCrawlContent(): void
    {
        $html = <<<'HTML'
        <!doctype html>
        <html lang="en">
            <head>
                <title>Test</title>
                <link rel="canonical" href="https://www.example.org/our-page">
            </head>
            <body>
                <h1>Hello world</h1>
            </body>
        </html>
        HTML;

        $crawler = new Crawler($html);

        $canonicalCrawler = new CanonicalCrawler($crawler);
        $canonicalCrawler->crawlContent();

        self::assertSame(
            'https://www.example.org/our-page',
            $canonicalCrawler->getCanonicalUrl()
        );
    }

    public function testCrawlContentWithoutCanonicalUrl(): void
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

        $canonicalCrawler = new CanonicalCrawler($crawler);
        $canonicalCrawler->crawlContent();

        self::assertNull(
            $canonicalCrawler->getCanonicalUrl()
        );
    }

    public function testCrawlContentUsesFirstOfManyCanonicalUrls(): void
    {
        $html = <<<'HTML'
        <!doctype html>
        <html lang="en">
            <head>
                <title>Test</title>
                <link rel="canonical" href="https://www.example.org/first-page">
                <link rel="canonical" href="https://www.example.org/second-page">
            </head>
            <body>
                <h1>Hello world</h1>
            </body>
        </html>
        HTML;

        $crawler = new Crawler($html);

        $canonicalCrawler = new CanonicalCrawler($crawler);
        $canonicalCrawler->crawlContent();

        self::assertSame(
            'https://www.example.org/first-page',
            $canonicalCrawler->getCanonicalUrl()
        );
    }

    public function testCrawlContentIgnoresOtherRelValues(): void
    {
        $html = <<<'HTML'
        <!doctype html>
        <html lang="en">
            <head>
                <title>Test</title>
                <link rel="alternate" hreflang="de" href="https://www.example.org/de">
                <link rel="canonical alternate" href="https://www.example.org/combined">
            </head>
            <body>
                <h1>Hello world</h1>
            </body>
        </html>
        HTML;

        $crawler = new Crawler($html);

        $canonicalCrawler = new CanonicalCrawler($crawler);
        $canonicalCrawler->crawlContent();

        self::assertNull(
            $canonicalCrawler->getCanonicalUrl()
        );
    }
}
