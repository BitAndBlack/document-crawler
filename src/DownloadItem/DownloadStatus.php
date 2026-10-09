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

/**
 * A mutable status that a download can be brought to, by reference.
 *
 * It is shared between a {@see DownloadItem} and the client that performs the download,
 * so the item reflects the final status of the download as soon as it has finished.
 */
final class DownloadStatus
{
    /**
     * @param array<int, Throwable> $errors
     */
    public function __construct(
        private bool $hasSuccess,
        private array $errors,
    ) {
    }

    /**
     * @param array<int, Throwable> $errors
     */
    public function update(bool $hasSuccess, array $errors): void
    {
        $this->hasSuccess = $hasSuccess;
        $this->errors = $errors;
    }

    public function hasSuccess(): bool
    {
        return $this->hasSuccess;
    }

    /**
     * @return array<int, Throwable>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
