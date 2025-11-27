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

use BitAndBlack\Composer\Composer;
use BitAndblack\DocumentCrawler\Exception\MissingDependencyException;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use React\Http\Browser;
use Throwable;

readonly class ReactDownloader implements FileSystemDownloaderInterface
{
    private Browser $browser;

    /**
     * @param Browser|null $browser
     * @throws MissingDependencyException
     */
    public function __construct(Browser|null $browser = null)
    {
        if (null === $browser && false === Composer::classExists(Browser::class)) {
            throw new MissingDependencyException(
                static::class,
                Browser::class,
                'react/http'
            );
        }

        $this->browser = $browser ?? new Browser();
    }

    public function download(string $src, string $cacheFile): DownloadItem
    {
        $hasSuccess = true;

        /** @var array<int, Throwable> $errors */
        $errors = [];

        $onFulFilled = function (ResponseInterface $response) use (&$hasSuccess, $cacheFile) {
            if ($response->getStatusCode() > StatusCodeInterface::STATUS_BAD_REQUEST) {
                $hasSuccess = false;
                return;
            }

            $hasSuccess = false !== file_put_contents(
                $cacheFile,
                (string) $response->getBody()
            );
        };

        $onRejected = function (Throwable $error) use (&$hasSuccess, &$errors) {
            $errors[] = $error;
            $hasSuccess = false;
        };

        $this->browser
            ->get($src)
            ->then($onFulFilled, $onRejected)
        ;

        return new DownloadItem(
            $src,
            $cacheFile,
            $hasSuccess,
            $errors,
        );
    }
}
