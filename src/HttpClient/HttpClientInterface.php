<?php

/**
 * Bit&Black Document Crawler.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

namespace BitAndBlack\DocumentCrawler\HttpClient;

use BitAndBlack\DocumentCrawler\DownloadItem\DownloadItem;
use BitAndBlack\DocumentCrawler\Exception;
use Psr\Http\Message\ResponseInterface;

interface HttpClientInterface
{
    /**
     * Requests a URL in a blocking, non-asynchronously way and returns the response.
     *
     * @throws Exception
     */
    public function requestUrl(string $url): ResponseInterface;

    /**
     * Loads an external resource and stores it somewhere in the file system.
     * This may happen asynchronously.
     *
     * @param string $src The source of a resource, for example `/build/images/my-image-1.jpg`.
     * @param string $cacheFile The path of a downloaded resource, for example `/disk/my-image-1-downloaded.jpg`.
     * @return DownloadItem
     */
    public function download(string $src, string $cacheFile): DownloadItem;
}
