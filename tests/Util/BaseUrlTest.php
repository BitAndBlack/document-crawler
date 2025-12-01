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

namespace BitAndBlack\DocumentCrawler\Tests\Util;

use BitAndBlack\DocumentCrawler\Util\BaseUrl;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class BaseUrlTest extends TestCase
{
    public static function getGetBaseUrlData(): Generator
    {
        yield [
            'https://www.bitandblack.com/en/imprint.html',
            'https://www.bitandblack.com',
        ];

        yield [
            'https://www.bitandblack.com///',
            'https://www.bitandblack.com',
        ];

        yield [
            'bitandblack.com',
            'https://bitandblack.com',
        ];
    }

    #[DataProvider('getGetBaseUrlData')]
    public function testGetBaseUrl(string $url, string $urlExpected): void
    {
        self::assertSame(
            $urlExpected,
            (string) new BaseUrl($url)
        );
    }
}
