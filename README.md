[![PHP from Packagist](https://img.shields.io/packagist/php-v/bitandblack/document-crawler)](https://www.php.net)
[![Latest Stable Version](https://poser.pugx.org/bitandblack/document-crawler/v/stable)](https://packagist.org/packages/bitandblack/document-crawler)
[![Total Downloads](https://poser.pugx.org/bitandblack/document-crawler/downloads)](https://packagist.org/packages/bitandblack/document-crawler)
[![License](https://poser.pugx.org/bitandblack/document-crawler/license)](https://packagist.org/packages/bitandblack/document-crawler)

<p align="center">
    <a href="https://www.bitandblack.com" target="_blank">
        <img src="https://www.bitandblack.com/build/images/BitAndBlack-Logo-Full.png" alt="Bit&Black Logo" width="400">
    </a>
</p>

# Bit&Black Document Crawler

Extract titles, meta tags, images, anchors, headings, canonical URLs, structured data and more from an HTML or XML document.

## Installation

This library is installed via [Composer](https://packagist.org/packages/bitandblack/document-crawler):

```
composer require bitandblack/document-crawler
```

It requires PHP 8.2 or higher.

Downloading resources or using `HolisticDocumentCrawler::createFromUrl()` additionally requires a [PSR-18](https://www.php-fig.org/psr/psr-18/) HTTP client implementation in your project (for example [`symfony/http-client`](https://symfony.com/doc/current/http_client.html)) — the [HttpDiscoveryClient](./src/HttpClient/HttpDiscoveryClient.php) picks up whatever is installed.

## Usage

### Using crawlers to extract parts of a document

The *Bit&Black Document Crawler* library provides different crawlers to extract information from a document. The following crawlers are currently available:

-   [**AnchorsCrawler**](./src/Crawler/AnchorsCrawler.php): Crawl and extract all defined anchors in a document, that have been declared with `<a href="...">...</a>`.
-   [**CanonicalCrawler**](./src/Crawler/CanonicalCrawler.php): Crawl and extract the canonical URL of a document, that has been declared with `<link rel="canonical" href="..." />`.
-   [**HeadingsCrawler**](./src/Crawler/HeadingsCrawler.php): Crawl and extract all headings in a document, that have been declared with `<h1>...</h1>` up to `<h6>...</h6>`.
-   [**IconsCrawler**](./src/Crawler/IconsCrawler.php): Crawl and extract all defined icons in a document, that have been declared with `<link rel="icon" ... />`.
-   [**IframesCrawler**](./src/Crawler/IframesCrawler.php): Crawl and extract all defined iframes in a document, that have been declared with `<iframe ...></iframe>`.
-   [**ImagesCrawler**](./src/Crawler/ImagesCrawler.php): Crawl and extract all defined images in a document, that have been declared with `<img ... />`.
-   [**LanguageCodeCrawler**](./src/Crawler/LanguageCodeCrawler.php): Crawl and extract the language code of a document, that has been declared with `<html lang="...">`.
-   [**LinkTagsCrawler**](./src/Crawler/LinkTagsCrawler.php): Crawl and extract all link tags of a document, that have been declared with `<link ... />`.
-   [**MetaTagsCrawler**](./src/Crawler/MetaTagsCrawler.php): Crawl and extract all defined meta tags in a document, that have been declared with `<meta ... />`.
-   [**StructuredDataCrawler**](./src/Crawler/StructuredDataCrawler.php): Crawl and extract all structured data blocks in a document, that have been declared with `<script type="application/ld+json">...</script>`.
-   [**TitleCrawler**](./src/Crawler/TitleCrawler.php): Crawl and extract the title of a document, that has been declared with `<title>...</title>`.

All those crawlers work the same — they need a [DomCrawler](https://symfony.com/doc/current/components/dom_crawler.html) object, that contains the document:

```php
<?php

use BitAndBlack\DocumentCrawler\Crawler\TitleCrawler;
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

All DTOs implement `JsonSerializable` and `Stringable`, so extracted results can be encoded or cast to string directly.

### Handling resources

In some cases, crawlers process external resources, which you may want to handle in a specific way. To achieve this, each crawler uses a so-called _Resource Handler_. The following resource handlers are currently available:

-   The [FileSystemDownloadHandler](./src/ResourceHandler/FileSystemDownloadHandler.php): This one loads resources and writes them to the file system.
    There are different _Http Clients_ available to fetch resources:

    -   The [HttpDiscoveryClient](./src/HttpClient/HttpDiscoveryClient.php) is the default one and makes use of whatever library your project uses to download resources.
    -   The [ReactClient](./src/HttpClient/ReactClient.php) needs the [`react/http`](https://github.com/reactphp/http) library and downloads resources asynchronously in the background: the downloads run in parallel and the returned download item reflects the final status of the download, once it has finished.
    -   You can — for sure — create a custom _Http Client_ by implementing the [HttpClientInterface](./src/HttpClient/HttpClientInterface.php).

-   The [PassiveResourceHandler](./src/ResourceHandler/PassiveResourceHandler.php): This handler does nothing and is the default one.

You can create a custom _Resource Handler_ by implementing the [ResourceHandlerInterface](./src/ResourceHandler/ResourceHandlerInterface.php).

### Crawling everything at once

In case you don't want to set up every crawler yourself, there is the [HolisticDocumentCrawler](./src/HolisticDocumentCrawler.php), that does all the work for you:

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

// Get the canonical URL:
$canonicalUrl = $holisticDocumentCrawler->getCanonicalUrl();

// Get all headings:
$headings = $holisticDocumentCrawler->getHeadings();

// Get all icons:
$icons = $holisticDocumentCrawler->getIcons();

// Get all iframes:
$iframes = $holisticDocumentCrawler->getIframes();

// Get all images:
$images = $holisticDocumentCrawler->getImages();

// Get the language code:
$languageCode = $holisticDocumentCrawler->getLanguageCode();

// Get all link tags:
$linkTags = $holisticDocumentCrawler->getLinkTags();

// Get all meta tags:
$metaTags = $holisticDocumentCrawler->getMetaTags();

// Get all structured data blocks:
$structuredData = $holisticDocumentCrawler->getStructuredData();

// Get the title:
$title = $holisticDocumentCrawler->getTitle();
```

The `HolisticDocumentCrawler` can also be initialised using the `createFromUrl` method:

```php
<?php

use BitAndBlack\DocumentCrawler\HolisticDocumentCrawler;

$holisticDocumentCrawler = HolisticDocumentCrawler::createFromUrl('https://www.bitandblack.com');
```

Runnable examples can be found in the [examples](./examples) directory. The list of notable changes is documented in the [CHANGELOG.md](./CHANGELOG.md).

## Help

If you have any questions, feel free to contact us at `hello@bitandblack.com`.

Further information about Bit&Black can be found under [www.bitandblack.com](https://www.bitandblack.com).
