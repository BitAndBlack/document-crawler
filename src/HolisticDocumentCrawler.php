<?php

/**
 * Bit&Black Document Crawler.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

namespace BitAndBlack\DocumentCrawler;

use BitAndBlack\DocumentCrawler\Crawler\AnchorsCrawler;
use BitAndBlack\DocumentCrawler\Crawler\IconsCrawler;
use BitAndBlack\DocumentCrawler\Crawler\ImagesCrawler;
use BitAndBlack\DocumentCrawler\Crawler\LanguageCodeCrawler;
use BitAndBlack\DocumentCrawler\Crawler\MetaTagsCrawler;
use BitAndBlack\DocumentCrawler\Crawler\TitleCrawler;
use BitAndBlack\DocumentCrawler\DTO\Anchor;
use BitAndBlack\DocumentCrawler\DTO\Icon;
use BitAndBlack\DocumentCrawler\DTO\Image;
use BitAndBlack\DocumentCrawler\DTO\LanguageCode;
use BitAndBlack\DocumentCrawler\DTO\MetaTag;
use BitAndBlack\DocumentCrawler\HttpClient\HttpClientInterface;
use BitAndBlack\DocumentCrawler\HttpClient\HttpDiscoveryClient;
use BitAndBlack\DocumentCrawler\ResourceHandler\PassiveResourceHandler;
use BitAndBlack\DocumentCrawler\ResourceHandler\ResourceHandlerInterface;
use BitAndBlack\DocumentCrawler\Util\BaseUrl;
use Symfony\Component\DomCrawler\Crawler;

/**
 * This crawler takes a document as a whole and runs
 *
 * * the {@see IconsCrawler}
 * * the {@see ImagesCrawler}
 * * the {@see LanguageCodeCrawler}
 * * the {@see MetaTagsCrawler}
 * * and the {@see TitleCrawler}
 *
 * Instead of initializing the class with a document, it's also possible to use the
 * {@see HolisticDocumentCrawler::createFromUrl()} method and use a URL instead.
 */
readonly class HolisticDocumentCrawler
{
    private IconsCrawler $iconsCrawler;

    private ImagesCrawler $imagesCrawler;

    private LanguageCodeCrawler $languageCodeCrawler;

    private MetaTagsCrawler $metaTagsCrawler;

    private TitleCrawler $titleCrawler;

    private AnchorsCrawler $anchorsCrawler;

    /**
     * @param string $document The content of an HTML or XML document.
     * @param string|null $baseUrl A URL that gets used for every relative URL in the document to make an absolute URL out of it.
     *                             This URL will be converted to a base URL automatically.
     * @param ResourceHandlerInterface $resourceHandler
     */
    public function __construct(
        string $document,
        string|null $baseUrl = null,
        private ResourceHandlerInterface $resourceHandler = new PassiveResourceHandler(),
    ) {
        if (null !== $baseUrl) {
            $baseUrl = (string) new BaseUrl($baseUrl);
        }

        $crawler = new Crawler($document, $baseUrl);

        $this->iconsCrawler = new IconsCrawler($crawler);
        $this->iconsCrawler->setResourceHandler($this->resourceHandler);
        $this->iconsCrawler->crawlContent();

        $this->imagesCrawler = new ImagesCrawler($crawler);
        $this->imagesCrawler->setResourceHandler($this->resourceHandler);
        $this->imagesCrawler->crawlContent();

        $this->languageCodeCrawler = new LanguageCodeCrawler($crawler);
        $this->languageCodeCrawler->crawlContent();

        $this->metaTagsCrawler = new MetaTagsCrawler($crawler);
        $this->metaTagsCrawler->setResourceHandler($this->resourceHandler);
        $this->metaTagsCrawler->crawlContent();

        $this->titleCrawler = new TitleCrawler($crawler);
        $this->titleCrawler->crawlContent();

        $this->anchorsCrawler = new AnchorsCrawler($crawler);
        $this->anchorsCrawler->crawlContent();
    }

    /**
     * Initialise the class with a URL instead of a document.
     * The content will be fetched at first and then crawled as second.
     *
     * @throws Exception
     */
    public static function createFromUrl(
        string $url,
        ResourceHandlerInterface $resourceHandler = new PassiveResourceHandler(),
        HttpClientInterface $httpClient = new HttpDiscoveryClient(),
    ): self {
        $response = $httpClient->requestUrl($url);
        $content = $response->getBody()->getContents();
        return new self($content, $url, $resourceHandler);
    }

    public function getResourceHandler(): ResourceHandlerInterface
    {
        return $this->resourceHandler;
    }

    /**
     * @return array<string, array<int, MetaTag>>
     */
    public function getMetaTags(): array
    {
        return $this->metaTagsCrawler->getMetaTags();
    }

    /**
     * @return array<int, Icon>
     */
    public function getIcons(): array
    {
        return $this->iconsCrawler->getIcons();
    }

    /**
     * @return string|null
     */
    public function getTitle(): string|null
    {
        return $this->titleCrawler->getTitle();
    }

    /**
     * @return array<int, Image>
     */
    public function getImages(): array
    {
        return $this->imagesCrawler->getImages();
    }

    public function getLanguageCode(): LanguageCode|null
    {
        return $this->languageCodeCrawler->getLanguageCode();
    }

    /**
     * @return array<int, Anchor>
     */
    public function getAnchors(): array
    {
        return $this->anchorsCrawler->getAnchors();
    }
}
