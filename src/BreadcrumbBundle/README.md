# Breadcrumb Bundle

## Overview
The BreadcrumbBundle provides a flexible way to generate and render breadcrumbs in the Chameleon System.
It replaces the legacy MTBreadcrumb module with a modern, extensible architecture based on Symfony services.

## Getting Started
### 1. Register the Module
Ensure the breadcrumb module is registered in your application kernel or `bundles.php` (if not already included):
```php
// in AppKernel::registerBundles() or bundles.php
new ChameleonSystem\BreadcrumbBundle\ChameleonSystemBreadcrumbBundle(),
```

### 2. Insert the Breadcrumb Module in Your Layout
In your page template, render the `BreadcrumbModule` module at the desired location:

This will use the first active `BreadcrumbGenerator` to build a `BreadcrumbDataModel`.

## Core Concepts

### Breadcrumb Data Models
- **BreadcrumbDataModel** (implements `Iterator` & `Countable`): holds an ordered list of `BreadcrumbItemDataModel`.
- **BreadcrumbItemDataModel**: encapsulates a single breadcrumb item with `getName(): string` and `getUrl(): string`.

### Breadcrumb Generators
A **BreadcrumbGeneratorInterface** defines two methods:
```php
interface BreadcrumbGeneratorInterface
{
    public function isActive(): bool;
    public function generate(): BreadcrumbDataModel;
}
```
Generators determine whether they should run (`isActive`) and return a populated `BreadcrumbDataModel`.

Generators are automatically collected by the **BreadcrumbGeneratorProvider** when tagged:
```xml
<tag name="chameleon_system_breadcrumb.generator.breadcrumb_generator" order="100" />
```
The `order` attribute (integer) defines priority; the first generator whose `isActive()` returns `true` is used.

### Caching
Generators may extend `AbstractBreadcrumbGenerator` to leverage built-in cache key generation. Override methods
to customize cache behavior as needed.

## Custom Generator Example
To add a custom breadcrumb generator:

1. **Create the generator class**:
    ```php
    namespace App\Breadcrumb;

    use ChameleonSystem\BreadcrumbBundle\Interfaces\BreadcrumbGeneratorInterface;
    use ChameleonSystem\BreadcrumbBundle\Library\DataModel\BreadcrumbDataModel;
    use ChameleonSystem\BreadcrumbBundle\Library\DataModel\BreadcrumbItemDataModel;

    class MyCustomBreadcrumbGenerator implements BreadcrumbGeneratorInterface
    {
        public function isActive(): bool
        {
            // return true when this generator should run, e.g. on specific pages
            return /* condition */ true;
        }

        public function generate(): BreadcrumbDataModel
        {
            $breadcrumb = new BreadcrumbDataModel();
            // prepend items in reverse order (home first):
            $breadcrumb->add(new BreadcrumbItemDataModel('Home', '/'));
            $breadcrumb->add(new BreadcrumbItemDataModel('Section', '/section'));
            // ...
            return $breadcrumb;
        }
    }
    ```

2. **Register as a service** (`config/services.yaml`):
    ```yaml
    services:
        App\Breadcrumb\MyCustomBreadcrumbGenerator:
            tags:
                - { name: 'chameleon_system_breadcrumb.generator.breadcrumb_generator', order: 50 }
    ```

This custom generator will now be considered before the default one (which has order 999).

## Snippet Template
The default Twig snippet is located at:
```
Resources/views/snippets/standard.html.twig
```
## Services Reference
- **chameleon_system_breadcrumb.module.breadcrumb**: Renders the breadcrumb module.
- **chameleon_system_breadcrumb.breadcrumb.breadcrumb_standard_page_generator**: Default page-based generator.
- **chameleon_system_breadcrumb.breadcrumb.breadcrumb_generator_utils**: Utility methods for tree-based breadcrumbs.

## License
This bundle is licensed under the MIT License. See the `LICENSE` file in the project root for details.