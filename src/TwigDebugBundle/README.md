Chameleon System TwigDebugBundle
================================

Overview
--------
The TwigDebugBundle enhances Twig template rendering in the Chameleon System by injecting HTML comments around `include` calls and integrating with the ViewRenderer post-render event. It helps developers trace which Twig templates and blocks were used to generate each part of a page.

Key Features
------------
- **Include Annotations**: Wraps each Twig `include` with HTML comments indicating the included template path.
- **Post-Render Listener**: Hooks into `chameleon_system.viewrenderer.post_render` to annotate rendered HTML from snippets and modules.
- **Twig Extension**: Provides a custom `DebugExtension` that overrides the Twig `include` node behavior.
- **Configurable**: Enable or disable via a simple configuration flag.
- **Development Aid**: Ideal for debugging complex template hierarchies in backend and frontend.

Installation
------------
This bundle is included in `chameleon-system/chameleon-base` and registered automatically via Symfony Flex or bundle discovery.
To register manually (no Flex), add to `app/AppKernel.php`:
```php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = [
        // ...
        new ChameleonSystem\TwigDebugBundle\ChameleonSystemTwigDebugBundle(),
    ];
    return $bundles;
}
```
Clear the cache:
```bash
php bin/console cache:clear
```

Configuration
-------------
Enable or disable the debug annotations in your configuration file (e.g. `config/packages/chameleon_system_twig_debug.yaml`):
```yaml
chameleon_system_twig_debug:
  enabled: true
```

Usage
-----
With the bundle enabled, Twig `include` statements are preceded and followed by comments in the rendered HTML:
```html
<!-- BEGIN Twig include: path/to/template.html.twig -->
...included content...
<!-- END Twig include: path/to/template.html.twig -->
```
These annotations also apply to snippets and modules rendered via the Chameleon ViewRenderer.

Extensibility
-------------
- **Override Template Comments**: Extend `ChameleonSystem\TwigDebugBundle\Twig\Extension\DebugExtension` to customize comment format.
- **Custom Post-Render Handling**: Decorate or replace `ViewRendererPostRenderListener` to add additional annotations or filtering.
- **Disable in Production**: Keep `enabled: false` in production environments to avoid overhead and leaking template paths.

License
-------
This bundle is released under the same license as the Chameleon System. See the LICENSE file in the project root for details.