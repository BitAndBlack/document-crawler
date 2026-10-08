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

use BitAndBlack\DocumentCrawler\HolisticDocumentCrawler;

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';


$holisticDocumentCrawler = HolisticDocumentCrawler::createFromUrl('https://www.bitandblack.com/de/impressum.html');

// Get all links:
echo json_encode($holisticDocumentCrawler->getLinkTags(), JSON_PRETTY_PRINT) . PHP_EOL;

// Get all icons:
echo json_encode($holisticDocumentCrawler->getIcons(), JSON_PRETTY_PRINT) . PHP_EOL;

// Get all images:
echo json_encode($holisticDocumentCrawler->getImages(), JSON_PRETTY_PRINT) . PHP_EOL;

// Get the language code:
echo json_encode($holisticDocumentCrawler->getLanguageCode(), JSON_PRETTY_PRINT) . PHP_EOL;

// Get all meta tags:
echo json_encode($holisticDocumentCrawler->getMetaTags(), JSON_PRETTY_PRINT) . PHP_EOL;

// Get the title:
echo json_encode($holisticDocumentCrawler->getTitle(), JSON_PRETTY_PRINT) . PHP_EOL;

// Get all anchors:
echo json_encode($holisticDocumentCrawler->getAnchors(), JSON_PRETTY_PRINT) . PHP_EOL;

// Get the canonical URL:
echo json_encode($holisticDocumentCrawler->getCanonicalUrl(), JSON_PRETTY_PRINT) . PHP_EOL;

// Get all headings:
echo json_encode($holisticDocumentCrawler->getHeadings(), JSON_PRETTY_PRINT) . PHP_EOL;

// Get all iframes:
echo json_encode($holisticDocumentCrawler->getIframes(), JSON_PRETTY_PRINT) . PHP_EOL;

// Get all structured data blocks:
echo json_encode($holisticDocumentCrawler->getStructuredData(), JSON_PRETTY_PRINT) . PHP_EOL;
