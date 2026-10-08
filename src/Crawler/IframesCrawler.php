<?php

/**
 * Bit&Black Document Crawler.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

namespace BitAndBlack\DocumentCrawler\Crawler;

use BitAndBlack\DocumentCrawler\DTO\Iframe;
use BitAndBlack\DocumentCrawler\ResourceHandler\ResourceHandlerInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Crawl and extract all defined iframes in a document, that have been declared with `<iframe ...></iframe>`.
 * __Please note:__ Iframes without a `src` attribute do not get extracted.
 */
class IframesCrawler implements CrawlerInterface
{
    /**
     * @var array<int, Iframe>
     */
    private array $iframes = [];

    public function __construct(
        private readonly Crawler $crawler,
    ) {
    }

    public function crawlContent(): void
    {
        $eachNode = static function (Crawler $node): Iframe|null {
            $src = $node->attr('src');

            if (null === $src || '' === $src) {
                return null;
            }

            $title = $node->attr('title');

            if ('' === $title) {
                $title = null;
            }

            $width = $node->attr('width');

            if ('' === $width) {
                $width = null;
            }

            $height = $node->attr('height');

            if ('' === $height) {
                $height = null;
            }

            return new Iframe(
                src: $src,
                title: $title,
                width: $width,
                height: $height,
            );
        };

        /** @var array<int, Iframe|null> $iframes */
        $iframes = $this->crawler
            ->filter('iframe')
            ->each($eachNode)
        ;

        /**
         * Remove null values and reindex.
         *
         * @var array<int, Iframe> $iframes
         */
        $iframes = array_values(array_filter($iframes));

        $this->iframes = $iframes;
    }

    /**
     * @return array<int, Iframe>
     */
    public function getIframes(): array
    {
        return $this->iframes;
    }

    public function setResourceHandler(ResourceHandlerInterface $resourceHandler): self
    {
        // Not needed here.
        return $this;
    }
}
