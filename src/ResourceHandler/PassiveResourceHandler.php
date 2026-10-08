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
         * Protocol-relative URLs get the scheme of the base URL.
         */
        if (true === str_starts_with($src, '//') && null !== $baseUrl) {
            return (parse_url($baseUrl, PHP_URL_SCHEME) ?? 'https') . ':' . $src;
        }

        /**
         * Change relative urls to absolute ones.
         */
        if (false === str_starts_with($src, '//')
            && false === str_starts_with($src, 'http')
            && false === str_starts_with($src, 'data:')
        ) {
            if (null === $baseUrl) {
                return $src;
            }

            return $baseUrl . '/' . ltrim($src, '/');
        }

        return $src;
    }

    public function hasHandledAllResources(): bool
    {
        return true;
    }
}
