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
        $eachNode = static function (Crawler $node): ?array {
            $key = $node->attr('rel');

            if (null === $key || !str_contains($key, 'icon')) {
                return null;
            }

            return [
                'name' => $key,
                'value' => $node->attr('href'),
            ];
        };

        /**
         * @var array<int, array{
         *     name: string,
         *     value: string|null,
         * }|null> $favicons
         */
        $favicons = $this->crawler
            ->filter('head > link')
            ->each($eachNode)
        ;

        $favicons = array_filter($favicons);

        foreach ($favicons as $favicon) {
            $iconName = $favicon['name'];
            $iconResource = $favicon['value'];

            if (null === $iconResource) {
                continue;
            }

            $iconResourceHandled = $this->resourceHandler->handleResource(
                $iconResource,
                (string) $this->crawler->getUri()
            );

            if (false === $iconResourceHandled) {
                continue;
            }

            $this->icons[] = new Icon($iconName, $iconResourceHandled);
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
