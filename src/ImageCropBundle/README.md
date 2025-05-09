# Chameleon System ImageCropBundle
===================================

Overview
--------
The ImageCropBundle adds first-class image cropping support to the Chameleon System media manager and image fields.
Editors can upload images or choose from the library, draw crop rectangles (cutouts) in the backend, and use those exact regions in frontend templates.

Installation
------------
This bundle is part of the `chameleon-system/chameleon-base` package.

- Register the bundle in `app/AppKernel.php`:
  ```php
  // app/AppKernel.php
  public function registerBundles()
  {
      $bundles = [
          // ...
          new ChameleonSystem\ImageCropBundle\ChameleonSystemImageCropBundle(),
      ];
      return $bundles;
  }
  ```
- Run the Chameleon System CMS update.
- Install assets: `php bin/console assets:install --symlink` (or equivalent).
- Clear the Symfony cache.

Presets
-------
Define crop presets in the `cms_image_crop_preset` table (via the Table Editor):
  * **system_name** – unique key (e.g. `thumbnail`, `hero`)
  * **name** – human-readable label
  * **width**, **height** – target dimensions
These presets appear in the crop UI and can be referenced in Twig or as field defaults.

Field Type: Image with Crop
--------------------------
To enable cropping on a record field:
1. In the **Table Editor** for your table, select or add a field with PHP class `TCMSFieldMediaWithImageCrop` (label “Image with crop”).
2. Set the **field name** (e.g. `cms_media_id` or any custom name).
3. Define a **default value** pointing to a placeholder image record ID (e.g. `1`).
4. (Optional) In the field’s “Parameters” or “Extended Settings”:  
   - `bShowCategorySelector=1/0` – show media category selector (default `1`).  
   - `sDefaultCategoryId=<media category id>` – upload new images into this category.  
   - `imageCropPresetSystemName=<preset_system_name>` – default preset applied on new records.  
   - `imageCropPresetRestrictionSystemNames=<preset1;preset2;…>` – restrict dropdown to these presets.
5. Save.  
The storage table will now hold two values per field:  
- `<fieldName>` → the CMS media ID.  
- `<fieldName>ImageCropId` → the selected crop record ID.

Twig Usage
----------
Use the provided Twig filters from `ChameleonSystem\ImageCropBundle\Twig\CropImageExtension`:
  - `imageUrlWithCropFallbackPreset(mediaId, cropId, preset)`  
  - `imageUrlWithCropFallbackSize(mediaId, cropId, width, height)`  
  - `imageHasCropDataForPreset(mediaId, preset)`  
  - `imageUrlWithCropSize(mediaId, cropId, targetWidth, targetHeight)`  

Examples:
```twig
{# 1) Use existing crop or fallback to a named preset #}
<img src="{{ record.cms_media_id       
             |imageUrlWithCropFallbackPreset(record.cms_media_id_image_crop_id, 'thumbnail') }}"
     alt="{{ record.name }}" />

{# 2) Fallback to a fixed size if no crop exists #}
<img src="{{ record.cms_media_id       
             |imageUrlWithCropFallbackSize(record.cms_media_id_image_crop_id, 300, 200) }}"
     alt="..." />

{# 3) Check for crop availability in a preset #}
{% if record.cms_media_id | imageHasCropDataForPreset('hero') %}
  <div class="hero-image">
    <img src="{{ record.cms_media_id | imageUrlWithCropFallbackPreset(record.cms_media_id_image_crop_id, 'hero') }}" />
  </div>
{% endif %}

{# 4) Force a crop to resize to exact dimensions #}
<img src="{{ record.cms_media_id | imageUrlWithCropSize(record.cms_media_id_image_crop_id, 400, 400) }}" />
```

Programmatic API
-----------------
If you need to generate URLs in PHP, inject `CropImageServiceInterface`:
```php
use ChameleonSystem\ImageCropBundle\Bridge\Chameleon\Service\CropImageServiceInterface;

class YourService {
    public function __construct(private CropImageServiceInterface $cropService) {}

    public function getUrl(string $mediaId, string $preset, string $langId): ?string
    {
        $model = $this->cropService->getCroppedImageForCmsMediaIdAndPresetId(
            $mediaId,
            $preset,
            $langId
        );
        return $model ? $model->getImageUrl() : null;
    }
}
```

License
-------
This bundle is released under the same license as the Chameleon System.
