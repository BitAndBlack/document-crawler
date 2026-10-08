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

namespace BitAndBlack\DocumentCrawler\Tests\HttpClient;

use BitAndBlack\DocumentCrawler\DownloadItem\DownloadItem;
use BitAndBlack\DocumentCrawler\HttpClient\HttpClientInterface;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

/**
 * A HTTP client for tests, that answers with a canned response and never touches the network.
 */
final readonly class TestHttpClient implements HttpClientInterface
{
    public function __construct(
        private string $body = '<!doctype html><html lang="en"><head><title>Example Domain</title></head><body><h1>Hello world</h1></body></html>',
    ) {
    }

    public function requestUrl(string $url): ResponseInterface
    {
        return new Response(200, [], $this->body);
    }

    public function download(string $src, string $cacheFile): DownloadItem
    {
        $hasSuccess = false !== file_put_contents($cacheFile, 'test-resource-content');

        $errors = [];

        if (false === $hasSuccess) {
            $errors[] = new RuntimeException('Failed to write test resource to "' . $cacheFile . '".');
        }

        return new DownloadItem(
            $src,
            $cacheFile,
            $hasSuccess,
            $errors,
        );
    }
}
