<?php

/**
 * Bit&Black Document Crawler.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

namespace BitAndBlack\DocumentCrawler\ResourceHandler;

use BitAndBlack\DocumentCrawler\FileSystemDownloader\FileSystemDownloaderInterface;
use BitAndBlack\DocumentCrawler\FileSystemDownloader\HttpDiscoveryDownloader;
use BitAndBlack\DocumentCrawler\Util\BaseUrl;
use BitAndBlack\PathInfo\PathInfo;
use Throwable;

class FileSystemDownloadHandler implements ResourceHandlerInterface
{
    private bool $skipExternalResources = false;

    private bool $hasHandledAllResources = true;

    /**
     * @var array<int, Throwable>
     */
    private array $errors = [];

    private bool $resourceNameHashingEnabled = false;

    /**
     * @param string $cacheFolderPath Local path to a folder, where the resource can be stored.
     * @param string|null $cachedResourceFileNamePrefix Additional path that gets prepended to the resource name.
     *                                                  This one is useful, for example if you want to store
     *                                                  the resource in a folder, that is available publicly.
     * @param FileSystemDownloaderInterface $fileSystemDownloader
     */
    public function __construct(
        private readonly string $cacheFolderPath,
        private readonly string|null $cachedResourceFileNamePrefix = null,
        private readonly FileSystemDownloaderInterface $fileSystemDownloader = new HttpDiscoveryDownloader(),
    ) {
    }

    /**
     * Normalizes the url of a resource, creates a hashed name and downloads it.
     *
     * @param string $src Absolute or relative path to a resource, for example `/build/images/my-file-1.jpg`.
     * @param string $baseUrl The base uri to use along with the resource, for example `https://www.example.org`.
     *                        The base uri helps to verify, if a resource belongs to the same domain or not.
     * @return string|false The name of the handled resource (can be modified), or `false` on failure.
     */
    public function handleResource(string $src, string $baseUrl): string|false
    {
        $baseUrl = (string) new BaseUrl($baseUrl);

        /**
         * Change relativ urls to absolute ones.
         */
        if (false === str_starts_with($src, 'http') && false === str_starts_with($src, 'data:')) {
            $src = $baseUrl . '/' . ltrim($src, '/');
        }

        /**
         * Skip the wrong urls, where the image has the same source as the requested url.
         */
        if ($baseUrl === $src) {
            return false;
        }

        $isExternalUrl = str_starts_with($src, 'http')
            && !str_starts_with($src, $baseUrl)
        ;

        /**
         * Decide if external resources should be used.
         */
        if (true === $isExternalUrl && true === $this->shouldSkipExternalResources()) {
            return false;
        }

        $pathInfo = new PathInfo($src);

        if (true === $this->isResourceNameHashingEnabled()) {
            $pathInfo = $pathInfo->withFileName(
                hash('md5', $src)
            );
        }

        $fileName = $pathInfo->getBaseName();

        if (null === $fileName) {
            return false;
        }

        /**
         * Ignore resources without an extension, because they won't load well in Kiwa.
         * (Kiwa appends `.html` when a suffix is missing in a request.)
         * It could be possible that this behavior will change in the future.
         */
        if (null === $pathInfo->getExtension()) {
            return false;
        }

        $cacheFile = rtrim($this->cacheFolderPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $fileName;
        $cacheFileExists = file_exists($cacheFile);
        $hasSuccess = true;

        if (false === $cacheFileExists) {
            $this->hasHandledAllResources = false;

            $download = $this->fileSystemDownloader->download($src, $cacheFile);
            $this->errors = $download->getErrors();
            $hasSuccess = $download->hasSuccess();
        }

        if (false === $hasSuccess || false === file_exists($cacheFile)) {
            return false;
        }

        if (null !== $this->cachedResourceFileNamePrefix) {
            $fileName = rtrim($this->cachedResourceFileNamePrefix, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $fileName;
        }

        return $fileName;
    }

    /**
     * @return array<int, Throwable>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Tells if all resources have been downloaded.
     * If so, the crawling result can be cached, too.
     *
     * @return bool
     */
    public function hasHandledAllResources(): bool
    {
        return $this->hasHandledAllResources;
    }

    /**
     * Defines if external resources should be skipped.
     * This ensures that only relevant resources of the same domain are going to be used.
     * Disabling external resources can lead to problems with websites using CDNs,
     * so in this case it would better to disable this behavior.
     * The default value is `false`.
     *
     * @param bool $skipExternalResources
     * @return void
     */
    public function setSkipExternalResources(bool $skipExternalResources): void
    {
        $this->skipExternalResources = $skipExternalResources;
    }

    /**
     * Tells if external resources should be skipped.
     * This ensures that only relevant resources of the same domain are going to be used.
     * Disabling external resources can lead to problems with websites using CDNs,
     * so in this case it would better to disable this behavior.
     * The default value is `false`.
     *
     * @return bool
     */
    public function shouldSkipExternalResources(): bool
    {
        return $this->skipExternalResources;
    }

    /**
     * Tells if the resource name should be hashed.
     * This is `false` per default.
     *
     * @return bool
     */
    public function isResourceNameHashingEnabled(): bool
    {
        return $this->resourceNameHashingEnabled;
    }

    /**
     * Defines if the resource name should be hashed.
     * This is `false` per default.
     *
     * @param bool $resourceNameHashingEnabled
     * @return $this
     */
    public function setResourceNameHashingEnabled(bool $resourceNameHashingEnabled): self
    {
        $this->resourceNameHashingEnabled = $resourceNameHashingEnabled;
        return $this;
    }
}
