<?php

/**
 * Bit&Black Document Crawler.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

namespace BitAndblack\DocumentCrawler\Crawler;

use BitAndblack\DocumentCrawler\DTO\Image;
use BitAndblack\DocumentCrawler\ResourceHandler\PassiveResourceHandler;
use BitAndblack\DocumentCrawler\ResourceHandler\ResourceHandlerInterface;
use BitAndBlack\PathInfo\PathInfo;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Crawl and extract all defined images in a document, that have been declared with `<img ... />`.
 */
class ImagesCrawler implements CrawlerInterface
{
    /**
     * @var array<int, Image>
     */
    private array $images = [];

    private int $imageCrawlingLimit = 5;

    private ResourceHandlerInterface $resourceHandler;

    /**
     * @var array<int, string>
     */
    private array $knownImageFormats = [
        'gif',
        'png',
        'jpg',
        'jpeg',
    ];

    public function __construct(
        private readonly Crawler $crawler,
    ) {
        $this->resourceHandler = new PassiveResourceHandler();
    }

    public function crawlContent(): void
    {
        $eachNode = static function (Crawler $node): Image|null {
            $src = $node->attr('src');

            if ('' === $src || null === $src) {
                return null;
            }

            $alt = $node->attr('alt');

            if ('' === $alt) {
                $alt = null;
            }

            $title = $node->attr('title');

            if ('' === $title) {
                $title = null;
            }

            return new Image(
                resource: $src,
                alt: $alt,
                title: $title,
            );
        };

        /** @var array<int, Image|null> $images */
        $images = $this->crawler
            ->filter('img')
            ->each($eachNode)
        ;

        /**
         * Remove null values.
         *
         * @var array<int, Image> $images
         */
        $images = array_filter($images);

        $imageCounter = 0;

        foreach ($images as $image) {
            $imageResource = $image->getResource();

            $pathInfo = new PathInfo($imageResource);
            $extension = strtolower((string) $pathInfo->getExtension());

            if (!in_array($extension, $this->knownImageFormats, true)) {
                continue;
            }

            $imageResourceHandled = $this->resourceHandler->handleResource(
                $imageResource,
                (string) $this->crawler->getUri()
            );

            if (false === $imageResourceHandled) {
                continue;
            }

            $this->images[] = new Image(
                $imageResourceHandled,
                $image->getAlt(),
                $image->getTitle(),
            );

            ++$imageCounter;

            if ($imageCounter === $this->imageCrawlingLimit) {
                break;
            }
        }
    }

    /**
     * @return  array<int, Image>
     */
    public function getImages(): array
    {
        return $this->images;
    }

    public function getImageCrawlingLimit(): int
    {
        return $this->imageCrawlingLimit;
    }

    public function setImageCrawlingLimit(int $imageCrawlingLimit): self
    {
        $this->imageCrawlingLimit = $imageCrawlingLimit;
        return $this;
    }

    public function setResourceHandler(ResourceHandlerInterface $resourceHandler): self
    {
        $this->resourceHandler = $resourceHandler;
        return $this;
    }
}
