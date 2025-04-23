# ImageCropBundle

It's possible to crop images directly in the media manager or image fields and use the resulting cutouts in templates.

## Installation

- Add `new \ChameleonSystem\ImageCropBundle\ChameleonSystemImageCropBundle()` to the AppKernel.
- Run CMS updates.
- Run `assets:install` console command.
- Clear Symfony cache.

## Presets

You can define presets for common image formats in `cms_image_crop_preset`. Users can choose these presets when creating cutouts.

## Field Type

To use cropped images for an image field, change the field type to `Image with crop`. This will allow to choose or create a cutout. The ID of the crop is provided in the autoclass (e.g. fieldCmsMediaIdImageCropId).

You can restrict to certain presets by setting `imageCropPresetRestrictionSystemNames` in the field config or define a default preset with `imageCropPresetSystemName`.

## Twig

Use the twig filters in \ChameleonSystem\ImageCropBundle\Twig\CropImageExtension to show the cropped images. There are different options:

- Show a specific crop by providing a crop ID or preset (`imageUrlWithCropFallbackPreset`)
- Show a specific crop by providing a crop ID or fixed size (`imageUrlWithCropFallbackSize`)
- Show a specific crop, but scale and crop it to a fixed size (`imageUrlWithCropSize`)

If preset or size is provided, the corresponding crop is automatically selected. If there is no crop that fits, image is cropped from the center.