<?php

/**
 * Bit&Black Document Crawler.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

use BitAndblack\DocumentCrawler\Crawler\TitleCrawler;
use Symfony\Component\DomCrawler\Crawler;

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$document = <<<HTML
<!doctype html>
<html lang="en">
    <head>
        <title>Test</title>
    </head>
    <body>
        <h1>Hello world</h1>
    </body>
</html>
HTML;

$crawler = new Crawler($document);

$titleCrawler = new TitleCrawler($crawler);
$titleCrawler->crawlContent();

// This will output `Test`.
dump($titleCrawler->getTitle());
