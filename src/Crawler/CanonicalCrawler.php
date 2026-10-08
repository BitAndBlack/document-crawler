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

namespace BitAndBlack\DocumentCrawler\Crawler;

use BitAndBlack\DocumentCrawler\ResourceHandler\ResourceHandlerInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Crawl and extract the canonical URL of a document, that has been declared with `<link rel="canonical" href="..." />`.
 * __Please note:__ Only the first matching link tag gets used and the `rel` value must equal `canonical` exactly —
 * link tags with multiple rel values (e.g. `rel="canonical alternate"`) are not picked up.
 * The URL gets returned exactly as written; relative URLs do not get resolved here.
 */
class CanonicalCrawler implements CrawlerInterface
{
    private string|null $canonicalUrl = null;

    public function __construct(
        private readonly Crawler $crawler,
    ) {
    }

    public function crawlContent(): void
    {
        $canonicalLink = $this->crawler
            ->filter('head link[rel="canonical"]')
            ->first()
        ;

        if (0 === $canonicalLink->count()) {
            return;
        }

        $href = $canonicalLink->attr('href');

        if (null === $href || '' === $href) {
            return;
        }

        $this->canonicalUrl = $href;
    }

    public function getCanonicalUrl(): string|null
    {
        return $this->canonicalUrl;
    }

    public function setResourceHandler(ResourceHandlerInterface $resourceHandler): self
    {
        // Not needed here.
        return $this;
    }
}
