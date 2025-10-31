# EasyAdminImagePreviewFieldBundle

[English](README.md) | [中文](README.zh-CN.md)

EasyAdmin bundle providing image preview fields with modal support.

## Usage

```php
use Tourze\EasyAdminImagePreviewFieldBundle\Field\ImagePreviewField;

public function configureFields(string $pageName): iterable
{
    // Basic usage
    yield ImagePreviewField::new('url', 'Image');
    
    // Custom configuration
    yield ImagePreviewField::new('imageUrl', 'Preview')
        ->setWidthProperty('imageWidth')    // default: 'width'
        ->setHeightProperty('imageHeight')  // default: 'height'  
        ->setFilenameProperty('originalName') // default: 'fileName'
        ->setMaxSize(120, 80)              // list view size
        ->setDetailMaxSize(400, 300);      // detail view size
}
```

## Features

- **List view**: Small thumbnails with modal preview on click
- **Detail view**: Larger preview images with "open in new window" link  
- **Configurable**: Customize property names and sizes
- **Responsive**: Modal closes on click/ESC key