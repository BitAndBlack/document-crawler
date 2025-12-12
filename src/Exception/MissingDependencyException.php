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

class MissingDependencyException extends Exception
{
    /**
     * @param class-string $classOrigin
     * @param string $dependencyMissing
     */
    public function __construct(string $classOrigin, string $dependencyMissing)
    {
        parent::__construct(
            'Failed to use class "' . $classOrigin . '" as external dependencies from library "' . $dependencyMissing . '" are missing. '
            . 'Check your Composer dependencies and maybe add the library by running `$ composer require ' . $dependencyMissing . '` there.'
        );
    }
}
