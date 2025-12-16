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

$document = <<<'HTML'
<!doctype html>
<html lang="en">
    <head>
        <title>Example Domain</title>
        <link rel="icon" href="/favicon.ico">
    </head>
    <body>
        <h1>Hello world</h1>
        <a href="/something.html" title="Click here to move to somewhere else">Something</a>
    </body>
</html>
HTML;

$holisticDocumentCrawler = new HolisticDocumentCrawler($document);

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

// Get all anchors:
dump($holisticDocumentCrawler->getAnchors());
