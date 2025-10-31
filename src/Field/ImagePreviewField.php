<?php

declare(strict_types=1);

namespace Tourze\EasyAdminImagePreviewFieldBundle\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class ImagePreviewField implements FieldInterface
{
    use FieldTrait;

    public static function new(string $propertyName, ?string $label = null): self
    {
        $field = (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setTemplateName('crud/field/text')
            ->setFormType(TextType::class)
            ->addCssClass('field-image-preview')
        ;

        $field->formatValue(function ($value, $entity) {
            return self::formatImageValue($value, $entity);
        });

        return $field;
    }

    private static function formatImageValue(mixed $value, mixed $entity): string
    {
        // 如果 value 是关联对象（如 File），从中获取URL和其他属性
        if (is_object($value)) {
            $fileEntity = $value;
            $url = self::getEntityProperty($fileEntity, 'url');
            if (null === $url || '' === $url) {
                return '无图片';
            }
            $width = self::getEntityProperty($fileEntity, 'width');
            $height = self::getEntityProperty($fileEntity, 'height');
            $fileName = self::getEntityProperty($fileEntity, 'fileName') ?? 'image';
        } else {
            // 原有逻辑：value 是 URL 字符串
            if (null === $value || '' === $value) {
                return '无';
            }
            $url = $value;
            $width = self::getEntityProperty($entity, 'width');
            $height = self::getEntityProperty($entity, 'height');
            $fileName = self::getEntityProperty($entity, 'fileName') ?? 'image';
        }

        if (null === $width || '' === $width || null === $height || '' === $height) {
            return sprintf('<a href="%s" target="_blank">%s</a>', $url, basename($url));
        }

        // 使用响应式设计：根据容器和页面类型自动调整显示方式
        // 通过CSS选择器判断当前页面类型，避免使用$_GET超全局变量
        return sprintf(
            '<div class="image-preview-field" data-image-url="%s" data-file-name="%s">
                <!-- 索引页显示：小缩略图 -->
                <div class="index-view">
                    <img src="%s" alt="%s" class="thumbnail" onclick="showImageModal(\'%s\', \'%s\')" title="点击预览图片" />
                </div>
                <!-- 详情页显示：大图预览 -->
                <div class="detail-view">
                    <img src="%s" alt="%s" class="large-preview" />
                    <a href="%s" target="_blank" class="open-link">在新窗口打开</a>
                </div>
            </div>
            <style>
                /* 默认显示索引视图 */
                .image-preview-field .index-view {
                    display: block;
                }
                .image-preview-field .detail-view {
                    display: none;
                }
                
                .image-preview-field .thumbnail {
                    max-width: 100px; 
                    max-height: 60px; 
                    object-fit: cover; 
                    border-radius: 4px; 
                    border: 1px solid #ddd; 
                    cursor: pointer;
                }
                
                .image-preview-field .large-preview {
                    max-width: 300px; 
                    max-height: 200px; 
                    border-radius: 8px; 
                    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                    margin-bottom: 10px;
                    display: block;
                }
                
                .image-preview-field .open-link {
                    margin-top: 10px; 
                    display: inline-block;
                }
                
                /* 在详情页面显示大图 - 通过EasyAdmin的页面CSS类名识别 */
                .ea-detail .image-preview-field .index-view,
                .content-panel-detail .image-preview-field .index-view,
                .page-detail .image-preview-field .index-view {
                    display: none;
                }
                
                .ea-detail .image-preview-field .detail-view,
                .content-panel-detail .image-preview-field .detail-view,
                .page-detail .image-preview-field .detail-view {
                    display: block;
                }
            </style>
            <script>
                if (!window.imageModalScriptLoaded) { 
                    window.imageModalScriptLoaded = true; 
                    function showImageModal(src, title) { 
                        if (document.getElementById("imageModal")) { 
                            document.getElementById("imageModal").remove(); 
                        } 
                        const modal = document.createElement("div"); 
                        modal.id = "imageModal"; 
                        modal.style.cssText = "position: fixed; top: 0; left: 0; width: 100%%; height: 100%%; background: rgba(0,0,0,0.8); z-index: 9999; display: flex; align-items: center; justify-content: center; cursor: pointer;"; 
                        const img = document.createElement("img"); 
                        img.src = src; 
                        img.alt = title; 
                        img.style.cssText = "max-width: 90%%; max-height: 90%%; object-fit: contain; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.5);"; 
                        modal.appendChild(img); 
                        modal.onclick = () => modal.remove(); 
                        document.body.appendChild(modal); 
                        document.addEventListener("keydown", function closeOnEscape(e) { 
                            if (e.key === "Escape") { 
                                modal.remove(); 
                                document.removeEventListener("keydown", closeOnEscape); 
                            } 
                        }); 
                    } 
                }
            </script>',
            htmlspecialchars($url, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($fileName, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($url, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($fileName, ENT_QUOTES, 'UTF-8'),
            addslashes($url),
            addslashes($fileName),
            htmlspecialchars($url, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($fileName, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($url, ENT_QUOTES, 'UTF-8')
        );
    }

    private static function getEntityProperty(mixed $entity, string $property): mixed
    {
        if (!is_object($entity)) {
            return null;
        }

        try {
            $reflection = new \ReflectionClass($entity);

            // 尝试通过方法获取属性值
            $value = self::getValueByMethod($reflection, $entity, $property);
            if (null !== $value) {
                return $value;
            }

            // 尝试通过直接属性访问
            return self::getValueByProperty($reflection, $entity, $property);
        } catch (\ReflectionException $e) {
            return null;
        }
    }

    /**
     * @param \ReflectionClass<object> $reflection
     */
    private static function getValueByMethod(\ReflectionClass $reflection, object $entity, string $property): mixed
    {
        $methods = ['get' . ucfirst($property), 'is' . ucfirst($property)];

        foreach ($methods as $methodName) {
            if ($reflection->hasMethod($methodName)) {
                $method = $reflection->getMethod($methodName);
                if ($method->isPublic()) {
                    return $method->invoke($entity);
                }
            }
        }

        return null;
    }

    /**
     * @param \ReflectionClass<object> $reflection
     */
    private static function getValueByProperty(\ReflectionClass $reflection, object $entity, string $property): mixed
    {
        if (!$reflection->hasProperty($property)) {
            return null;
        }

        $reflectionProperty = $reflection->getProperty($property);
        if ($reflectionProperty->isPublic()) {
            return $reflectionProperty->getValue($entity);
        }

        return null;
    }
}
