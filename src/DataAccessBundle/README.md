Chameleon System DataAccessBundle
=================================

Overview
--------
The DataAccessBundle provides Doctrine ORM entity mappings and related infrastructure for core CMS data objects. It enables modern object-relational access to tables such as pages, navigation, modules, and more, replacing legacy Tdb* classes with Doctrine entities.

Installation
------------
This bundle is included in the `chameleon-system/chameleon-base` package. No additional installation steps are required beyond installing `chameleon-system/chameleon-base` via Composer.

Bundle Registration
-------------------
This bundle is part of the `chameleon-system/chameleon-base` package.

Symfony Flex (4+) auto-registers bundles. Without Symfony Flex, add it to `app/AppKernel.php` manually:

```php
public function registerBundles()
{
    $bundles = [
        // ...
        new ChameleonSystem\DataAccessBundle\ChameleonSystemDataAccessBundle(),
    ];
    return $bundles;
}
```

Configuration
-------------
No custom configuration is needed. The bundle registers its services and mapping files automatically.
The service definition in `Resources/config/services.xml` sets:
- Autowiring and autoconfiguration for PHP services under `ChameleonSystem\DataAccessBundle`.
- A Doctrine `postLoad` event listener (`EmptyStringRelationPostLoadListener`) to normalize empty-string foreign keys to null.

Entity Mapping
--------------
All ORM mappings are defined in `config/doctrine/*.orm.xml`. Entities live under `src/Entity`, grouped by domain:

- `Entity/Core` (e.g. `PkgCmsRouting`, `CmsTplPage`, `CmsUser`)
- `Entity/CoreConfig` (e.g. `CmsConfig`, `CmsConfigParameter`)
- `Entity/CoreMedia`, `Entity/CoreMenu`, `Entity/CorePortal`, etc.

Example: Fetch active front-end routes
```php
$em = $this->get('doctrine')->getManager();
$repo = $em->getRepository(ChameleonSystem\DataAccessBundle\Entity\Core\PkgCmsRouting::class);
$routes = $repo->findBy(
    ['active' => true],
    ['position' => 'ASC']
);
```

Services
--------
- **ChameleonSystem\DataAccessBundle\Doctrine\EmptyStringRelationPostLoadListener**
  - Listens to Doctrine `postLoad` events
  - Converts empty-string many-to-one foreign keys to `null` to prevent invalid relation lookups

Extending
---------
To extend or override mappings:
1. Place custom `.orm.xml` files in your application’s `config/doctrine` directory, ensuring they are loaded before the bundle mappings.
2. Extend entity classes under `src/Entity` and update your mapping accordingly.

License
-------
This bundle follows the same license as the Chameleon System.