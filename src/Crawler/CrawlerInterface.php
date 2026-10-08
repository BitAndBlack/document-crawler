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

namespace BitAndBlack\DocumentCrawler\Crawler;

use BitAndBlack\DocumentCrawler\ResourceHandler\ResourceHandlerInterface;
use Symfony\Component\DomCrawler\Crawler;

interface CrawlerInterface
{
    public function __construct(Crawler $crawler);

    /**
     * Crawls the given content.
     */
    public function crawlContent(): void;

    /**
     * Adds a resource handler, that does something with the external resource.
     */
    public function setResourceHandler(ResourceHandlerInterface $resourceHandler): self;
}
