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

namespace BitAndBlack\DocumentCrawler\HttpClient;

use BitAndBlack\Composer\Composer;
use BitAndBlack\DocumentCrawler\DownloadItem\DownloadItem;
use BitAndBlack\DocumentCrawler\DownloadItem\DownloadStatus;
use BitAndBlack\DocumentCrawler\Exception;
use BitAndBlack\DocumentCrawler\Exception\MissingDependencyException;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use React\Http\Browser;
use Throwable;
use function React\Async\await;

readonly class ReactClient implements HttpClientInterface
{
    private Browser $browser;

    /**
     * @throws MissingDependencyException
     */
    public function __construct(Browser|null $browser = null)
    {
        if (null === $browser && false === Composer::classExists(Browser::class)) {
            throw new MissingDependencyException(
                static::class,
                'react/http',
            );
        }

        if (false === function_exists('React\Async\await')) {
            throw new MissingDependencyException(
                static::class,
                'react/async',
            );
        }

        $this->browser = $browser ?? new Browser();
    }

    /**
     * Requests a URL in a blocking, non-asynchronously way and returns the response.
     *
     * @throws Exception
     */
    public function requestUrl(string $url): ResponseInterface
    {
        $promise = $this->browser->get($url);

        try {
            $response = await($promise);
        } catch (Throwable $throwable) {
            throw new Exception('Failed to request URL.', $throwable);
        }

        return $response;
    }

    /**
     * Loads an external resource and stores it somewhere in the file system.
     * The download runs in the background: the returned download item
     * reflects the final status of the download, once it has finished.
     */
    public function download(string $src, string $cacheFile): DownloadItem
    {
        $downloadStatus = new DownloadStatus(
            true,
            [],
        );

        $onFulFilled = function (ResponseInterface $response) use ($downloadStatus, $cacheFile): void {
            $hasSuccess = $response->getStatusCode() < StatusCodeInterface::STATUS_BAD_REQUEST;

            if (true === $hasSuccess) {
                $hasSuccess = false !== file_put_contents(
                    $cacheFile,
                    (string) $response->getBody()
                );
            }

            $hasSuccess = $hasSuccess && file_exists($cacheFile);

            /** @var array<int, Throwable> $errors */
            $errors = [];

            if (false === $hasSuccess) {
                $errors[] = new Exception('Failed to download resource.');
            }

            $downloadStatus->update($hasSuccess, $errors);
        };

        $onRejected = function (Throwable $error) use ($downloadStatus): void {
            $downloadStatus->update(
                false,
                [$error],
            );
        };

        $this->browser
            ->get($src)
            ->then($onFulFilled, $onRejected)
        ;

        return new DownloadItem(
            $src,
            $cacheFile,
            true,
            [],
            $downloadStatus,
        );
    }
}
