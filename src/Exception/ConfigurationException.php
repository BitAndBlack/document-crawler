<?php

/**
 * Bit&Black Document Crawler.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

namespace BitAndBlack\DocumentCrawler\Exception;

use BitAndBlack\DocumentCrawler\Exception;

class ConfigurationException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
