# Multi File Upload

TYPO3 extension for multi-file upload in the Form Framework.

## Features

- Multi-image upload form element for TYPO3 Form Framework
- Preview gallery with lightbox support
- Delete functionality for uploaded files
- Email attachment support for multiple files
- Bootstrap 5 compatible styling
- German and English localization

## Requirements

- TYPO3 13.4 LTS
- PHP 8.2+

## Installation

```bash
composer require brezo-it/multi-file-upload
```

## Usage

### Form Editor

1. Open the TYPO3 Form Editor
2. Add a new element "Multi Image Upload" from the "Custom" group
3. Configure allowed MIME types and upload folder as needed

### YAML Configuration

```yaml
renderables:
  - identifier: images
    type: MultiImageUpload
    label: 'Upload Images'
    properties:
      saveToFileMount: '1:/user_upload/'
      allowedMimeTypes:
        - 'image/jpeg'
        - 'image/png'
        - 'image/bmp'
```

### Email Finisher

To use the extended EmailFinisher that supports multi-file attachments:

```yaml
finishers:
  - identifier: EmailToReceiver
    options:
      implementationClassName: BrezoIt\MultiFileUpload\Form\Finishers\EmailFinisher
      subject: 'New submission'
      recipients:
        admin@example.com: 'Admin'
      senderAddress: 'noreply@example.com'
      attachUploads: true
```

## Configuration Options

### Form Element Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `saveToFileMount` | string | `1:/user_upload/` | FAL storage path for uploads |
| `allowedMimeTypes` | array | `image/jpeg, image/png, image/bmp` | Allowed file types |
| `imageMaxWidth` | int | `400` | Max width for preview images |
| `imageMaxHeight` | int | `400` | Max height for preview images |
| `imageLinkMaxWidth` | int | `1200` | Max width for lightbox images |

### Rendering Options (CSS Classes)

| Option | Default | Description |
|--------|---------|-------------|
| `previewListClass` | `row g-3` | Container class for image grid |
| `previewItemClass` | `col-md-4 col-lg-3 mb-3` | Class for each image item |
| `previewImageWrapperClass` | `card` | Wrapper class for image card |
| `deleteWrapperClass` | `form-check card-footer` | Class for delete checkbox wrapper |

## File Structure

```
Classes/
  Form/
    Elements/MultiImageUpload.php       # Form element definition
    Finishers/EmailFinisher.php         # Extended email finisher
  Mvc/Property/
    PropertyMappingConfiguration.php    # Property mapping config
    TypeConverter/
      MultiUploadedFileReferenceConverter.php  # File upload converter
  ViewHelpers/Form/
    MultiUploadedResourceViewHelper.php        # File input rendering
    MultiUploadDeleteCheckboxViewHelper.php    # Delete checkbox
    RenderFormValueViewHelper.php              # Summary page rendering

Configuration/
  Icons.php                 # Icon registration
  JavaScriptModules.php     # Form editor JS
  Services.yaml             # DI configuration
  Yaml/FormSetup.yaml       # Form element setup

Resources/
  Private/
    Language/               # Translations (en, de)
    Partials/Form/          # Fluid templates
  Public/
    Css/multi-upload.css    # Styles
    Icons/Extension.svg     # Extension icon
    JavaScript/             # Form editor JS
```

## Customization

### Custom Styling

Override the CSS by including your own stylesheet after the extension's CSS:

```typoscript
page.includeCSS {
    multiUploadCustom = EXT:your_extension/Resources/Public/Css/multi-upload-custom.css
}
```

### Custom Templates

Override templates via TypoScript:

```typoscript
plugin.tx_form.settings.yamlConfigurations {
    100 = EXT:your_extension/Configuration/Yaml/CustomFormSetup.yaml
}
```

## License

GPL-2.0-or-later
