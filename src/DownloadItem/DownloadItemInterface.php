<?php

/**
 * Bit&Black Document Crawler.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

namespace BitAndBlack\DocumentCrawler\DownloadItem;

use Throwable;

interface DownloadItemInterface
{
    /**
     * Tells the source of a resource, for example `/build/images/my-image-1.jpg`.
     *
     * @return string
     */
    public function getSrc(): string;

    /**
     * Tells the path of a downloaded resource, for example `/disk/my-image-1-downloaded.jpg`.
     *
     * @return string
     */
    public function getFileDownloaded(): string;

    /**
     * Returns errors, if existing.
     *
     * @return array<int, Throwable>
     */
    public function getErrors(): array;

    /**
     * Tells if the download of the resource was successful.
     *
     * @return bool
     */
    public function hasSuccess(): bool;
}
