<?php

/**
 * Bit&Black Document Crawler.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

namespace BitAndBlack\DocumentCrawler\Util;

use JsonSerializable;
use Stringable;

readonly class Url implements Stringable, JsonSerializable
{
    public function __construct(
        private string|null $scheme,
        private string|null $host,
        private int|null $port,
        private string|null $user,
        private string|null $pass,
        private string|null $query,
        private string|null $path,
        private string|null $fragment,
    ) {
    }

    public function __toString(): string
    {
        return $this->getUrl();
    }

    /**
     * @return array{
     *     scheme: string|null,
     *     host: string|null,
     *     port: int|null,
     *     user: string|null,
     *     pass: string|null,
     *     query: string|null,
     *     path: string|null,
     *     fragment: string|null,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'scheme' => $this->getScheme(),
            'host' => $this->getHost(),
            'port' => $this->getPort(),
            'user' => $this->getUser(),
            'pass' => $this->getPass(),
            'query' => $this->getQuery(),
            'path' => $this->getPath(),
            'fragment' => $this->getFragment(),
        ];
    }

    public function getUrl(): string
    {
        $scheme = $this->getScheme() !== null ? $this->getScheme() . '://' : null;

        $host = $this->getHost();

        $port = $this->getPort() !== null ? ':' . $this->getPort() : null;

        $user = $this->getUser();

        $pass = $this->getPass() !== null ? ':' . $this->getPass() : null;

        $pass = ($user || $pass) ? $pass . '@' : '';

        $path = $this->getPath();

        $query = $this->getQuery() !== null ? '?' . $this->getQuery() : null;

        $fragment = $this->getFragment() !== null ? '#' . $this->getFragment() : null;

        return $scheme . $user . $pass . $host . $port . $path . $query . $fragment;
    }

    public function getScheme(): string|null
    {
        return $this->scheme;
    }

    public function getHost(): string|null
    {
        return $this->host;
    }

    public function getPort(): int|null
    {
        return $this->port;
    }

    public function getUser(): string|null
    {
        return $this->user;
    }

    public function getPass(): string|null
    {
        return $this->pass;
    }

    public function getQuery(): string|null
    {
        return $this->query;
    }

    public function withQuery(string|null $query): self
    {
        return new self(
            $this->getScheme(),
            $this->getHost(),
            $this->getPort(),
            $this->getUser(),
            $this->getPass(),
            $query,
            $this->getPath(),
            $this->getFragment(),
        );
    }

    public function getPath(): string|null
    {
        return $this->path;
    }

    public function getFragment(): string|null
    {
        return $this->fragment;
    }


    public function withFragment(string|null $fragment): self
    {
        return new self(
            $this->getScheme(),
            $this->getHost(),
            $this->getPort(),
            $this->getUser(),
            $this->getPass(),
            $this->getQuery(),
            $this->getPath(),
            $fragment,
        );
    }
}
