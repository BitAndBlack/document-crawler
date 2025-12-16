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

use BitAndBlack\DocumentCrawler\Util\Url;
use BitAndBlack\DocumentCrawler\Util\UrlParser;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class UrlParserTest extends TestCase
{
    public static function getCanHandleSpecialCharsData(): Generator
    {
        yield [
            'www.äöüß.de',
            UrlParser::parse('https://www.äöüß.de')->getHost(),
        ];

        yield [
            '/de-de/land/österreich.html',
            UrlParser::parse('https://www.calidar.io/de-de/land/%c3%b6sterreich.html')->getPath(),
        ];

        yield [
            '/en-gb/country/são+tomé+and+príncipe.html',
            UrlParser::parse('https://www.calidar.io/en-gb/country/s%c3%a3o+tom%c3%a9+and+pr%c3%adncipe.html')->getPath(),
        ];

        yield [
            '/en-gb/country/são+tomé+and+príncipe.html',
            UrlParser::parse('https://www.calidar.io/en-gb/country/são+tomé+and+príncipe.html')->getPath(),
        ];
    }

    #[DataProvider('getCanHandleSpecialCharsData')]
    public function testCanHandleSpecialChars(string $url, string $urlPartExpected): void
    {
        self::assertSame(
            $url,
            $urlPartExpected
        );
    }

    public static function getCanParseAndUnparseData(): Generator
    {
        yield [
            'https://www.bücher.de/wiröd.html?foo=baß',
            new Url(
                scheme: 'https',
                host: 'www.bücher.de',
                port: null,
                user: null,
                pass: null,
                query: 'foo=baß',
                path: '/wiröd.html',
                fragment: null,
            ),
        ];
    }

    #[DataProvider('getCanParseAndUnparseData')]
    public function testCanParseAndUnparse(string $url, Url $urlParsedExpected): void
    {
        $urlParsed = UrlParser::parse($url);

        self::assertEquals(
            $urlParsedExpected,
            $urlParsed
        );

        $unparsed = $urlParsed->getUrl();

        self::assertSame(
            $unparsed,
            $url
        );
    }
}
