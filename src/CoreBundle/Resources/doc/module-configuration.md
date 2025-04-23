# Module configuration

Please note that there are two types of Modules:

1. Modules that extend `MTPkgViewRendererAbstractModuleMapper`. These Modules use twig as a template engine. The Template name and path will need to be configured in the "View/Mapper Konfiguration" field. The template path is always relative to the snippet folder within the theme folders defined.

2. Modules that extend a parent of `MTPkgViewRendererAbstractModuleMapper`. These Modules are a little older and still use php as a template engine. These Modules expect the Templates to be files of the form `viewName.view.php` and will look for them in:
   - `src/framework/modules/MyModule/views`
   - the theme of the active portal in `./webModules/MyModule`

Note that you may also define the views for modules of this type in "View/Mapper Konfiguration" field. This makes sense to do whenever the views of the module can be found in a theme and you need to know which views are available without having a portal context (as is the case when you use the multi module for example).