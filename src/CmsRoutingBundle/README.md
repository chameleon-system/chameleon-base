Chameleon System CmsRoutingBundle
=================================

This bundle provides dynamic routing for Chameleon System projects, loading SEO-friendly routes from the database. 
Administrators can configure custom URLs in the CMS backend without touching Symfony config files.

Installation
------------
Note: The bundle is already registered with Chameleon System by default.

Install via Composer:

    composer require chameleon-system/chameleon-base

Bundle Registration
-------------------
For Symfony 4+ (Flex), the bundle is auto-registered.
For Symfony 3 or lower or without Flex, add to `AppKernel.php`:

    // app/AppKernel.php
    public function registerBundles()
    {
        $bundles = [
            // ...
            new ChameleonSystem\CmsRoutingBundle\ChameleonSystemCmsRoutingBundle(),
        ];
        return $bundles;
    }

Services
--------
The following service IDs are available (defined in the CoreBundle):

- `chameleon_system_core.routing.loader.chameleon`
  - Class `esono\pkgCmsRouting\CmsRouteLoader`
  - A `routing.loader` for importing routes from the CMS (`pkg_cms_routing` table).

Interfaces & Extension Points
-----------------------------
- `esono\pkgCmsRouting\CollectionGeneratorInterface`
  - Implement this interface in a service to generate a `RouteCollection` from custom logic. Reference your service ID in the CMS route entry with type `service`.

- `esono\pkgCmsRouting\RouteControllerInterface`
  - Define controllers for dynamic routes by implementing this interface (or extend `AbstractRouteController`).

Events
------
- `ChameleonSystem\CmsRoutingBundle\Event\RoutingConfigChangedEvent`
  - Dispatched whenever a routing entry (`pkg_cms_routing`) is created, updated, or deleted. Listen to this event to clear caches or trigger rebuilds.

Bridge & Backend Integration
----------------------------
The `Bridge/Chameleon` directory contains migrations and a `CmsRoutingTableEditor` to manage routing entries in the Chameleon backend.

Usage Example
-------------
Register a custom route generator service in XML:

```xml
<service id="App\Routing\MyRouteGenerator" class="App\Routing\MyRouteGenerator">
    <!-- no tags required; reference this ID in the CMS route record (type = "service") -->
</service>
```

Implement the generator:

```php
use esono\pkgCmsRouting\CollectionGeneratorInterface;
use Symfony\Component\Routing\RouteCollection;

class MyRouteGenerator implements CollectionGeneratorInterface
{
    public function getCollection(array $routeConfig, $portal = null, $language = null): RouteCollection
    {
        $collection = new RouteCollection();
        // build and return routes based on $routeConfig and context
        return $collection;
    }
}
```

After saving a CMS route with your service ID, clear the routing cache or listen to `RoutingConfigChangedEvent` to rebuild.

License
-------
This bundle is released under the same license as the Chameleon System.
