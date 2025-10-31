<?php

declare(strict_types=1);

namespace Tourze\EasyAdminImagePreviewFieldBundle;

use EasyCorp\Bundle\EasyAdminBundle\EasyAdminBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;

class EasyAdminImagePreviewFieldBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            EasyAdminBundle::class => ['all' => true],
            TwigBundle::class => ['all' => true],
        ];
    }
}
