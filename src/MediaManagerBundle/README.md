Chameleon System MediaManagerBundle
===================================

Overview
--------
The MediaManagerBundle provides a powerful backend module for browsing, uploading, and managing media files (images and folders) in the Chameleon System CMS backend.

Key features:
- Tree and list views for media items and folders
- Drag-and-drop upload and move operations
- Multi-selection with bulk actions (delete, move)
- Inline editing of media item properties (name, metadata)
- Pagination and sorting by upload date, change date, and name
- Usage tracking: find and delete references to media items
- Integration with image fields: pick media items in form fields via a "Pick Image" dialog
- Legacy list view support through the Core List Manager

Installation
------------
- The bundle is included in `chameleon-system/chameleon-base`; no additional composer require is needed.
- If you are not using Symfony Flex auto-registration, register the bundle in `app/AppKernel.php`:
  ```php
  // app/AppKernel.php
  public function registerBundles()
  {
      $bundles = [
          // ...
          new ChameleonSystem\MediaManagerBundle\ChameleonSystemMediaManagerBundle(),
      ];
      return $bundles;
  }
  ```
- Run the **CMS update** to apply database migrations and snippet chains:
  ```bash
  php bin/console cms:update
  ```
- Publish public assets:
  ```bash
  php bin/console assets:install --symlink
  ```
- Clear the cache:
  ```bash
  php bin/console cache:clear
  ```

Configuration
-------------
Customize the media manager behavior in your configuration file (e.g. `config/packages/chameleon_system_media_manager.yaml`):
```yaml
chameleon_system_media_manager:
  # Open the media manager in a new window or within the Chameleon frame
  open_in_new_window: false

  # Available page sizes for the list view (-1 displays all items)
  available_page_sizes: [12, 24, 48, 96, 204, 504, -1]

  # Default page size for the list view
  default_page_size: 24
```

Usage
-----
Access the Media Manager in the backend via the "Media Manager" module in the navigation, or embed it programmatically:
```twig
{# Render the media manager module in a Twig template #}
{{ render(controller('ChameleonSystemMediaManagerBundle:BackendModule:mediaManager')) }}
```

Generate URLs for media items:
```php
use ChameleonSystem\MediaManagerBundle\Bridge\Chameleon\MediaManagerUrlGenerator;

$url = $this->container->get(MediaManagerUrlGenerator::class)->getMediaItemUrl($mediaItem);
```

Extensibility
-------------
- **Usage Finders**
  Implement `MediaItemUsageFinderInterface` and tag with `chameleon_system_media_manager.usage_finder` to detect custom references to media items.
- **Usage Delete Services**
  Implement `MediaItemUsageDeleteServiceInterface` and tag with `chameleon_system_media_manager.usage_delete_service` to clean up references when media items are removed.
- **Sort Columns**
  Tag services with `chameleon_system_media_manager.sort_column` to add custom sorting options (e.g. by file size or custom metadata).
- **Backend Module Mappers**
  Tag services with `chameleon_system_media_manager.backend_module.mapper.*` to alter list results, sorting, paging, search autocomplete, and more.
- **Template Overrides**
  Copy Twig templates from `Resources/views/snippets-cms/mediaManager/...` to your own bundle or theme directory to customize the UI.

Assets
------
Public assets (JavaScript and CSS) are located in `Resources/public`. If you update these assets, re-run:
```bash
php bin/console assets:install --symlink
```

License
-------
This bundle is licensed under the same terms as the Chameleon System (see LICENSE file in repository root).
