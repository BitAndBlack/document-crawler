<?php

/**
 * Bit&Black Document Crawler.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

namespace BitAndblack\DocumentCrawler;

use BitAndblack\DocumentCrawler\Crawler\IconsCrawler;
use BitAndblack\DocumentCrawler\Crawler\ImagesCrawler;
use BitAndblack\DocumentCrawler\Crawler\LanguageCodeCrawler;
use BitAndblack\DocumentCrawler\Crawler\MetaTagsCrawler;
use BitAndblack\DocumentCrawler\Crawler\TitleCrawler;
use BitAndblack\DocumentCrawler\DTO\Icon;
use BitAndblack\DocumentCrawler\DTO\Image;
use BitAndblack\DocumentCrawler\DTO\LanguageCode;
use BitAndblack\DocumentCrawler\DTO\MetaTag;
use BitAndblack\DocumentCrawler\ResourceHandler\PassiveResourceHandler;
use BitAndblack\DocumentCrawler\ResourceHandler\ResourceHandlerInterface;
use Fig\Http\Message\RequestMethodInterface;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientExceptionInterface;
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
    /**
     * @var array<string, array<int, MetaTag>>
     */
    private array $metaTags;

    /**
     * @var array<int, Icon>
     */
    private array $icons;

    private string|null $title;

    /**
     * @var array<int,Image>
     */
    private array $images;

    private LanguageCode|null $languageCode;

    /**
     * @param string $content
     * @param string|null $url
     * @param ResourceHandlerInterface $resourceHandler
     */
    public function __construct(
        string $content,
        string|null $url = null,
        private ResourceHandlerInterface $resourceHandler = new PassiveResourceHandler(),
    ) {
        $crawler = new Crawler($content, $url);

        $iconsCrawler = new IconsCrawler($crawler);
        $iconsCrawler->setResourceHandler($this->resourceHandler);
        $iconsCrawler->crawlContent();
        $this->icons = $iconsCrawler->getIcons();

        $imagesCrawler = new ImagesCrawler($crawler);
        $imagesCrawler->setResourceHandler($this->resourceHandler);
        $imagesCrawler->crawlContent();
        $this->images = $imagesCrawler->getImages();

        $languageCodeCrawler = new LanguageCodeCrawler($crawler);
        $languageCodeCrawler->crawlContent();
        $this->languageCode = $languageCodeCrawler->getLanguageCode();

        $metaTagsCrawler = new MetaTagsCrawler($crawler);
        $metaTagsCrawler->setResourceHandler($this->resourceHandler);
        $metaTagsCrawler->crawlContent();
        $this->metaTags = $metaTagsCrawler->getMetaTags();

        $titleCrawler = new TitleCrawler($crawler);
        $titleCrawler->crawlContent();
        $this->title = $titleCrawler->getTitle();
    }

    /**
     * Initialise the class with a URL instead of a document.
     * The content will be fetched at first and then crawled as second.
     *
     * @throws Exception
     */
    public static function createFromUrl(string $url, ResourceHandlerInterface $resourceHandler = new PassiveResourceHandler()): self
    {
        $requestFactory = Psr17FactoryDiscovery::findRequestFactory();
        $psr18Client = Psr18ClientDiscovery::find();

        $request = $requestFactory->createRequest(RequestMethodInterface::METHOD_GET, $url);

        try {
            $response = $psr18Client->sendRequest($request);
        } catch (ClientExceptionInterface $clientException) {
            throw new Exception('Failed to request URL.', $clientException);
        }

        $content = $response->getBody()->getContents();

        return new self($content, $url, $resourceHandler);
    }

    /**
     * @return array<string, array<int, MetaTag>>
     */
    public function getMetaTags(): array
    {
        return $this->metaTags;
    }

    /**
     * @return array<int, Icon>
     */
    public function getIcons(): array
    {
        return $this->icons;
    }

    /**
     * @return string|null
     */
    public function getTitle(): string|null
    {
        return $this->title;
    }

    /**
     * @return array<int, Image>
     */
    public function getImages(): array
    {
        return $this->images;
    }

    public function getResourceHandler(): ResourceHandlerInterface
    {
        return $this->resourceHandler;
    }

    public function getLanguageCode(): LanguageCode|null
    {
        return $this->languageCode;
    }
}
