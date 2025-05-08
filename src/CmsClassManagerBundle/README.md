Chameleon System CmsClassManagerBundle
======================================

Overview
--------
The CmsClassManagerBundle automates the generation and management of virtual PHP classes based on database-driven extension chains.
It reads configuration from the `pkg_cms_class_manager` table and writes class files to your application's auto-generated classes directory.

Features
--------
- Define entry-point classes and extension hierarchies via CMS table entries
- Dynamically generate chained class definitions on disk
- Refresh virtual classes on deployment or at runtime
- Lightweight service (`chameleon_system_cms_class_manager.manager`)

Installation
------------
This bundle is included by default in Chameleon System. To register manually, add to your kernel or `bundles.php`:
```php
// in AppKernel::registerBundles() or bundles.php
new ChameleonSystem\\CmsClassManagerBundle\\ChameleonSystemCmsClassManagerBundle(),
```

Configuration
-------------
Use the CMS backend to manage the `pkg_cms_class_manager` table:
- **name_of_entry_point**: Fully qualified class name to use as the root of the virtual class chain.
- **exit_class**: Base class for the first generated class in the chain.

Example rows:
```
name_of_entry_point: MyEntryPointClass
exit_class: BaseClass
```

Services
--------
The bundle exposes one primary service: `TPkgCmsVirtualClassManager`

Usage
-----
Inject and use the manager service in your code or deployment script:
```php
use TPkgCmsVirtualClassManager;

class MyDeploymentService
{
    public function __construct(
        private readonly TPkgCmsVirtualClassManager $classManager
    ) {}

    public function regenerateVirtualClasses(string $entryPoint, string $targetDir): void
    {
        // Load configuration for the entry point from DB
        if ($this->classManager->load($entryPoint)) {
            // Generate or refresh virtual class files
            $this->classManager->UpdateVirtualClasses($targetDir);
        }
    }
}
```

Static Utility
---------------
To query the database for valid entry-point classes, use:
```php
$entryPoint = TPkgCmsVirtualClassManager::GetEntryPointClassForClass(
    'MyClassName', // class name you want an entry for
    'subType',     // subtype identifier (not used by default)
    'type',        // type identifier (not used by default)
    false          // refresh cache of entry points
);
```

Directory Structure
-------------------
By default, generated classes are written to the auto-data-object directory under `var/autoclasses/`.
Pass a custom `$targetDir` to `UpdateVirtualClasses()` to override.

Testing
-------
Unit tests are provided under `Tests/CacheTest.php` using fixture data in `Tests/fixtures/`.

License
-------
This bundle is licensed under the MIT License. See `LICENSE` in the project root.
