<?php

namespace Tourze\EasyAdminImagePreviewFieldBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\EasyAdminImagePreviewFieldBundle\DependencyInjection\EasyAdminImagePreviewFieldExtension;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;
use Tourze\SymfonyDependencyServiceLoader\AutoExtension;

/**
 * @internal
 */
#[CoversClass(EasyAdminImagePreviewFieldExtension::class)]
final class EasyAdminImagePreviewFieldExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    protected function provideServiceDirectories(): iterable
    {
        yield from parent::provideServiceDirectories();
        // Field services are currently commented out in services.yaml
        // yield 'Field';
    }

    public function testGetConfigDirShouldReturnCorrectPath(): void
    {
        $extension = new EasyAdminImagePreviewFieldExtension();

        // 使用反射访问受保护的方法
        $reflection = new \ReflectionClass($extension);
        $method = $reflection->getMethod('getConfigDir');
        $method->setAccessible(true);

        $configDir = $method->invoke($extension);

        $this->assertStringContainsString('/../Resources/config', $configDir);
        $this->assertIsString($configDir);
    }

    public function testExtensionShouldExtendAutoExtension(): void
    {
        $extension = new EasyAdminImagePreviewFieldExtension();
        $this->assertInstanceOf(AutoExtension::class, $extension);
    }
}
