Chameleon System PkgCoreBundle
==============================

Overview
--------
The PkgCoreBundle provides foundational services and utilities for the Chameleon System, including:
- Redirect strategies and linkable object interfaces for frontend URL management
- Parameter container framework for scoped parameter storage
- Layout plugin management for custom UI components
- Teaser mappers for rendering CMS page teasers
- CMS endpoint modules for login and host redirection
- Network utilities and exception handling classes

Key Features
------------
- **Redirect Strategies**: Implement `ChameleonRedirectStrategyInterface` to control HTTP redirects (exception-based, shutdown-based).
- **Linkable Objects**: Use `ICmsLinkableObject` to generate CMS links from data models.
- **Parameter Container**: `AbstractPkgCmsCoreParameterContainer` manages request/session parameters across services.
- **Layout Plugins**: Manage plugins via `TPkgCmsCoreLayoutPluginManager` and `IPkgCmsCoreLayoutPlugin`.
- **Teaser Mapping**: Customize teaser output with `TPkgCoreTeaserMapper_CmsTplPage`.
- **Endpoint Modules**: Frontend modules like `MTLoginEndPoint` and `TPkgCmsCoreSendToHost` handle authentication and redirection.
- **Network Utilities**: `TPkgCoreUtility_Network` offers HTTP client helpers and host checks.
- **Exception Handling**: `TPkgCmsException` and derived classes for robust error logging and messages.

Installation
------------
This bundle is part of the `chameleon-system/chameleon-base` package and is loaded automatically by Symfony.
If you need manual registration (no Symfony Flex), add in `app/AppKernel.php`:
```php
public function registerBundles()
{
    $bundles = [
        // ...
        new ChameleonSystem\PkgCoreBundle\ChameleonSystemPkgCoreBundle(),
    ];
    return $bundles;
}
```

Usage
-----
**Redirect Strategy**  
Inject and use the redirect strategy service:
```php
use ChameleonSystem\PkgCoreBundle\interfaces\ChameleonRedirectStrategyInterface;

public function foo(ChameleonRedirectStrategyInterface $strategy): Response
{
    return $strategy->redirectToUrl('https://example.com');
}
```

**Parameter Container**  
Use or extend `AbstractPkgCmsCoreParameterContainer` to store scoped parameters:
```php
$container = new MyParameterContainer();
$container->setParameter('key', 'value');
$value = $container->getParameter('key');
```

**Layout Plugins**  
Retrieve and render plugins via the plugin manager:
```php
$manager = \ChameleonSystem\CoreBundle\ServiceLocator::get('pkg_cms_core.layout_plugin_manager');
$plugin = $manager->getPlugin('pluginName');
echo $plugin->render($data);
```

**Teaser Mappers**  
Tag custom teaser mappers (in `mappers/teaser`) with `chameleon_system.mapper` to override teaser rendering.

Extensibility
-------------
- Extend or override classes in `objects/redirect`, `objects/parameterContainer`, `objects/layoutPlugins`, or `cmsModules`.
- Register additional mappers under `mappers` by tagging services with `chameleon_system.mapper`.

License
-------
This bundle is released under the same license as the Chameleon System. See the LICENSE file in the project root.