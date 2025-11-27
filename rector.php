<?php

use Rector\Config\RectorConfig;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector;
use Rector\PHPUnit\Set\PHPUnitSetList;

return RectorConfig::configure()
    ->withParallel()
    ->withPaths([
        __DIR__,
    ])
    ->withSkip([
        __DIR__ . DIRECTORY_SEPARATOR . 'config',
        __DIR__ . DIRECTORY_SEPARATOR . 'node_modules',
        __DIR__ . DIRECTORY_SEPARATOR . 'var',
        __DIR__ . DIRECTORY_SEPARATOR . 'vendor',
        PreferPHPUnitThisCallRector::class,
    ])
    ->withSets([
        PHPUnitSetList::PHPUNIT_100,
        PHPUnitSetList::PHPUNIT_110,
        PHPUnitSetList::PHPUNIT_CODE_QUALITY,
        PHPUnitSetList::ANNOTATIONS_TO_ATTRIBUTES,
    ])
    ->withImportNames()
    ->withPhpSets()
;
