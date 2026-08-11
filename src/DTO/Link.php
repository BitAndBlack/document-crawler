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

readonly class Link implements DtoInterface
{
    public function __construct(
        private string $rel,
        private string|null $as,
        private string|null $blocking,
        private string|null $crossOrigin,
        private string|null $disabled,
        private string|null $fetchPriority,
        private string|null $href,
        private string|null $hreflang,
        private string|null $imageSizes,
        private string|null $imageSrcSet,
        private string|null $integrity,
        private string|null $media,
        private string|null $referrerPolicy,
        private string|null $sizes,
        private string|null $target,
        private string|null $title,
        private string|null $type,
    ) {
    }

    public function __toString(): string
    {
        /**
         * @todo Check if this makes sense.
         */
        return $this->getRel();
    }

    /**
     * @return array{
     *     rel: string,
     *     as: string|null,
     *     blocking: string|null,
     *     crossOrigin: string|null,
     *     disabled: string|null,
     *     fetchPriority: string|null,
     *     href: string|null,
     *     hreflang: string|null,
     *     imageSizes: string|null,
     *     imageSrcSet: string|null,
     *     integrity: string|null,
     *     media: string|null,
     *     referrerPolicy: string|null,
     *     sizes: string|null,
     *     target: string|null,
     *     title: string|null,
     *     type: string|null,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'rel' => $this->getRel(),
            'as' => $this->getAs(),
            'blocking' => $this->getBlocking(),
            'crossOrigin' => $this->getCrossOrigin(),
            'disabled' => $this->getDisabled(),
            'fetchPriority' => $this->getFetchPriority(),
            'href' => $this->getHref(),
            'hreflang' => $this->getHreflang(),
            'imageSizes' => $this->getImageSizes(),
            'imageSrcSet' => $this->getImageSrcSet(),
            'integrity' => $this->getIntegrity(),
            'media' => $this->getMedia(),
            'referrerPolicy' => $this->getReferrerPolicy(),
            'sizes' => $this->getSizes(),
            'target' => $this->getTarget(),
            'title' => $this->getTitle(),
            'type' => $this->getType(),
        ];
    }

    public function getRel(): string
    {
        return $this->rel;
    }

    public function getAs(): string|null
    {
        return $this->as;
    }

    public function getBlocking(): string|null
    {
        return $this->blocking;
    }

    public function getCrossOrigin(): string|null
    {
        return $this->crossOrigin;
    }

    public function getDisabled(): string|null
    {
        return $this->disabled;
    }

    public function getFetchPriority(): string|null
    {
        return $this->fetchPriority;
    }

    public function getHref(): string|null
    {
        return $this->href;
    }

    public function getHreflang(): string|null
    {
        return $this->hreflang;
    }

    public function getImageSizes(): string|null
    {
        return $this->imageSizes;
    }

    public function getImageSrcSet(): string|null
    {
        return $this->imageSrcSet;
    }

    public function getIntegrity(): string|null
    {
        return $this->integrity;
    }

    public function getMedia(): string|null
    {
        return $this->media;
    }

    public function getReferrerPolicy(): string|null
    {
        return $this->referrerPolicy;
    }

    public function getSizes(): string|null
    {
        return $this->sizes;
    }

    public function getTarget(): string|null
    {
        return $this->target;
    }

    public function getTitle(): string|null
    {
        return $this->title;
    }

    public function getType(): string|null
    {
        return $this->type;
    }
}
