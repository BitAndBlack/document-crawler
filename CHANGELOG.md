# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

All changes so far are additive — existing classes, constructors and `HolisticDocumentCrawler` wiring keep working.

## 0.7.0 – 2026-10-09

### Added

-   `CanonicalCrawler`: Extracts the canonical URL from `<link rel="canonical" href="..." />`.
    Returns the first match or `null` when missing; the `rel` value must equal `canonical` exactly and the URL is
    returned as written (relative URLs are not resolved).
-   `StructuredDataCrawler` and the `StructuredData` DTO: Extracts all `<script type="application/ld+json">...</script>`
    blocks with their raw JSON, decoded payload, parse-level validity flag (`isValidJson()`) and a flattened, deduplicated
    list of schema types (from `@type`, `@graph` and `itemListElement`, recursively).
    Schema validation is intentionally out of scope.
-   `HeadingsCrawler` and the `Heading` DTO: Extracts all `<h1>` … `<h6>` headings in document order, with level and text
    (empty headings keep an empty text), plus per-level counts via `getHeadingCounts()`.
-   `IframesCrawler` and the `Iframe` DTO: Extracts all `<iframe>` elements with `src`, `title`, `width` and `height`.
    Iframes without a `src` attribute are skipped.
-   `Image` DTO: New optional fields `loading`, `width`, `height` and `srcset` with getters; filled by the `ImagesCrawler`
    and included in `jsonSerialize()`. Existing three-argument construction keeps working.
-   `Anchor` DTO: New optional field `rel` with getter (e.g. `nofollow`, `ugc`, `sponsored`); filled by the
    `AnchorsCrawler` and included in `jsonSerialize()`. Existing three-argument construction keeps working.
-   `HolisticDocumentCrawler`: New methods `getCanonicalUrl()`, `getStructuredData()`, `getHeadings()` and `getIframes()`.
-   `AnchorsCrawlerTest`: The anchors crawler is now covered by tests, including the new `rel` extraction.

### Fixed

-   Wrong `TitleCrawler` namespace in the README usage example (`ContentCrawler` → `Crawler`).
-   `ReactClient::download()` reported success (and no errors) before the HTTP request had finished: the status was
    captured in the returned `DownloadItem`, so failures and 404 responses of background downloads were lost. The
    returned item now shares a reference with the running download and reflects its final status, once it has finished.
-   Protocol-relative URLs (`//cdn.example.org/pic.png`) got mangled into local paths by the `PassiveResourceHandler`
    and the `FileSystemDownloadHandler`. They get the scheme of the base URL now (or are kept as written without one).
-   `FileSystemDownloadHandler` classified external resources by string prefix, so `https://hostile.com` counted as
    internal when the base URL was `https://host`. The host gets compared now.
-   `FileSystemDownloadHandler::getErrors()` overwrote the errors of previous downloads. Errors of all downloads get
    collected now.
-   A `<link>` tag without a `rel` attribute crashed the crawl with a `TypeError`. Such tags get skipped now.
-   `AnchorsCrawler::getAnchors()` threw when called before `crawlContent()` and could return a non-list array,
    which broke `json_encode()`. The result gets initialized and re-indexed now.
-   `BaseUrl` dropped the port: `https://example.org:8080/sub/page.html` became `https://example.org`.
-   `ImagesCrawler` silently ignored `webp`, `avif` and `svg` images.
-   `IconsCrawler` matched the `rel` value case-sensitively, so `<link rel="ICON">` was missed.
-   HTTP 400 responses were treated as successful downloads. Both HTTP clients treat status codes `>= 400` as a
    failure now and record an error in `DownloadItem::getErrors()`.
-   Tests hit the live network (`example.org`, `bitandblack.com`). They use a stub HTTP client now and run offline.
-   `phpunit.xml`: The test suite was named "Proxy Scheduling Test Suite" and the coverage source directory pointed
    to `tests/` instead of `src/`.

### Changed

-   `DownloadItem` got an optional `DownloadStatus` constructor argument: a mutable status shared with the HTTP client.
    `ReactClient` downloads in the background and updates that shared status when the download has finished, so the
    returned item reflects the final status (success and errors) of the download — without waiting for it.
-   CI: `composer validate` runs without `--strict` now. The unbounded `*` constraints on
    `psr/http-client-implementation` and `psr/http-factory-implementation` are intentional — they follow the usage guide
    of `php-http/discovery`, which itself provides both virtual packages — and `--strict` treated them as an error.
-   CI: The Composer cache key hashes `composer.json` now instead of `composer.lock`, which is not part of the repository.
-   All files in `src/` declare `strict_types` now.
-   CI: `actions/checkout@v4` and `actions/cache@v4`, the coding style check (ECS) runs in CI now via the new
    `composer ecs` script.
-   `.gitattributes`: `CHANGELOG.md` gets excluded from dist archives now; stale entries
    (`bitbucket-pipelines.yml`, `devTools`) got removed.
-   Examples print JSON instead of using `dump()`, so they can be copied into projects without
    `symfony/var-dumper`.