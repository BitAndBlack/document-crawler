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

use BitAndBlack\DocumentCrawler\DTO\Icon;
use BitAndBlack\DocumentCrawler\DTO\Link;
use BitAndBlack\DocumentCrawler\ResourceHandler\PassiveResourceHandler;
use BitAndBlack\DocumentCrawler\ResourceHandler\ResourceHandlerInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Crawl and extract all defined icons in a document, that have been declared with `<link rel="icon" ... />`.
 */
class IconsCrawler implements CrawlerInterface
{
    /**
     * @var array<int, Icon>
     */
    private array $icons = [];

    private ResourceHandlerInterface $resourceHandler;

    public function __construct(
        private readonly Crawler $crawler,
    ) {
        $this->resourceHandler = new PassiveResourceHandler();
    }

    public function crawlContent(): void
    {
        $linkTagsCrawler = new LinkTagsCrawler($this->crawler);
        $linkTagsCrawler->setResourceHandler($this->resourceHandler);
        $linkTagsCrawler->crawlContent();
        $links = $linkTagsCrawler->getLinks();

        foreach ($links as $link) {
            if (null === $link->getHref() || false === str_contains($link->getRel(), 'icon')) {
                continue;
            }

            $this->icons[] = new Icon(
                $link->getRel(),
                $link->getHref(),
            );
        }
    }

    /**
     * @return array<int, Icon>
     */
    public function getIcons(): array
    {
        return $this->icons;
    }

    public function setResourceHandler(ResourceHandlerInterface $resourceHandler): self
    {
        $this->resourceHandler = $resourceHandler;
        return $this;
    }
}
