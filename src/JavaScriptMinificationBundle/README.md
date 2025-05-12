# Chameleon System JavaScriptMinificationBundle
=============================================

Overview
--------
The JavaScriptMinificationBundle integrates a pluggable JavaScript minification pipeline into the Chameleon System resource collection.  
It intercepts aggregated JavaScript content (event `chameleon_system_core.resource_collection_collected.javascript`) and applies a configured minifier before output.

Installation
------------
- **Included** with `chameleon-system/chameleon-base`; no separate Composer require needed.
- Symfony Flex auto-registers the bundle. Without Flex, register in **app/AppKernel.php**:
  ```php
  // app/AppKernel.php
  public function registerBundles()
  {
      $bundles = [
          // ...
          new ChameleonSystem\JavaScriptMinificationBundle\ChameleonSystemJavaScriptMinificationBundle(),
      ];
      return $bundles;
  }
  ```
- Clear the Symfony cache.

Configuration
-------------
Configure which minifier integration to use (or disable minification) in `config/packages/chameleon_system_java_script_minification.yaml`:
```yaml
chameleon_system_java_script_minification:
  # alias of a tagged MinifyJsIntegration service, or null to disable auto-minification
  js_minifier_to_use: 'jshrink'
```
By default (`null`), the event listener is disabled and no JS is minified automatically.

Services
--------
- `chameleon_system_javascript_minify.minify_js` – **MinifyJsService** (implements `MinifyJsServiceInterface`).  
  Use `minifyJsContent(string $js): string` to invoke the configured integration manually.
- `chameleon_system_javascript_minify.javascript_minify_event_listener` – listens to the JS resource collection event and calls `minifyJsContent` automatically.  
  Uses Monolog channel **javascript_minify** for logging integration errors.

Creating a Minifier Integration
-------------------------------
To add your own minifier:
1. Create a class implementing `ChameleonSystem\JavaScriptMinification\Interfaces\MinifyJsIntegrationInterface`:
   ```php
   namespace App\Minifier;

   use ChameleonSystem\JavaScriptMinification\Interfaces\MinifyJsIntegrationInterface;
   use ChameleonSystem\JavaScriptMinification\Exceptions\MinifyJsIntegrationException;

   class JsShrinkIntegration implements MinifyJsIntegrationInterface
   {
       public function minifyJsContent($jsContent)
       {
           // call external library, throw MinifyJsIntegrationException on error
           return \JShrink\Minifier::minify($jsContent);
       }
   }
   ```
2. Register it as a Symfony service and tag it:
   ```yaml
   services:
     App\Minifier\JsShrinkIntegration:
       tags:
         - { name: 'chameleon_system.minify_js', alias: 'jshrink' }
   ```
3. Set `js_minifier_to_use: 'jshrink'` in your bundle config.
4. The `MinifyJsService` will receive your integration at compile time and perform minification.

Usage
-----
**Automatic**: When the JS resource collector event fires, registered JS content is minified transparently.

**Manual**: Inject `MinifyJsServiceInterface` to call on arbitrary JS strings:
```php
use ChameleonSystem\JavaScriptMinification\Interfaces\MinifyJsServiceInterface;

class MyService {
    public function __construct(private MinifyJsServiceInterface $minifier) {}

    public function doSomethingWithJs(string $script): string
    {
        return $this->minifier->minifyJsContent($script);
    }
}
```

Logging
-------
Errors in the integration are logged via Monolog under channel **javascript_minify**.

License
-------
This bundle is released under the same license as the Chameleon System.
