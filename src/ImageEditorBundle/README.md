Chameleon System ImageEditorBundle
===================================

Overview
--------
The ImageEditorBundle integrates a full-featured image editing UI (powered by Filerobot) into the Chameleon System CMS backend and media manager.
Editors can crop, resize, rotate, apply filters, annotate, and otherwise manipulate images, then save the result as a new media asset.

Installation
------------
This bundle is part of the `chameleon-system/chameleon-base` package. No additional installation steps are required.

- Symfony Flex (4+) auto-registers bundles. Without Flex, add to `app/AppKernel.php`:
  ```php
  // app/AppKernel.php
  public function registerBundles()
  {
      $bundles = [
          // ...
          new ChameleonSystem\ImageEditorBundle\ChameleonSystemImageEditorBundle(),
      ];
      return $bundles;
  }
  ```
- Run the Chameleon System CMS update.
- Install assets: `php bin/console assets:install --symlink`.
- Clear the Symfony cache.

Modules & Views
---------------
1. **Media Manager Extension**  
   The bundle registers a Media Manager extension that adds an **Edit Image** button to each media item.  
   - **Mapper**: `chameleon_system_image_editor.bridge_chameleon_media_manager_mapper.image_editor_mapper`  
   - **Template**: `snippets-cms/imageEditor/mediaManager/detailButtons.html.twig`  
   This button opens the standalone editor page.

2. **Backend Module: Image Editor**  
   A CMS backend module provides the editing canvas and save workflow.  
   - **Module Spot**: `contentmodule`  
   - **Page Definition**:  
     - `pagedef = imageEditor`  
     - `_pagedefType = @ChameleonSystemImageEditorBundle`  
   - **View Template**: `snippets-cms/imageEditor/mediaManager/imageEditor.html.twig`  

Twig Templates
--------------
All templates live under `Resources/views/snippets-cms/imageEditor/mediaManager`:
- `detailButtons.html.twig` – renders the **Edit Image** button.
- `imageEditor.html.twig` – the editor form, loads Filerobot assets, and handles save.

Services
--------
Key service IDs (configured in `Resources/config/services.xml`):
- `chameleon_system_image_editor.bridge_chameleon_backend_module.image_editor_module`  
- `chameleon_system_image_editor.bridge_chameleon_media_manager_extension.media_manager_image_editor_extension`  
- `chameleon_system_image_editor.bridge_chameleon_media_manager_mapper.image_editor_mapper`  
- `chameleon_system_image_editor.service.image_editor_url_service`  

Programmatic URL Generation
----------------------------
To open the editor from custom code, inject `ChameleonSystem\ImageEditorBundle\Interface\ImageEditorUrlServiceInterface`:
```php
use ChameleonSystem\ImageEditorBundle\Interface\ImageEditorUrlServiceInterface;

class YourService {
    public function __construct(private ImageEditorUrlServiceInterface $urlService) {}

    public function getEditorLink(string $mediaId, int $width, int $height): string
    {
        return $urlService->getImageEditorUrl($mediaId, (string)$width, (string)$height);
    }
}
```

Configuration
-------------
No additional configuration is required. All migrations add snippet chains and register the module automatically.

Usage
-----
1. In the **Media Manager**, click the **Edit Image** button on any image.  
2. The editor loads with the original image and target dimensions.  
3. Make edits (crop, rotate, filters, annotations).  
4. Click **Save Image** to write a new media record; the page will reload with the saved version.

License
-------
This bundle is released under the same license as the Chameleon System.