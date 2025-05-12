Chameleon System UrlAliasBundle
===============================
# Overview
The UrlAliasBundle enables SEO-friendly URL aliasing in the Chameleon System.  
It maps arbitrary source URLs (with optional parameter mapping and wildcard support) to internal target routes or external URLs, issuing permanent redirects as needed.  
The bundle also provides a scheduled cleanup Cronjob for obsolete alias entries.

# Key Features
- **Dynamic URL Aliases**: Define one-to-one or wildcard aliases in the `cms_url_alias` table.
- **Parameter Mapping**: Rename, ignore, or preserve URL query parameters when redirecting.
- **Exact and Wildcard Matches**: Support for exact matches or prefix-based (non-exact) routing.
- **Sorted Resolution**: Aliases are evaluated in order of specificity (fewer URL segments first).
- **Permanent Redirects**: Executes HTTP 301 redirects to target URLs.
- **Cronjob Cleanup**: `delete_old_url_alias_entries_cronjob` removes stale alias records.
- **Migration Support**: Database migrations for initial table creation and schema updates.

# Installation
This bundle is included in the `chameleon-system/chameleon-base` package and already installed.
To register manually (no Flex), add to `app/AppKernel.php`:
```php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = [
        // ...
        new ChameleonSystem\UrlAliasBundle\ChameleonSystemUrlAliasBundle(),
    ];
    return $bundles;
}
```
Clear the cache:
```bash
php bin/console cache:clear
```

# Configuration
No additional YAML configuration is required.  
Manage URL aliases via the **Table Editor** under `cms_url_alias`:
- **source_url**: incoming URL path or pattern (with optional `?param=value`).
- **target_url**: destination URL or path (supports variable injection).
- **exact_match**: `1` for exact-only redirects; `0` for prefix (wildcard) matches.
- **ignore_parameter**: newline- or comma-separated query parameters to ignore.
- **parameter_mapping**: lines of `oldParam=newParam` to rename parameters.
- **cms_portal_id**: limit alias to a specific portal or leave blank for global.
- **active**: enable or disable the alias without deleting it.

# Usage
1. **Create an Alias**  
   - In the Table Editor, insert a record in `cms_url_alias` with your desired source and target URLs.
2. **Test Redirects**  
   - Visit the source URL in your browser (including query strings) and observe a 301 redirect to the target.
3. **Wildcard Example**  
   - Source: `/old-section/?*`
   - Target: `/new-section/?{*}`  
   - All paths under `/old-section/` map to `/new-section/`, preserving query parameters.

# Cronjob Cleanup
Run the cleanup task to delete URL alias entries older than the configured retention period:
```bash
php bin/console chameleon:cron:run delete_old_url_alias_entries_cronjob
```

# Extensibility
- **Custom Handler**: Extend `TCMSSmartURLHandler_URLAlias` (in `objects/TCMSSmartURLHandler`) to modify resolution logic.
- **Migration Scripts**: Add new migration files under `Bridge/Chameleon/Migration/Script` to adjust schema.
- **Table Editor Overrides**: Customize table editor behavior by extending `objects/TCMSTableEditor` classes.

# License
This bundle is released under the same license as the Chameleon System. See the LICENSE file in the project root.
