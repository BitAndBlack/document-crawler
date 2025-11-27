<?php

/**
 * Bit&Black Document Crawler.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

namespace BitAndblack\DocumentCrawler;

class Exception extends \Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
