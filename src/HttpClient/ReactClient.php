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

use BitAndBlack\Composer\Composer;
use BitAndBlack\DocumentCrawler\DownloadItem\DownloadItem;
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
     * @param Browser|null $browser
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
     * @param string $url
     * @return ResponseInterface
     * @throws Exception
     */
    public function requestUrl(string $url): ResponseInterface
    {
        $promise = $this->browser->get($url);

        try {
            $response = await($promise);
        } catch (Throwable $error) {
            throw new Exception('Failed to request URL.', $error);
        }

        return $response;
    }

    /**
     * Loads an external resource and stores it somewhere in the file system.
     * This may happen asynchronously.
     *
     * @param string $src
     * @param string $cacheFile
     * @return DownloadItem
     */
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
