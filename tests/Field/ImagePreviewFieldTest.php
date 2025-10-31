<?php

declare(strict_types=1);

namespace Tourze\EasyAdminImagePreviewFieldBundle\Tests\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Tourze\EasyAdminImagePreviewFieldBundle\Field\ImagePreviewField;

/**
 * @internal
 */
#[CoversClass(ImagePreviewField::class)]
final class ImagePreviewFieldTest extends TestCase
{
    public function testNewShouldCreateFieldWithCorrectProperties(): void
    {
        $field = ImagePreviewField::new('image', 'Image Preview');

        $this->assertInstanceOf(FieldInterface::class, $field);
        $this->assertEquals('image', $field->getAsDto()->getProperty());
        $this->assertEquals('Image Preview', $field->getAsDto()->getLabel());
        $this->assertEquals('crud/field/text', $field->getAsDto()->getTemplateName());
        $this->assertEquals(TextType::class, $field->getAsDto()->getFormType());
        $this->assertStringContainsString('field-image-preview', $field->getAsDto()->getCssClass());
    }

    public function testNewWithoutLabelShouldUseNullLabel(): void
    {
        $field = ImagePreviewField::new('image');

        $this->assertEquals('image', $field->getAsDto()->getProperty());
        $this->assertNull($field->getAsDto()->getLabel());
    }

    public function testFormatImageValueWithStringUrlShouldReturnFormattedHtml(): void
    {
        // 创建一个模拟实体来测试格式化
        $entity = new class {
            public function getWidth(): int
            {
                return 100;
            }

            public function getHeight(): int
            {
                return 80;
            }

            public function getFileName(): string
            {
                return 'test.jpg';
            }
        };

        // 使用反射调用私有的静态方法
        $reflection = new \ReflectionClass(ImagePreviewField::class);
        $method = $reflection->getMethod('formatImageValue');
        $method->setAccessible(true);

        $result = $method->invokeArgs(null, ['https://example.com/image.jpg', $entity]);

        $this->assertIsString($result);
        $this->assertStringContainsString('<div class="image-preview-field"', $result);
        $this->assertStringContainsString('https://example.com/image.jpg', $result);
        $this->assertStringContainsString('onclick="showImageModal', $result);
        $this->assertStringContainsString('<script>', $result);
        $this->assertStringContainsString('class="thumbnail"', $result);
        $this->assertStringContainsString('class="large-preview"', $result);
    }

    public function testFormatImageValueWithEmptyValueShouldReturnNoText(): void
    {
        $entity = new \stdClass();

        // 使用反射调用私有的静态方法
        $reflection = new \ReflectionClass(ImagePreviewField::class);
        $method = $reflection->getMethod('formatImageValue');
        $method->setAccessible(true);

        $result = $method->invokeArgs(null, ['', $entity]);

        $this->assertEquals('无', $result);
    }

    public function testFormatImageValueWithNullValueShouldReturnNoText(): void
    {
        $entity = new \stdClass();

        // 使用反射调用私有的静态方法
        $reflection = new \ReflectionClass(ImagePreviewField::class);
        $method = $reflection->getMethod('formatImageValue');
        $method->setAccessible(true);

        $result = $method->invokeArgs(null, [null, $entity]);

        $this->assertEquals('无', $result);
    }

    public function testFormatImageValueWithFileObjectShouldUseObjectProperties(): void
    {
        // 创建文件对象模拟
        $fileEntity = new class {
            public function getUrl(): string
            {
                return 'https://example.com/file.jpg';
            }

            public function getWidth(): int
            {
                return 200;
            }

            public function getHeight(): int
            {
                return 150;
            }

            public function getFileName(): string
            {
                return 'file.jpg';
            }
        };

        $entity = new \stdClass();

        // 使用反射调用私有的静态方法
        $reflection = new \ReflectionClass(ImagePreviewField::class);
        $method = $reflection->getMethod('formatImageValue');
        $method->setAccessible(true);

        $result = $method->invokeArgs(null, [$fileEntity, $entity]);

        $this->assertIsString($result);
        $this->assertStringContainsString('<div class="image-preview-field"', $result);
        $this->assertStringContainsString('https://example.com/file.jpg', $result);
        $this->assertStringContainsString('file.jpg', $result);
        $this->assertStringContainsString('class="thumbnail"', $result);
    }

    public function testFormatImageValueWithFileObjectWithoutUrlShouldReturnNoImageText(): void
    {
        // 创建没有URL的文件对象
        $fileEntity = new class {};

        $entity = new \stdClass();

        // 使用反射调用私有的静态方法
        $reflection = new \ReflectionClass(ImagePreviewField::class);
        $method = $reflection->getMethod('formatImageValue');
        $method->setAccessible(true);

        $result = $method->invokeArgs(null, [$fileEntity, $entity]);

        $this->assertEquals('无图片', $result);
    }

    public function testFormatImageValueWithoutDimensionsShouldReturnSimpleLink(): void
    {
        // 没有宽高属性的实体
        $entity = new \stdClass();

        // 使用反射调用私有的静态方法
        $reflection = new \ReflectionClass(ImagePreviewField::class);
        $method = $reflection->getMethod('formatImageValue');
        $method->setAccessible(true);

        $result = $method->invokeArgs(null, ['https://example.com/image.jpg', $entity]);

        $this->assertIsString($result);
        $this->assertStringContainsString('<a href="https://example.com/image.jpg"', $result);
        $this->assertStringContainsString('target="_blank"', $result);
        $this->assertStringContainsString('image.jpg', $result);
        $this->assertStringNotContainsString('<div class="image-preview-field"', $result);
    }

    public function testGetEntityPropertyWithGetterMethodShouldReturnValue(): void
    {
        $entity = new class {
            public function getTestProperty(): string
            {
                return 'test_value';
            }
        };

        // 使用反射测试私有方法
        $reflection = new \ReflectionClass(ImagePreviewField::class);
        $method = $reflection->getMethod('getEntityProperty');
        $method->setAccessible(true);

        $result = $method->invokeArgs(null, [$entity, 'testProperty']);

        $this->assertEquals('test_value', $result);
    }

    public function testGetEntityPropertyWithPublicPropertyShouldReturnValue(): void
    {
        $entity = new class {
            public string $testProperty = 'property_value';
        };

        // 使用反射测试私有方法
        $reflection = new \ReflectionClass(ImagePreviewField::class);
        $method = $reflection->getMethod('getEntityProperty');
        $method->setAccessible(true);

        $result = $method->invokeArgs(null, [$entity, 'testProperty']);

        $this->assertEquals('property_value', $result);
    }

    public function testGetEntityPropertyWithIsMethodShouldReturnValue(): void
    {
        $entity = new class {
            public function isActive(): bool
            {
                return true;
            }
        };

        // 使用反射测试私有方法
        $reflection = new \ReflectionClass(ImagePreviewField::class);
        $method = $reflection->getMethod('getEntityProperty');
        $method->setAccessible(true);

        $result = $method->invokeArgs(null, [$entity, 'active']);

        $this->assertTrue($result);
    }

    public function testGetEntityPropertyWithNonObjectShouldReturnNull(): void
    {
        // 使用反射测试私有方法
        $reflection = new \ReflectionClass(ImagePreviewField::class);
        $method = $reflection->getMethod('getEntityProperty');
        $method->setAccessible(true);

        $result = $method->invokeArgs(null, ['not_an_object', 'testProperty']);

        $this->assertNull($result);
    }

    public function testGetEntityPropertyWithNonExistentPropertyShouldReturnNull(): void
    {
        $entity = new \stdClass();

        // 使用反射测试私有方法
        $reflection = new \ReflectionClass(ImagePreviewField::class);
        $method = $reflection->getMethod('getEntityProperty');
        $method->setAccessible(true);

        $result = $method->invokeArgs(null, [$entity, 'nonExistentProperty']);

        $this->assertNull($result);
    }

    public function testGetValueByMethodShouldFindCorrectMethod(): void
    {
        $entity = new class {
            public function getTestValue(): string
            {
                return 'method_value';
            }
        };

        $reflection = new \ReflectionClass($entity);

        // 使用反射测试私有方法
        $classReflection = new \ReflectionClass(ImagePreviewField::class);
        $method = $classReflection->getMethod('getValueByMethod');
        $method->setAccessible(true);

        $result = $method->invokeArgs(null, [$reflection, $entity, 'testValue']);

        $this->assertEquals('method_value', $result);
    }

    public function testGetValueByPropertyShouldFindPublicProperty(): void
    {
        $entity = new class {
            public string $publicProperty = 'property_value';
        };

        $reflection = new \ReflectionClass($entity);

        // 使用反射测试私有方法
        $classReflection = new \ReflectionClass(ImagePreviewField::class);
        $method = $classReflection->getMethod('getValueByProperty');
        $method->setAccessible(true);

        $result = $method->invokeArgs(null, [$reflection, $entity, 'publicProperty']);

        $this->assertEquals('property_value', $result);
    }

    public function testFormatImageValueWithSpecialCharactersShouldEscapeHtml(): void
    {
        $entity = new class {
            public function getWidth(): int
            {
                return 100;
            }

            public function getHeight(): int
            {
                return 80;
            }

            public function getFileName(): string
            {
                return 'test<script>.jpg';
            }
        };

        // 使用反射调用私有的静态方法
        $reflection = new \ReflectionClass(ImagePreviewField::class);
        $method = $reflection->getMethod('formatImageValue');
        $method->setAccessible(true);

        $result = $method->invokeArgs(null, ['https://example.com/test<script>.jpg', $entity]);

        $this->assertIsString($result);
        $this->assertStringContainsString('&lt;script&gt;', $result);
        // 检查HTML转义是否正确
        $this->assertStringContainsString('data-file-name="test&lt;script&gt;.jpg"', $result);
    }

    public function testFormatValueMethodShouldReturnFieldInstance(): void
    {
        $field = ImagePreviewField::new('image');
        $result = $field->formatValue(function ($value, $entity) {
            return 'formatted: ' . $value;
        });

        $this->assertInstanceOf(ImagePreviewField::class, $result);
        $this->assertSame($field, $result);
    }

    public function testAddCssClassShouldReturnFieldInstance(): void
    {
        $field = ImagePreviewField::new('image');
        $result = $field->addCssClass('custom-class');

        $this->assertInstanceOf(ImagePreviewField::class, $result);
        $this->assertSame($field, $result);
        $this->assertStringContainsString('custom-class', $field->getAsDto()->getCssClass());
    }

    public function testHideOnDetailShouldReturnFieldInstance(): void
    {
        $field = ImagePreviewField::new('image');
        $result = $field->hideOnDetail();

        $this->assertInstanceOf(ImagePreviewField::class, $result);
        $this->assertSame($field, $result);
    }

    public function testHideOnFormShouldReturnFieldInstance(): void
    {
        $field = ImagePreviewField::new('image');
        $result = $field->hideOnForm();

        $this->assertInstanceOf(ImagePreviewField::class, $result);
        $this->assertSame($field, $result);
    }

    public function testHideOnIndexShouldReturnFieldInstance(): void
    {
        $field = ImagePreviewField::new('image');
        $result = $field->hideOnIndex();

        $this->assertInstanceOf(ImagePreviewField::class, $result);
        $this->assertSame($field, $result);
    }

    public function testHideWhenCreatingShouldReturnFieldInstance(): void
    {
        $field = ImagePreviewField::new('image');
        $result = $field->hideWhenCreating();

        $this->assertInstanceOf(ImagePreviewField::class, $result);
        $this->assertSame($field, $result);
    }

    public function testHideWhenUpdatingShouldReturnFieldInstance(): void
    {
        $field = ImagePreviewField::new('image');
        $result = $field->hideWhenUpdating();

        $this->assertInstanceOf(ImagePreviewField::class, $result);
        $this->assertSame($field, $result);
    }

    public function testOnlyOnDetailShouldReturnFieldInstance(): void
    {
        $field = ImagePreviewField::new('image');
        $result = $field->onlyOnDetail();

        $this->assertInstanceOf(ImagePreviewField::class, $result);
        $this->assertSame($field, $result);
    }

    public function testOnlyOnFormsShouldReturnFieldInstance(): void
    {
        $field = ImagePreviewField::new('image');
        $result = $field->onlyOnForms();

        $this->assertInstanceOf(ImagePreviewField::class, $result);
        $this->assertSame($field, $result);
    }

    public function testOnlyOnIndexShouldReturnFieldInstance(): void
    {
        $field = ImagePreviewField::new('image');
        $result = $field->onlyOnIndex();

        $this->assertInstanceOf(ImagePreviewField::class, $result);
        $this->assertSame($field, $result);
    }

    public function testOnlyWhenCreatingShouldReturnFieldInstance(): void
    {
        $field = ImagePreviewField::new('image');
        $result = $field->onlyWhenCreating();

        $this->assertInstanceOf(ImagePreviewField::class, $result);
        $this->assertSame($field, $result);
    }

    public function testOnlyWhenUpdatingShouldReturnFieldInstance(): void
    {
        $field = ImagePreviewField::new('image');
        $result = $field->onlyWhenUpdating();

        $this->assertInstanceOf(ImagePreviewField::class, $result);
        $this->assertSame($field, $result);
    }

    public function testAddCssFilesShouldReturnFieldInstance(): void
    {
        $field = ImagePreviewField::new('image');
        $result = $field->addCssFiles('custom.css', 'another.css');

        $this->assertInstanceOf(ImagePreviewField::class, $result);
        $this->assertSame($field, $result);
    }

    public function testAddJsFilesShouldReturnFieldInstance(): void
    {
        $field = ImagePreviewField::new('image');
        $result = $field->addJsFiles('custom.js', 'another.js');

        $this->assertInstanceOf(ImagePreviewField::class, $result);
        $this->assertSame($field, $result);
    }

    public function testAddFormThemeShouldReturnFieldInstance(): void
    {
        $field = ImagePreviewField::new('image');
        $result = $field->addFormTheme('custom_theme.html.twig');

        $this->assertInstanceOf(ImagePreviewField::class, $result);
        $this->assertSame($field, $result);
    }

    public function testAddHtmlContentsToBodyShouldReturnFieldInstance(): void
    {
        $field = ImagePreviewField::new('image');
        $result = $field->addHtmlContentsToBody('<script>test</script>');

        $this->assertInstanceOf(ImagePreviewField::class, $result);
        $this->assertSame($field, $result);
    }

    public function testAddHtmlContentsToHeadShouldReturnFieldInstance(): void
    {
        $field = ImagePreviewField::new('image');
        $result = $field->addHtmlContentsToHead('<meta name="test">');

        $this->assertInstanceOf(ImagePreviewField::class, $result);
        $this->assertSame($field, $result);
    }

    public function testAddAssetMapperEntriesShouldReturnFieldInstance(): void
    {
        $field = ImagePreviewField::new('image');
        $result = $field->addAssetMapperEntries('test.js', 'another.js');

        $this->assertInstanceOf(ImagePreviewField::class, $result);
        $this->assertSame($field, $result);
    }

    public function testAddWebpackEncoreEntriesShouldThrowRuntimeException(): void
    {
        $field = ImagePreviewField::new('image');

        // 由于 Webpack Encore 未安装，这个方法会抛出 RuntimeException
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('You are trying to add Webpack Encore entries in a field but Webpack Encore is not installed in your project');

        $field->addWebpackEncoreEntries('app', 'admin');
    }
}
