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

use BitAndBlack\DocumentCrawler\DTO\Heading;
use BitAndBlack\DocumentCrawler\ResourceHandler\ResourceHandlerInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Crawl and extract all headings in a document, that have been declared with `<h1>...</h1>`
 * up to `<h6>...</h6>`.
 * __Please note:__ An empty heading gets extracted with an empty text, it does not get skipped.
 */
class HeadingsCrawler implements CrawlerInterface
{
    /**
     * @var array<int, Heading>
     */
    private array $headings = [];

    public function __construct(
        private readonly Crawler $crawler,
    ) {
    }

    public function crawlContent(): void
    {
        $eachNode = static function (Crawler $node): Heading {
            $level = (int) substr($node->nodeName(), 1);

            return new Heading(
                level: $level,
                text: $node->text(),
            );
        };

        /** @var array<int, Heading> $headings */
        $headings = $this->crawler
            ->filter('h1, h2, h3, h4, h5, h6')
            ->each($eachNode)
        ;

        $this->headings = $headings;
    }

    /**
     * @return array<int, Heading>
     */
    public function getHeadings(): array
    {
        return $this->headings;
    }

    /**
     * The amount of headings per level, e.g. `[1 => 1, 2 => 4]`.
     * Levels without headings do not get reported.
     *
     * @return array<int, int>
     */
    public function getHeadingCounts(): array
    {
        $counts = [];

        foreach ($this->headings as $heading) {
            $level = $heading->getLevel();
            $counts[$level] = ($counts[$level] ?? 0) + 1;
        }

        ksort($counts);

        return $counts;
    }

    public function setResourceHandler(ResourceHandlerInterface $resourceHandler): self
    {
        // Not needed here.
        return $this;
    }
}
