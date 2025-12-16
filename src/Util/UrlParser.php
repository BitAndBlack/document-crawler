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

class UrlParser
{
    /**
     * This method parses a URL and encodes its parts.
     *
     * @param string $url
     * @return Url
     */
    public static function parse(string $url): Url
    {
        /** @var array{
         *     scheme: ?string,
         *     host: ?string,
         *     port: ?int,
         *     user: ?string,
         *     pass: ?string,
         *     query: ?string,
         *     path: ?string,
         *     fragment: ?string
         * } $result
         */
        $result = [
            'scheme' => null,
            'host' => null,
            'port' => null,
            'user' => null,
            'pass' => null,
            'query' => null,
            'path' => null,
            'fragment' => null,
        ];

        $replacements = ['!', '*', '\'', '(', ')', ';', ':', '@', '&', '=', '$', ',', '/', '?', '#', '[', ']'];
        $entities = ['%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%24', '%2C', '%2F', '%3F', '%23', '%5B', '%5D'];

        $encodedURL = str_replace($entities, $replacements, urlencode($url));
        $encodedParts = parse_url($encodedURL);

        if (false === is_array($encodedParts)) {
            return new Url(...$result);
        }

        foreach ($encodedParts as $key => $value) {
            $value = str_replace($replacements, $entities, (string) $value);
            $value = urldecode($value);
            $value = rawurldecode($value);
            $result[$key] = $value;
        }

        if (null !== $result['port']) {
            $result['port'] = (int) $result['port'];
        }

        return new Url(...$result);
    }
}
