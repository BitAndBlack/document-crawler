<?php

/**
 * Bit&Black Document Crawler.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

namespace BitAndblack\DocumentCrawler\FileSystemDownloader;

interface FileSystemDownloaderInterface
{
    /**
     * Loads an external resource and stores it somewhere in the file system.
     *
     * @param string $src The source of a resource, for example `/build/images/my-image-1.jpg`.
     * @param string $cacheFile The path of a downloaded resource, for example `/disk/my-image-1-downloaded.jpg`.
     * @return DownloadItem
     */
    public function download(string $src, string $cacheFile): DownloadItem;
}
