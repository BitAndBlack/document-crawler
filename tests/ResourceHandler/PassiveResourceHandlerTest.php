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

namespace BitAndBlack\DocumentCrawler\Tests\ResourceHandler;

use BitAndBlack\DocumentCrawler\ResourceHandler\PassiveResourceHandler;
use PHPUnit\Framework\TestCase;

final class PassiveResourceHandlerTest extends TestCase
{
    public function testKeepsAbsoluteUrls(): void
    {
        $passiveResourceHandler = new PassiveResourceHandler();

        self::assertSame(
            'https://cdn.example.org/pic.png',
            $passiveResourceHandler->handleResource('https://cdn.example.org/pic.png', 'https://www.example.org/page.html')
        );
    }

    public function testKeepsDataUrls(): void
    {
        $passiveResourceHandler = new PassiveResourceHandler();

        self::assertSame(
            'data:image/png;base64,xyz',
            $passiveResourceHandler->handleResource('data:image/png;base64,xyz', 'https://www.example.org/page.html')
        );
    }

    public function testResolvesProtocolRelativeUrls(): void
    {
        $passiveResourceHandler = new PassiveResourceHandler();

        self::assertSame(
            'https://cdn.example.org/pic.png',
            $passiveResourceHandler->handleResource('//cdn.example.org/pic.png', 'https://www.example.org/page.html')
        );
    }

    public function testKeepsProtocolRelativeUrlsWithoutBaseUrl(): void
    {
        $passiveResourceHandler = new PassiveResourceHandler();

        self::assertSame(
            '//cdn.example.org/pic.png',
            $passiveResourceHandler->handleResource('//cdn.example.org/pic.png', null)
        );
    }

    public function testResolvesRelativeUrls(): void
    {
        $passiveResourceHandler = new PassiveResourceHandler();

        self::assertSame(
            'https://www.example.org/build/images/pic.png',
            $passiveResourceHandler->handleResource('/build/images/pic.png', 'https://www.example.org/page.html')
        );
    }

    public function testKeepsRelativeUrlsWithoutBaseUrl(): void
    {
        $passiveResourceHandler = new PassiveResourceHandler();

        self::assertSame(
            'build/images/pic.png',
            $passiveResourceHandler->handleResource('build/images/pic.png', null)
        );
    }
}
