<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Php81\Rector\Property\ReadOnlyPropertyRector;
use Rector\Php82\Rector\Class_\ReadOnlyClassRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/application/controllers',
        __DIR__ . '/application/models',
        __DIR__ . '/application/libraries',
        __DIR__ . '/application/helpers',
    ])
    ->withBootstrapFiles([
        __DIR__ . '/rector-bootstrap.php',
    ])
    ->withPhpSets(php84: true)
    ->withSkip([
        ClassPropertyAssignToConstructorPromotionRector::class,
        ReadOnlyPropertyRector::class,
        ReadOnlyClassRector::class,
    ]);
