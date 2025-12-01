<?php

/**
 * Bit&Black Document Crawler.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

use BitAndBlack\DocumentCrawler\HolisticDocumentCrawler;

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$holisticDocumentCrawler = new HolisticDocumentCrawler('https://www.bitandblack.com');

// Get all icons:
dump($holisticDocumentCrawler->getIcons());

// Get all images:
dump($holisticDocumentCrawler->getImages());

// Get the language code:
dump($holisticDocumentCrawler->getLanguageCode());

// Get all meta tags:
dump($holisticDocumentCrawler->getMetaTags());

// Get the title:
dump($holisticDocumentCrawler->getTitle());
