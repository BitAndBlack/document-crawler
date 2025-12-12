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
use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

readonly class HttpDiscoveryClient implements HttpClientInterface
{
    private ClientInterface $psr18Client;

    private RequestFactoryInterface $requestFactory;

    public function __construct()
    {
        $this->psr18Client = Psr18ClientDiscovery::find();
        $this->requestFactory = Psr17FactoryDiscovery::findRequestFactory();
    }

    /**
     * Requests a URL in a blocking, non-asynchronously way and returns the response.
     *
     * @throws Exception
     */
    public function requestUrl(string $url): ResponseInterface
    {
        $request = $this->requestFactory->createRequest(RequestMethodInterface::METHOD_GET, $url);

        try {
            $response = $this->psr18Client->sendRequest($request);
        } catch (ClientExceptionInterface $clientException) {
            throw new Exception('Failed to request URL.', $clientException);
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

        $response = null;

        try {
            $response = $this->requestUrl($src);
        } catch (Exception $error) {
            $errors[] = $error;
            $hasSuccess = false;
        }

        if (null !== $response && $response->getStatusCode() > StatusCodeInterface::STATUS_BAD_REQUEST) {
            $hasSuccess = false;
        }

        if (null !== $response && true === $hasSuccess) {
            $hasSuccess = false !== file_put_contents(
                $cacheFile,
                (string) $response->getBody()
            );
        }

        return new DownloadItem(
            $src,
            $cacheFile,
            $hasSuccess,
            $errors,
        );
    }
}
