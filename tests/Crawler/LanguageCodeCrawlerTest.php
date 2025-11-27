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

use BitAndblack\DocumentCrawler\Crawler\LanguageCodeCrawler;
use PHPUnit\Framework\TestCase;
use Places2Be\Locales\LanguageCode;
use Symfony\Component\DomCrawler\Crawler;

final class LanguageCodeCrawlerTest extends TestCase
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
            </body>
        </html>
        HTML;

        $crawler = new Crawler($html);

        $languageCodeCrawler = new LanguageCodeCrawler($crawler);
        $languageCodeCrawler->crawlContent();

        $languageCode = $languageCodeCrawler->getLanguageCode();

        self::assertInstanceOf(
            LanguageCode::class,
            $languageCode,
        );

        self::assertSame(
            'en',
            $languageCode->getLanguageCode()
        );
    }
}
