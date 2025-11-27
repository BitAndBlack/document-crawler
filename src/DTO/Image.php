<?php

/**
 * Bit&Black Document Crawler.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

namespace BitAndblack\DocumentCrawler\DTO;

readonly class Image implements DtoInterface
{
    public function __construct(
        private string $resource,
        private string|null $alt,
        private string|null $title,
    ) {
    }

    public function __toString(): string
    {
        return $this->getResource();
    }

    /**
     * @return array{
     *     resource: string,
     *     alt: string|null,
     *     title: string|null,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'resource' => $this->getResource(),
            'alt' => $this->getAlt(),
            'title' => $this->getTitle(),
        ];
    }

    public function getResource(): string
    {
        return $this->resource;
    }

    public function getAlt(): string|null
    {
        return $this->alt;
    }

    public function getTitle(): string|null
    {
        return $this->title;
    }
}
