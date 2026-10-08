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

use BitAndBlack\DocumentCrawler\DTO\StructuredData;
use BitAndBlack\DocumentCrawler\ResourceHandler\ResourceHandlerInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Crawl and extract all structured data blocks in a document, that have been declared with
 * `<script type="application/ld+json">...</script>`.
 * __Please note:__ The JSON only gets parsed, it does not get validated against a schema.
 */
class StructuredDataCrawler implements CrawlerInterface
{
    /**
     * @var array<int, StructuredData>
     */
    private array $structuredData = [];

    public function __construct(
        private readonly Crawler $crawler,
    ) {
    }

    public function crawlContent(): void
    {
        $eachNode = static fn (Crawler $node): string => $node->text(null, false);

        /** @var array<int, string> $jsonBlocks */
        $jsonBlocks = $this->crawler
            ->filter('script[type="application/ld+json"]')
            ->each($eachNode)
        ;

        foreach ($jsonBlocks as $json) {
            $decoded = json_decode($json, true);

            $validJson = JSON_ERROR_NONE === json_last_error();

            if (false === $validJson || false === is_array($decoded)) {
                $decoded = [];
            }

            $this->structuredData[] = new StructuredData(
                json: $json,
                decoded: $decoded,
                validJson: $validJson,
                types: true === $validJson ? $this->flattenTypes($decoded) : [],
            );
        }
    }

    /**
     * @return array<int, StructuredData>
     */
    public function getStructuredData(): array
    {
        return $this->structuredData;
    }

    public function setResourceHandler(ResourceHandlerInterface $resourceHandler): self
    {
        // Not needed here.
        return $this;
    }

    /**
     * Flattens all types of a decoded JSON-LD block. `@type` (string or list of strings),
     * `@graph`, `graph[].@type` and `itemListElement` get walked recursively; every type
     * gets reported only once per block, in first-seen order.
     *
     * @param array<int|string, mixed> $data
     *
     * @return list<string>
     */
    private function flattenTypes(array $data): array
    {
        $types = [];

        $this->collectTypes($data, $types);

        /** @var list<string> $types */
        $types = array_values(array_unique($types));

        return $types;
    }

    /**
     * @param array<int|string, mixed> $data
     * @param list<string> $types
     */
    private function collectTypes(array $data, array &$types): void
    {
        if (true === array_is_list($data) && [] !== $data) {
            foreach ($data as $item) {
                if (true === is_array($item)) {
                    $this->collectTypes($item, $types);
                }
            }

            return;
        }

        $type = $data['@type'] ?? null;

        if (true === is_string($type) && '' !== $type) {
            $types[] = $type;
        }

        if (true === is_array($type)) {
            foreach ($type as $subType) {
                if (true === is_string($subType) && '' !== $subType) {
                    $types[] = $subType;
                }
            }
        }

        foreach (['@graph', 'graph', 'itemListElement'] as $collectionKey) {
            $collection = $data[$collectionKey] ?? null;

            if (false === is_array($collection)) {
                continue;
            }

            foreach ($collection as $item) {
                if (true === is_array($item)) {
                    $this->collectTypes($item, $types);
                }
            }
        }
    }
}
