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

readonly class DownloadItem implements DownloadItemInterface
{
    /**
     * @param string $src
     * @param string $fileDownloaded
     * @param bool $hasSuccess
     * @param array<int, Throwable> $errors
     */
    public function __construct(
        private string $src,
        private string $fileDownloaded,
        private bool $hasSuccess,
        private array $errors,
    ) {
    }

    public function getSrc(): string
    {
        return $this->src;
    }

    public function getFileDownloaded(): string
    {
        return $this->fileDownloaded;
    }

    /**
     * @return array<int, Throwable>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasSuccess(): bool
    {
        return $this->hasSuccess;
    }
}
