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

use BitAndBlack\DocumentCrawler\HttpClient\ReactClient;
use PHPUnit\Framework\TestCase;
use React\EventLoop\Loop;

final class ReactClientTest extends TestCase
{
    public function testDownloadRunsInBackgroundAndUpdatesTheItem(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'document-crawler-test');

        self::assertIsString($tempFile);
        unlink($tempFile);

        $reactClient = new ReactClient();

        $downloadItem = $reactClient->download('http://127.0.0.1:1/unreachable-resource.png', $tempFile);

        self::assertTrue(
            $downloadItem->hasSuccess()
        );

        self::assertFileDoesNotExist(
            $tempFile
        );

        /**
         * The download runs in the background, on the event loop.
         * Running the loop to completion is what happens when the PHP process ends.
         */
        Loop::run();

        self::assertFalse(
            $downloadItem->hasSuccess()
        );

        self::assertNotEmpty(
            $downloadItem->getErrors()
        );

        self::assertFileDoesNotExist(
            $tempFile
        );
    }
}
