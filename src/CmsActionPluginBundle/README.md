Chameleon System CmsActionPluginBundle
======================================

Overview
--------
The CmsActionPluginBundle allows you to register lightweight action handlers (plugins) that respond to `module_fnc` calls
without rendering full modules. This is ideal for handling AJAX calls, redirects, or other custom logic without the overhead
of a full backend module.

Features
--------
- Register action plugins at the portal or individual page level
- Handle `module_fnc[pluginName]=actionMethod` calls with simple PHP classes
- Avoid full module instantiation when only executing logic is required

Configuration
-------------
Action plugins are configured via CMS fields on your portal or on a page:

Each entry in the list must be on its own line and follow the format:
```
pluginIdentifier=Fully\Qualified\ClassName
```
- **pluginIdentifier**: the key used in the `module_fnc` call
- **ClassName**: PHP class name implementing your action plugin

Example (page or portal settings):
```
ChangeLanguage=TPkgCmsActionPlugin_ChangeLanguage
MyCustomAjax=App\Action\MyCustomAjaxPlugin
```

Creating an Action Plugin
-------------------------
1. Create a PHP class extending `AbstractPkgActionPlugin`:
    ```php
    use ChameleonSystem\CoreBundle\ServiceLocator;
    use ChameleonSystem\CoreBundle\Service\RedirectInterface;

    class TPkgCmsActionPlugin_ChangeLanguage extends AbstractPkgActionPlugin
    {
        public function changeLanguage(array $data): void
        {
            // e.g. $data['lang'] contains language code
            if (!isset($data['lang'])) {
                return;
            }
            // perform custom logic, then redirect
            $redirect = ServiceLocator::get('chameleon_system_core.redirect');
            $redirect->redirect('/?lang='.$data['lang']);
        }
    }
    ```

2. Register the plugin in CMS settings portal or page.

Calling Your Plugin
-------------------
In your template or JavaScript, trigger the action via `module_fnc` in GET or POST:
```html
<a href="/my-page?module_fnc[ChangeLanguage]=changeLanguage&lang=de">Deutsch</a>
```
Or via a POST form:
```html
<form method="post" action="/my-page">
  <input type="hidden" name="module_fnc[MyCustomAjax]" value="doWork">
  <input type="hidden" name="foo" value="bar">
  <button type="submit">Do AJAX Work</button>
</form>
```

Under the hood, `ChameleonController` will detect that `changeLanguage` is not a regular module method,
instantiate `TPkgCmsActionPluginManager`, and dispatch your plugin’s method with the provided parameters.

Error Handling
--------------
- If the plugin identifier is not registered, the call is ignored.
- If the method does not exist or is not public, a warning is triggered (no uncaught exception).

License
-------
This bundle is licensed under the MIT License. See the project root `LICENSE` file for details.