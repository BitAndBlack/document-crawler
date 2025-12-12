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

use BitAndBlack\DocumentCrawler\Util\BaseUrl;

/**
 * The passive resource handler does nothing but to return the original source.
 */
class PassiveResourceHandler implements ResourceHandlerInterface
{
    public function handleResource(string $src, string|null $baseUrl): string|false
    {
        if (null !== $baseUrl) {
            $baseUrl = (string) new BaseUrl($baseUrl);
        }

        /**
         * Change relative urls to absolute ones.
         */
        if (false === str_starts_with($src, 'http') && false === str_starts_with($src, 'data:')) {
            $src = $baseUrl . '/' . ltrim($src, '/');
        }

        return $src;
    }

    public function hasHandledAllResources(): bool
    {
        return true;
    }
}
