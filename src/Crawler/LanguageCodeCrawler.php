<?php

/**
 * Bit&Black Document Crawler.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

namespace BitAndBlack\DocumentCrawler\Crawler;

use BitAndBlack\DocumentCrawler\DTO\LanguageCode;
use BitAndBlack\DocumentCrawler\ResourceHandler\ResourceHandlerInterface;
use Places2Be\Locales\Exception;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Crawl and extract the language code of a document, that has been declared with `<html lang="...">`.
 */
class LanguageCodeCrawler implements CrawlerInterface
{
    private ?LanguageCode $languageCode = null;

    public function __construct(
        private readonly Crawler $crawler,
    ) {
    }

    public function crawlContent(): void
    {
        $languageCodeString = $this->crawler
            ->filter('html')
            ->first()
            ->attr('lang')
        ;

        try {
            $languageCode = new LanguageCode(
                (string) $languageCodeString,
                allowCountryUnspecificLanguageCode: true
            );
        } catch (Exception) {
            $languageCode = null;
        }

        $this->languageCode = $languageCode;
    }

    public function getLanguageCode(): ?LanguageCode
    {
        return $this->languageCode;
    }

    public function setResourceHandler(ResourceHandlerInterface $resourceHandler): self
    {
        // Not needed here.
        return $this;
    }
}
