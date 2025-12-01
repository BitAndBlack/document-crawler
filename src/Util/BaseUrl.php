<?php

/**
 * Bit&Black Document Crawler.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

namespace BitAndBlack\DocumentCrawler\Util;

use Stringable;

readonly class BaseUrl implements Stringable
{
    private string $baseUrl;

    public function __construct(string $url)
    {
        $scheme = parse_url($url, PHP_URL_SCHEME) ?? 'https';
        $host = parse_url($url, PHP_URL_HOST) ?? $url;

        $this->baseUrl = $scheme . '://' . $host;
    }

    public function __toString(): string
    {
        return $this->getBaseUrl();
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }
}
