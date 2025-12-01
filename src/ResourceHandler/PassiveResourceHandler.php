<?php

/**
 * Bit&Black Document Crawler.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

namespace BitAndBlack\DocumentCrawler\ResourceHandler;

/**
 * The passive resource handler does nothing but to return the original source.
 */
class PassiveResourceHandler implements ResourceHandlerInterface
{
    public function handleResource(string $src, string $baseUri): string|false
    {
        return $src;
    }

    public function hasHandledAllResources(): bool
    {
        return true;
    }
}
