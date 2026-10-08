# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

All changes so far are additive — existing classes, constructors and `HolisticDocumentCrawler` wiring keep working.

## [Unreleased]

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

[Unreleased]: https://github.com/BitAndBlack/document-crawler/compare/0.6.0...HEAD
