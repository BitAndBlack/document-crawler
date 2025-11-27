<?php

/**
 * Bit&Black Document Crawler.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

namespace BitAndblack\DocumentCrawler\Exception;

use BitAndblack\DocumentCrawler\Exception;

class MissingDependencyException extends Exception
{
    /**
     * @param class-string $classOrigin
     * @param class-string $classMissing
     * @param string $dependencyMissing
     */
    public function __construct(
        string $classOrigin,
        string $classMissing,
        string $dependencyMissing,
    ) {
        parent::__construct(
            'Failed to use class "' . $classOrigin . '" as class "' . $classMissing . '" is missing. '
            . 'Check your Composer dependencies and maybe add the library "' . $dependencyMissing . '" there.'
        );
    }
}
