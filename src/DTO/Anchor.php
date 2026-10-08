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

readonly class Anchor implements DtoInterface
{
    public function __construct(
        private string $href,
        private string|null $text,
        private string|null $title,
        private string|null $rel = null,
    ) {
    }

    public function __toString(): string
    {
        return $this->getHref();
    }

    /**
     * @return array{
     *     href: string,
     *     text: string|null,
     *     title: string|null,
     *     rel: string|null,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'href' => $this->getHref(),
            'text' => $this->getText(),
            'title' => $this->getTitle(),
            'rel' => $this->getRel(),
        ];
    }

    public function getHref(): string
    {
        return $this->href;
    }

    public function getText(): string|null
    {
        return $this->text;
    }

    public function getTitle(): string|null
    {
        return $this->title;
    }

    public function getRel(): string|null
    {
        return $this->rel;
    }
}
