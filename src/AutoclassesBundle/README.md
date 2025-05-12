Chameleon System AutoclassesBundle
==================================

Overview
--------
The AutoclassesBundle automates generation of PHP model classes from database table definitions. 
It ensures IDE auto-completion, static analysis support, and type-safe access to your data layer.

Features
--------
- Generate and maintain data classes for real and virtual tables
- Console commands for on-demand generation and exporting table configurations
- Symfony CacheWarmer integration for deployment workflows
- Safe atomic updates using a temporary directory to avoid partial writes

Installation
------------
This bundle is included and enabled by default in Chameleon System. To register manually, add it to your kernel or bundles configuration:

```php
// in AppKernel::registerBundles() or bundles.php
new ChameleonSystem\AutoclassesBundle\ChameleonSystemAutoclassesBundle(),
```

Configuration
-------------
- Ensure the `var/autoclasses` directory exists and is writable by both the web server and CLI user.
- Default target directory is `%kernel.cache_dir%/autoclasses`.

Usage
-----
1. Generate or update all autoclasses via console:

    ```bash
    php bin/console chameleon_system:autoclasses:generate
    ```

2. Export table configuration for use with Doctrine or other tools:

    ```bash
    php bin/console chameleon_system:autoclasses:dump
    ```

3. Symfony CacheWarmer
   The `AutoclassesCacheWarmer` implements the `CacheWarmerInterface` and runs automatically during `cache:warmup` in non-debug (prod) environments.

Cleanup
-------
To remove generated classes and start fresh, delete the autoclasses directory:

```bash
rm -rf var/autoclasses var/autoclasses_*
```

License
-------
This bundle is licensed under the MIT License. See the `LICENSE` file at the project root for details.
