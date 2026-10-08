<?php

/**
 * Bit&Black Document Crawler.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

namespace BitAndBlack\DocumentCrawler\DTO;

readonly class StructuredData implements DtoInterface
{
    /**
     * @param array<int|string, mixed> $decoded The decoded JSON as array; empty when the JSON is invalid
     *                                          or does not decode to an object or a list.
     * @param list<string> $types All types found in this block, flattened from `@graph`,
     *                            `@type` and `itemListElement`, deduplicated in first-seen order.
     */
    public function __construct(
        private string $json,
        private array $decoded,
        private bool $validJson,
        private array $types,
    ) {
    }

    public function __toString(): string
    {
        return $this->getJson();
    }

    /**
     * @return array{
     *     json: string,
     *     decoded: array<int|string, mixed>,
     *     validJson: bool,
     *     types: list<string>,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'json' => $this->getJson(),
            'decoded' => $this->getDecoded(),
            'validJson' => $this->isValidJson(),
            'types' => $this->getTypes(),
        ];
    }

    public function getJson(): string
    {
        return $this->json;
    }

    /**
     * @return array<int|string, mixed>
     */
    public function getDecoded(): array
    {
        return $this->decoded;
    }

    public function isValidJson(): bool
    {
        return $this->validJson;
    }

    /**
     * @return list<string>
     */
    public function getTypes(): array
    {
        return $this->types;
    }
}
