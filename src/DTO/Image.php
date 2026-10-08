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

namespace BitAndBlack\DocumentCrawler\DTO;

readonly class Image implements DtoInterface
{
    public function __construct(
        private string $resource,
        private string|null $alt,
        private string|null $title,
        private string|null $loading = null,
        private string|null $width = null,
        private string|null $height = null,
        private string|null $srcset = null,
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
     *     loading: string|null,
     *     width: string|null,
     *     height: string|null,
     *     srcset: string|null,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'resource' => $this->getResource(),
            'alt' => $this->getAlt(),
            'title' => $this->getTitle(),
            'loading' => $this->getLoading(),
            'width' => $this->getWidth(),
            'height' => $this->getHeight(),
            'srcset' => $this->getSrcset(),
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

    public function getLoading(): string|null
    {
        return $this->loading;
    }

    public function getWidth(): string|null
    {
        return $this->width;
    }

    public function getHeight(): string|null
    {
        return $this->height;
    }

    public function getSrcset(): string|null
    {
        return $this->srcset;
    }
}
