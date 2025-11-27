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

use BitAndblack\DocumentCrawler\DTO\MetaTag;
use BitAndblack\DocumentCrawler\ResourceHandler\PassiveResourceHandler;
use BitAndblack\DocumentCrawler\ResourceHandler\ResourceHandlerInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Crawl and extract all defined meta tags in a document, that have been declared with `<meta ... />`.
 */
class MetaTagsCrawler implements CrawlerInterface
{
    /**
     * @var array<string, array<int, MetaTag>>
     */
    private array $metaTags = [];

    private ResourceHandlerInterface $resourceHandler;

    public function __construct(
        private readonly Crawler $crawler,
    ) {
        $this->resourceHandler = new PassiveResourceHandler();
    }

    public function crawlContent(): void
    {
        $eachNode = static function (Crawler $node): ?array {
            $key = $node->attr('property')
                ?? $node->attr('name')
            ;

            $charset = $node->attr('charset');

            if (null === $key && null === $charset) {
                return null;
            }

            $value = $node->attr('content');

            if (null !== $charset) {
                $key = 'charset';
                $value = $charset;
            }

            return [
                'name' => $key,
                'value' => $value,
            ];
        };

        /**
         * @var array<int, array{
         *     name: string,
         *     value: string|null,
         * }|null> $metaTags
         */
        $metaTags = $this->crawler
            ->filter('head > meta')
            ->each($eachNode)
        ;

        /**
         * Remove null values here.
         */
        $metaTags = array_filter($metaTags);

        $metaResourcesToDownload = [
            'msapplication-TileImage',
            'og:image',
            'og:image:secure_url',
            'og:logo',
            'twitter:image',
        ];

        foreach ($metaTags as $metaTag) {
            $metaTagName = $metaTag['name'];
            $metaTagResource = $metaTag['value'];

            if (null === $metaTagResource) {
                continue;
            }

            $isDownloadableResource = in_array($metaTagName, $metaResourcesToDownload, false);

            if ($isDownloadableResource) {
                $metaTagResourceHandled = $this->resourceHandler->handleResource(
                    $metaTagResource,
                    (string) $this->crawler->getUri()
                );

                if (false === $metaTagResourceHandled) {
                    continue;
                }

                $metaTagResource = $metaTagResourceHandled;
            }

            $metaTag = new MetaTag($metaTagName, $metaTagResource);

            $this->metaTags[$metaTagName][] = $metaTag;
        }
    }

    /**
     * @return array<string, array<int, MetaTag>>
     */
    public function getMetaTags(): array
    {
        return $this->metaTags;
    }

    public function setResourceHandler(ResourceHandlerInterface $resourceHandler): self
    {
        $this->resourceHandler = $resourceHandler;
        return $this;
    }
}
