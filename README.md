[![PHP from Packagist](https://img.shields.io/packagist/php-v/bitandblack/document-crawler)](http://www.php.net)
[![Latest Stable Version](https://poser.pugx.org/bitandblack/document-crawler/v/stable)](https://packagist.org/packages/bitandblack/document-crawler)
[![Total Downloads](https://poser.pugx.org/bitandblack/document-crawler/downloads)](https://packagist.org/packages/bitandblack/document-crawler)
[![License](https://poser.pugx.org/bitandblack/document-crawler/license)](https://packagist.org/packages/bitandblack/document-crawler)

<p align="center">
    <a href="https://www.bitandblack.com" target="_blank">
        <img src="https://www.bitandblack.com/build/images/BitAndBlack-Logo-Full.png" alt="Bit&Black Logo" width="400">
    </a>
</p>

# Bit&Black Document Crawler

Extract different parts of an HTML or XML document.

## Installation

This library is made for the use with [Composer](https://packagist.org/packages/bitandblack/document-crawler). Add it to your project by running `$ composer require bitandblack/document-crawler`.

## Usage 

### Using Crawlers to extract parts of a document 

The *Bit&Black Document Crawler* library provides different crawlers, to extract information of a document. There are currently existing:

-   [**AnchorsCrawler**](./src/Crawler/AnchorsCrawler.php): Crawl and extract all defined anchors in a document, that have been declared with `<a href="...">...</a>`.
-   [**IconsCrawler**](./src/Crawler/IconsCrawler.php): Crawl and extract all defined icons in a document, that have been declared with `<link rel="icon" ... />`.
-   [**ImagesCrawler**](./src/Crawler/ImagesCrawler.php): Crawl and extract all defined images in a document, that have been declared with `<img ... />`.
-   [**LanguageCodeCrawler**](./src/Crawler/LanguageCodeCrawler.php): Crawl and extract the language code of a document, that has been declared with `<html lang="...">`.
-   [**MetaTagsCrawler**](./src/Crawler/MetaTagsCrawler.php): Crawl and extract all defined meta tags in a document, that have been declared with `<meta ... />`.
-   [**TitleCrawler**](./src/Crawler/TitleCrawler.php): Crawl and extract the title of a document, that has been declared with `<title>...</title>`.

All those crawlers work the same — they need a [DomCrawler](https://symfony.com/doc/current/components/dom_crawler.html) object, that contains the document:

```php
<?php

use BitAndBlack\DocumentCrawler\ContentCrawler\TitleCrawler;
use Symfony\Component\DomCrawler\Crawler;

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
echo $titleCrawler->getTitle();
```

You can create a custom _Crawler_ by implementing the [CrawlerInterface](./src/Crawler/CrawlerInterface.php).

### Handling resources

In same cases, resources are getting crawled, which you may want to handle in a specific way. To achieve this, each crawler makes use of a so-called _Resource Handler_. There are currently existing:

-   The [FileSystemDownloadHandler](./src/ResourceHandler/FileSystemDownloadHandler.php): This one loads resources and writes them to the file system.
    There are different _Http Clients_ available to fetch resources:

    -   The [HttpDiscoveryClient](./src/HttpClient/HttpDiscoveryClient.php) is the default one and makes use of whatever library your project uses to download resources.
    -   The [ReactClient](./src/HttpClient/ReactClient.php) needs the [`react/http`](https://github.com/reactphp/http) library and fetches resources asynchronously.
    -   You can — for sure — create a custom _Http Client_ by implementing the [HttpClientInterface](./src/HttpClient/HttpClientInterface.php).

-   The [PassiveResourceHandler](./src/ResourceHandler/PassiveResourceHandler.php): This handler does nothing and is the default one.

You can create a custom _Resource Handler_ by implementing the [ResourceHandlerInterface](./src/ResourceHandler/ResourceHandlerInterface.php).

### Crawling everything at once

In case you don't want to set up something, there is the [HolisticDocumentCrawler](./src/HolisticDocumentCrawler.php), that does all the work for you:

```php
<?php

use BitAndBlack\DocumentCrawler\HolisticDocumentCrawler;

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

$holisticDocumentCrawler = new HolisticDocumentCrawler($document);

// Get all anchors:
$anchors = $holisticDocumentCrawler->getAnchors();

// Get all icons:
$icons = $holisticDocumentCrawler->getIcons();

// Get all images:
$images = $holisticDocumentCrawler->getImages();

// Get the language code:
$languageCode = $holisticDocumentCrawler->getLanguageCode();

// Get all meta tags:
$metaTags = $holisticDocumentCrawler->getMetaTags();

// Get the title:
$title = $holisticDocumentCrawler->getTitle();
```

The `HolisticDocumentCrawler` can also be initialised using the `createFromUrl` method:

```php
<?php

use BitAndBlack\DocumentCrawler\HolisticDocumentCrawler;

$holisticDocumentCrawler = HolisticDocumentCrawler::createFromUrl('https://www.bitandblack.com');
```

## Help

If you have any questions, feel free to contact us under `hello@bitandblack.com`.

Further information about Bit&Black can be found under [www.bitandblack.com](https://www.bitandblack.com).
