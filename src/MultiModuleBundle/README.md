Chameleon System MultiModuleBundle
==================================
Overview
--------
The MultiModuleBundle enables grouping and rendering multiple backend modules as a single composite module in the Chameleon System. Use cases include tabbed interfaces, multi-step wizards, and dynamic module pipelines.

Key features
- Define named sets of modules via the `pkg_multi_module_set` and `pkg_multi_module_set_item` tables.
- Render a single module instance that outputs all items in a set, with configurable “show full” vs. tab-style output.
- Extendable via custom view mappers, sorters, and template overrides.

Installation
------------
This bundle is included in `chameleon-system/chameleon-base`; no additional composer require is needed.

- Copy the MultiModule implementation into your project’s framework modules directory:
  ```bash
  cp -R vendor/chameleon-system/chameleon-base/src/MultiModuleBundle/installation/tocopy/private/framework/modules/MTPkgMultiModule \
     src/framework/modules/MTPkgMultiModule
  ```
- Register the bundle in `app/AppKernel.php` (if not using Flex auto-registration):
  ```php
  // app/AppKernel.php
  public function registerBundles()
  {
      $bundles = [
          // ...
          new ChameleonSystem\MultiModuleBundle\ChameleonSystemMultiModuleBundle(),
      ];
      return $bundles;
  }
  ```

Usage
-----
1. **Define a Module Set**  
   - In the **Table Editor**, open **pkg_multi_module_set** and create a new set record.  
   - Switch to **pkg_multi_module_set_item** and add items: select a CMS module, set title, system name, sort order, and `show_full` flag.  

2. **Place the MultiModule on a Page**  
   - In the **Table Editor**, open **cms_tpl_module_instance**.  
   - Create a new record with **Module Type** = “Multi Module” (`pkgMultiModule`).  
   - Set **PkgMultiModuleSetId** to your set’s ID, assign a spot name, theme, etc., and save.  

3. **(Optional) Render Directly in Twig**  
   ```twig
   {{ render(controller('ChameleonSystemMultiModuleBundle:BackendModule:multiModule', {
       spotName: 'yourSpotName'
   })) }}
   ```

Extensibility
-------------
- **View Mappers**: implement `IViewMapper` and tag with `chameleon_system_multimodule.mapper.*` to supply custom data.  
- **Sort Columns**: tag services with `chameleon_system_multimodule.sort_column` to add new sorting criteria.  
- **Template Overrides**: copy `src/framework/modules/MTPkgMultiModule/views/standard.view.php` to adjust HTML structure.  

License
-------
This bundle is released under the same license as the Chameleon System (see LICENSE in the project root).
