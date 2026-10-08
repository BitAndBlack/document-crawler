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

namespace BitAndBlack\DocumentCrawler\Tests\ResourceHandler;

use BitAndBlack\DocumentCrawler\DownloadItem\DownloadItem;
use BitAndBlack\DocumentCrawler\HttpClient\HttpClientInterface;
use BitAndBlack\DocumentCrawler\ResourceHandler\FileSystemDownloadHandler;
use BitAndBlack\DocumentCrawler\Tests\HttpClient\TestHttpClient;
use BitAndBlack\Helpers\FileSystemHelper;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

final class FileSystemDownloadHandlerTest extends TestCase
{
    private static string $tempFolder;

    public static function setUpBeforeClass(): void
    {
        self::$tempFolder = __DIR__ . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR;
        mkdir(self::$tempFolder);
    }

    protected function tearDown(): void
    {
        FileSystemHelper::deleteFolder(self::$tempFolder);
        mkdir(self::$tempFolder);
    }

    public static function tearDownAfterClass(): void
    {
        FileSystemHelper::deleteFolder(self::$tempFolder);
    }

    public function testHandleResource(): void
    {
        $path = self::$tempFolder;
        $pathAdditional = '/my/path/additional';

        $fileSystemDownloadHandler = new FileSystemDownloadHandler(
            $path,
            $pathAdditional,
            new TestHttpClient(),
        );

        $resource = $fileSystemDownloadHandler->handleResource('/favicon.ico', 'https://www.bitandblack.com');

        self::assertStringContainsString(
            $pathAdditional,
            (string) $resource
        );

        $files = glob($path . DIRECTORY_SEPARATOR . '*.ico');

        self::assertIsArray($files);

        self::assertCount(
            1,
            $files
        );

        foreach ($files as $file) {
            unlink($file);
        }
    }

    public function testCanHashFile(): void
    {
        $path = self::$tempFolder;
        $pathAdditional = '/my/path/additional';

        $fileSystemDownloadHandler = new FileSystemDownloadHandler(
            $path,
            $pathAdditional,
            new TestHttpClient(),
        );

        $resource = $fileSystemDownloadHandler->handleResource('/favicon.ico', 'https://www.bitandblack.com');

        self::assertIsString($resource);

        self::assertStringContainsString(
            'favicon.ico',
            $resource
        );

        $fileSystemDownloadHandler->setResourceNameHashingEnabled(true);

        $resource = $fileSystemDownloadHandler->handleResource('/favicon.ico', 'https://www.bitandblack.com');

        self::assertIsString($resource);

        self::assertStringNotContainsString(
            'favicon.ico',
            $resource
        );
    }

    public function testCollectsErrorsOfAllDownloads(): void
    {
        $httpClient = new class() implements HttpClientInterface {
            public function requestUrl(string $url): ResponseInterface
            {
                return new Response(500);
            }

            public function download(string $src, string $cacheFile): DownloadItem
            {
                return new DownloadItem(
                    $src,
                    $cacheFile,
                    false,
                    [new RuntimeException('Downloading "' . $src . '" failed.')],
                );
            }
        };

        $fileSystemDownloadHandler = new FileSystemDownloadHandler(
            self::$tempFolder,
            null,
            $httpClient,
        );

        self::assertFalse(
            $fileSystemDownloadHandler->handleResource('/first.png', 'https://www.bitandblack.com')
        );

        self::assertFalse(
            $fileSystemDownloadHandler->handleResource('/second.png', 'https://www.bitandblack.com')
        );

        self::assertCount(
            2,
            $fileSystemDownloadHandler->getErrors()
        );
    }

    public function testSkipsExternalResourcesBasedOnHost(): void
    {
        $fileSystemDownloadHandler = new FileSystemDownloadHandler(
            self::$tempFolder,
            null,
            new TestHttpClient(),
        );

        $fileSystemDownloadHandler->setSkipExternalResources(true);

        self::assertFalse(
            $fileSystemDownloadHandler->handleResource('https://www.bitandblack.com.evil.example/pic.png', 'https://www.bitandblack.com')
        );

        self::assertIsString(
            $fileSystemDownloadHandler->handleResource('https://www.bitandblack.com/other-pic.png', 'https://www.bitandblack.com')
        );
    }

    public function testHandlesProtocolRelativeUrls(): void
    {
        $fileSystemDownloadHandler = new FileSystemDownloadHandler(
            self::$tempFolder,
            null,
            new TestHttpClient(),
        );

        self::assertSame(
            'pic.png',
            $fileSystemDownloadHandler->handleResource('//cdn.example.org/pic.png', 'https://www.bitandblack.com')
        );
    }
}
