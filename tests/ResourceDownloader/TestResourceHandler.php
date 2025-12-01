<?php

/**
 * Bit&Black Document Crawler.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

namespace BitAndBlack\DocumentCrawler\Tests\ResourceDownloader;

use BitAndBlack\DocumentCrawler\ResourceHandler\ResourceHandlerInterface;

class TestResourceHandler implements ResourceHandlerInterface
{
    public function handleResource(string $src, string $baseUri): string|false
    {
        return '__TEST__' . urlencode($src);
    }

    public function hasHandledAllResources(): bool
    {
        return true;
    }
}
