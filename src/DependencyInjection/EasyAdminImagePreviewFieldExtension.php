<?php

declare(strict_types=1);

namespace Tourze\EasyAdminImagePreviewFieldBundle\DependencyInjection;

use Tourze\SymfonyDependencyServiceLoader\AutoExtension;

class EasyAdminImagePreviewFieldExtension extends AutoExtension
{
    protected function getConfigDir(): string
    {
        return __DIR__ . '/../Resources/config';
    }
}
