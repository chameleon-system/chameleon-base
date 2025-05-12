Chameleon System SnippetRendererBundle
======================================
# Overview
The SnippetRendererBundle provides a lightweight, Twig-powered snippet rendering engine for HTML templates with named blocks and variable substitution.
It supports string-based snippets, file-based templates, and CMS module views, with integrated resource handling (CSS/JS/LESS) and robust error management.

# Key Features
- Render snippets from strings, files, or CMS modules using `GetNewInstance(source, type)`.
- Named block syntax:
  ```html
  [{ block header }]Default Header[{ endblock }]
  ```
- Variable injection via `setVar(name, value)` or buffered capture (`setCapturedVarStart/Stop`).
- Two Twig environments: file-based (`twig`) and string-based (`twig.string_environment`).
- PSR-3 logging of template loader errors and custom `SnippetRenderingException` for runtime issues.
- `IResourceHandler` support to collect and inject CSS, JS, and LESS assets declared in snippets.
- Legacy renderer compatibility (`TPkgSnippetRendererLegacy`) using `TViewParser` for backward support.
- Configurable service `chameleon_system_snippet_renderer.snippet_renderer` for dependency injection.

# Installation
The bundle is included in `chameleon-system/chameleon-base`; no additional Composer installation is needed.

To register manually (no Flex), add to `app/AppKernel.php`:
```php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = [
        // ...
        new ChameleonSystem\SnippetRendererBundle\ChameleonSystemSnippetRendererBundle(),
    ];
    return $bundles;
}
```
Clear the cache:
```bash
php bin/console cache:clear
```

# Usage
## Basic String Rendering
```php
use ChameleonSystem\SnippetRendererBundle\interfaces\IPkgSnippetRenderer;

$template = '<h1>[{ block title }]Hello[{ endblock }]</h1>';
$renderer = IPkgSnippetRenderer::GetNewInstance($template, IPkgSnippetRenderer::SOURCE_TYPE_STRING);
$renderer->setVar('title', 'World');
$html = $renderer->render();
```

## Captured Variables
```php
$renderer = IPkgSnippetRenderer::GetNewInstance(
    '[{ block content }][{ endblock }]',
    IPkgSnippetRenderer::SOURCE_TYPE_STRING
);
$renderer->setCapturedVarStart('content');
echo '<p>Dynamic content</p>';
$renderer->setCapturedVarStop();
$html = $renderer->render();
```

## File-Based Templates
```php
$renderer = IPkgSnippetRenderer::GetNewInstance(
    '/path/to/template.html',
    IPkgSnippetRenderer::SOURCE_TYPE_FILE
);
$html = $renderer->render();
```

## CMS Module Views
```php
// $moduleInstance is an instance of TdbCmsTplModuleInstance
$renderer = IPkgSnippetRenderer::GetNewInstance(
    $moduleInstance,
    IPkgSnippetRenderer::SOURCE_TYPE_CMSMODULE
);
$html = $renderer->render();
```

## Resource Handling
```php
use ChameleonSystem\CoreBundle\ServiceLocator;

$resourceHandler = ServiceLocator::get('chameleon_system_core.resource_handler');
$renderer->setResourceHandler($resourceHandler);
$html = $renderer->render();
// CSS/JS collected via $resourceHandler->handleResources()
```

## Thumbnail Filter
Generate CMS image thumbnails within snippets:
```php
use ChameleonSystem\SnippetRendererBundle\Filter\TPkgSnippetRendererFilter;

$url = TPkgSnippetRendererFilter::getThumbnail(
    $mediaId,
    200,
    200,
    false,
    true
);
```

# Configuration
No additional configuration is required. To override the renderer class, adjust the `chameleon_system_snippet_renderer.snippet_renderer.class` parameter in `services.xml`.

# Extensibility
- Implement `IResourceHandler` to customize asset collection.
- Decorate or override the service `chameleon_system_snippet_renderer.snippet_renderer` to inject custom behavior.
- Extend `PkgAbstractSnippetRenderer` for new source types or rendering logic.
- Add custom Twig filters or functions by tagging services and updating `services.xml`.

# License
This bundle is released under the same license as the Chameleon System. See the LICENSE file in the project root.
