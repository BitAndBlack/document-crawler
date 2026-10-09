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

namespace BitAndBlack\DocumentCrawler\DownloadItem;

use Throwable;

readonly class DownloadItem implements DownloadItemInterface
{
    /**
     * @param array<int, Throwable> $errors
     */
    public function __construct(
        private string $src,
        private string $fileDownloaded,
        private bool $hasSuccess = false,
        private array $errors = [],
        private DownloadStatus|null $status = null,
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
        return $this->status?->getErrors() ?? $this->errors;
    }

    public function hasSuccess(): bool
    {
        return $this->status?->hasSuccess() ?? $this->hasSuccess;
    }
}
