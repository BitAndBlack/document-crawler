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

readonly class Iframe implements DtoInterface
{
    public function __construct(
        private string $src,
        private string|null $title,
        private string|null $width,
        private string|null $height,
    ) {
    }

    public function __toString(): string
    {
        return $this->getSrc();
    }

    /**
     * @return array{
     *     src: string,
     *     title: string|null,
     *     width: string|null,
     *     height: string|null,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'src' => $this->getSrc(),
            'title' => $this->getTitle(),
            'width' => $this->getWidth(),
            'height' => $this->getHeight(),
        ];
    }

    public function getSrc(): string
    {
        return $this->src;
    }

    public function getTitle(): string|null
    {
        return $this->title;
    }

    public function getWidth(): string|null
    {
        return $this->width;
    }

    public function getHeight(): string|null
    {
        return $this->height;
    }
}
