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

interface ResourceHandlerInterface
{
    /**
     * Handles an external resource.
     *
     * @param string $src Absolute or relative path to a resource, for example `/build/images/my-file-1.jpg`.
     * @param string|null $baseUrl The base uri to use along with the resource, for example `https://www.example.org`.
     *                        The base uri helps to verify, if a resource belongs to the same domain or not.
     * @return string|false The name of the handled resource (can be modified), or `false` on failure.
     */
    public function handleResource(string $src, string|null $baseUrl): string|false;

    /**
     * Tells if all given resources have been handled.
     *
     * @return bool
     */
    public function hasHandledAllResources(): bool;
}
