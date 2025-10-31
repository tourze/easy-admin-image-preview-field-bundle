<?php

namespace Tourze\EasyAdminImagePreviewFieldBundle\Tests;

use EasyCorp\Bundle\EasyAdminBundle\EasyAdminBundle;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\EasyAdminImagePreviewFieldBundle\EasyAdminImagePreviewFieldBundle;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[RunTestsInSeparateProcesses]
#[CoversClass(EasyAdminImagePreviewFieldBundle::class)]
final class EasyAdminImagePreviewFieldBundleTest extends AbstractBundleTestCase
{
    public function testBundleDependenciesShouldReturnCorrectDependencies(): void
    {
        $dependencies = EasyAdminImagePreviewFieldBundle::getBundleDependencies();

        $this->assertIsArray($dependencies);
        $this->assertCount(2, $dependencies);
        $this->assertArrayHasKey(EasyAdminBundle::class, $dependencies);
        $this->assertArrayHasKey(TwigBundle::class, $dependencies);
        $this->assertEquals(['all' => true], $dependencies[EasyAdminBundle::class]);
        $this->assertEquals(['all' => true], $dependencies[TwigBundle::class]);
    }

    public function testBundleImplementsBundleDependencyInterface(): void
    {
        // @phpstan-ignore-next-line Bundle类通常不作为服务注册，直接实例化是合适的
        $bundle = new EasyAdminImagePreviewFieldBundle();
        $this->assertInstanceOf(BundleDependencyInterface::class, $bundle);
    }

    // Bundle instantiation test is handled by the base class
}
