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

use BitAndBlack\DocumentCrawler\DTO\Link;
use BitAndBlack\DocumentCrawler\ResourceHandler\PassiveResourceHandler;
use BitAndBlack\DocumentCrawler\ResourceHandler\ResourceHandlerInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Crawl and extract all defined link tags in a document, that have been declared with `<link ... />`.
 * __Please note:__ Currently only links inside the head tag will be crawled.
 */
class LinkTagsCrawler implements CrawlerInterface
{
    /**
     * @var list<Link>
     */
    private array $links = [];

    private ResourceHandlerInterface $resourceHandler;

    public function __construct(
        private readonly Crawler $crawler,
    ) {
        $this->resourceHandler = new PassiveResourceHandler();
    }

    public function crawlContent(): void
    {
        $eachNode = static fn (Crawler $node): array => [
            'as' => $node->attr('as'),
            'blocking' => $node->attr('blocking'),
            'crossOrigin' => $node->attr('crossorigin'),
            'disabled' => $node->attr('disabled'),
            'fetchPriority' => $node->attr('fetchpriority'),
            'href' => $node->attr('href'),
            'hreflang' => $node->attr('hreflang'),
            'imageSizes' => $node->attr('imagesizes'),
            'imageSrcSet' => $node->attr('imagesrcset'),
            'integrity' => $node->attr('integrity'),
            'media' => $node->attr('media'),
            'referrerPolicy' => $node->attr('referrerpolicy'),
            'rel' => $node->attr('rel'),
            'sizes' => $node->attr('sizes'),
            'target' => $node->attr('target'),
            'title' => $node->attr('title'),
            'type' => $node->attr('type'),
        ];

        /**
         * @var array<int, array{
         *     as: string|null,
         *     blocking: string|null,
         *     crossOrigin: string|null,
         *     disabled: string|null,
         *     fetchPriority: string|null,
         *     href: string|null,
         *     hreflang: string|null,
         *     imageSizes: string|null,
         *     imageSrcSet: string|null,
         *     integrity: string|null,
         *     media: string|null,
         *     referrerPolicy: string|null,
         *     rel: string,
         *     sizes: string|null,
         *     target: string|null,
         *     title: string|null,
         *     type: string|null,
         * }> $links
         */
        $links = $this->crawler
            ->filter('head > link')
            ->each($eachNode)
        ;

        foreach ($links as $link) {
            $href = $link['href'];

            if (null !== $href) {
                $hrefHandled = $this->resourceHandler->handleResource(
                    $href,
                    $this->crawler->getUri()
                );

                if (false === $hrefHandled) {
                    continue;
                }

                $href = $hrefHandled;
            }

            $this->links[] = new Link(
                $link['rel'],
                $link['as'],
                $link['blocking'],
                $link['crossOrigin'],
                $link['disabled'],
                $link['fetchPriority'],
                $href,
                $link['hreflang'],
                $link['imageSizes'],
                $link['imageSrcSet'],
                $link['integrity'],
                $link['media'],
                $link['referrerPolicy'],
                $link['sizes'],
                $link['target'],
                $link['title'],
                $link['type'],
            );
        }
    }

    /**
     * @return list<Link>
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    public function setResourceHandler(ResourceHandlerInterface $resourceHandler): self
    {
        $this->resourceHandler = $resourceHandler;
        return $this;
    }
}
