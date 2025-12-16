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

use BitAndBlack\DocumentCrawler\DTO\Anchor;
use BitAndBlack\DocumentCrawler\ResourceHandler\ResourceHandlerInterface;
use BitAndBlack\DocumentCrawler\Util\UrlParser;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Crawl and extract all defined anchors in a document, that have been declared with `<a href="..."> ... </a>`.
 */
class AnchorsCrawler implements CrawlerInterface
{
    /**
     * @var array<int, Anchor>
     */
    private array $anchors;

    public function __construct(
        private readonly Crawler $crawler,
    ) {
    }

    public function crawlContent(): void
    {
        $eachNode = static function (Crawler $node): Anchor|null {
            $href = $node->attr('href');

            if (null === $href) {
                return null;
            }

            $urlParsed = UrlParser::parse($href);

            $urlParsed = $urlParsed->withQuery(null);
            $urlParsed = $urlParsed->withFragment(null);

            $href = $urlParsed->getUrl();

            if (true === empty($href)) {
                return null;
            }

            $title = $node->attr('title');

            if (true === empty($title)) {
                $title = null;
            }

            $text = $node->text();

            if (true === empty($text)) {
                $text = null;
            }

            return new Anchor(
                $href,
                $text,
                $title
            );
        };

        /** @var array<int, Anchor|null> $anchors */
        $anchors = $this->crawler
            ->filter('a')
            ->each($eachNode)
        ;

        $this->anchors = array_filter($anchors);
    }

    /**
     * @return array<int, Anchor>
     */
    public function getAnchors(): array
    {
        return $this->anchors;
    }

    public function setResourceHandler(ResourceHandlerInterface $resourceHandler): self
    {
        // Not needed here.
        return $this;
    }
}
