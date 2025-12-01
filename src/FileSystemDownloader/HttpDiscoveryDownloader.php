<?php

/**
 * Bit&Black Document Crawler.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

namespace BitAndBlack\DocumentCrawler\FileSystemDownloader;

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Throwable;

readonly class HttpDiscoveryDownloader implements FileSystemDownloaderInterface
{
    private ClientInterface $psr18Client;

    private RequestFactoryInterface $requestFactory;

    public function __construct()
    {
        $this->psr18Client = Psr18ClientDiscovery::find();
        $this->requestFactory = Psr17FactoryDiscovery::findRequestFactory();
    }

    public function download(string $src, string $cacheFile): DownloadItem
    {
        $hasSuccess = true;

        /** @var array<int, Throwable> $errors */
        $errors = [];

        $request = $this->requestFactory->createRequest(RequestMethodInterface::METHOD_GET, $src);
        $response = null;

        try {
            $response = $this->psr18Client->sendRequest($request);
        } catch (ClientExceptionInterface $error) {
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
