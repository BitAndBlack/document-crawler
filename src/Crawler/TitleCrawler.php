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

use BitAndBlack\DocumentCrawler\ResourceHandler\ResourceHandlerInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Crawl and extract the title of a document, that has been declared with `<title>...</title>`.
 */
class TitleCrawler implements CrawlerInterface
{
    private ?string $title = null;

    public function __construct(
        private readonly Crawler $crawler,
    ) {
    }

    public function crawlContent(): void
    {
        $titleMissing = '__TITLE_MISSING__';

        $title = $this->crawler
            ->filter('head > title')
            ->first()
            ->text($titleMissing)
        ;

        if ($titleMissing !== $title) {
            $this->title = $title;
        }
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setResourceHandler(ResourceHandlerInterface $resourceHandler): self
    {
        // Not needed here.
        return $this;
    }
}
