<?php

/**
 * Bit&Black Document Crawler.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

namespace BitAndblack\DocumentCrawler\Crawler;

use BitAndblack\DocumentCrawler\ResourceHandler\ResourceHandlerInterface;
use Symfony\Component\DomCrawler\Crawler;

interface CrawlerInterface
{
    public function __construct(Crawler $crawler);

    /**
     * Crawls the given content.
     *
     * @return void
     */
    public function crawlContent(): void;

    /**
     * Adds a resource handler, that does something with the external resource.
     *
     * @param ResourceHandlerInterface $resourceHandler
     * @return self
     */
    public function setResourceHandler(ResourceHandlerInterface $resourceHandler): self;
}
