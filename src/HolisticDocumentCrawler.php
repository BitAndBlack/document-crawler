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
use Symfony\Component\DomCrawler\Crawler;
use Throwable;

/**
 * This crawler takes a document as a whole and runs
 *
 * * the {@see IconsCrawler}
 * * the {@see ImagesCrawler}
 * * the {@see LanguageCodeCrawler}
 * * the {@see MetaTagsCrawler}
 * * and the {@see TitleCrawler}
 *
 */
class HolisticDocumentCrawler
{
    /**
     * @var array<int, Throwable>
     */
    private array $errors = [];

    /**
     * @var array<string, array<int, MetaTag>>
     */
    private array $metaTags = [];

    /**
     * @var array<int, Icon>
     */
    private array $icons = [];

    private string|null $title = null;

    /**
     * @var array<int,Image>
     */
    private array $images = [];

    private LanguageCode|null $languageCode = null;

    public function __construct(
        string $url,
        private readonly ResourceHandlerInterface $resourceHandler = new PassiveResourceHandler(),
    ) {
        try {
            $requestFactory = Psr17FactoryDiscovery::findRequestFactory();
            $psr18Client = Psr18ClientDiscovery::find();

            $request = $requestFactory->createRequest(RequestMethodInterface::METHOD_GET, $url);
            $response = $psr18Client->sendRequest($request);

            $content = $response->getBody()->getContents();
        } catch (Throwable $throwable) {
            $this->errors[] = $throwable;
            return;
        }

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

    /**
     * @return array<int, Throwable>
     */
    public function getErrors(): array
    {
        return $this->errors;
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
