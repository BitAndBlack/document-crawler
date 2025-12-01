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

readonly class Icon implements DtoInterface
{
    public function __construct(
        private string $name,
        private string $resource,
    ) {
    }

    public function __toString(): string
    {
        return $this->getResource();
    }

    /**
     * @return array{
     *     name: string,
     *     resource: string,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->getName(),
            'resource' => $this->getResource(),
        ];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getResource(): string
    {
        return $this->resource;
    }
}
