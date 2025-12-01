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

use BitAndBlack\DocumentCrawler\ResourceHandler\FileSystemDownloadHandler;
use BitAndBlack\Helpers\FileSystemHelper;
use PHPUnit\Framework\TestCase;

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
            $pathAdditional
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
            $pathAdditional
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
}
