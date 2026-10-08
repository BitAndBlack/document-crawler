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

readonly class Heading implements DtoInterface
{
    /**
     * @param int $level The heading level, from 1 (h1) to 6 (h6).
     * @param string $text The heading text; an empty string, when the heading is empty.
     */
    public function __construct(
        private int $level,
        private string $text,
    ) {
    }

    public function __toString(): string
    {
        return $this->getText();
    }

    /**
     * @return array{
     *     level: int,
     *     text: string,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'level' => $this->getLevel(),
            'text' => $this->getText(),
        ];
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
