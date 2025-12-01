<?php

/**
 * Bit&Black Document Crawler.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

namespace BitAndBlack\DocumentCrawler;

use Throwable;

class Exception extends \Exception
{
    public function __construct(string $message, Throwable|null $previous = null)
    {
        parent::__construct($message, previous: $previous);
    }
}
