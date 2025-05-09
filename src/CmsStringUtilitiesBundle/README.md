Chameleon System CmsStringUtilitiesBundle
=========================================

The CmsStringUtilitiesBundle provides common string and array utility services for Chameleon System projects. 
It includes services for comparing arrays, manipulating URLs, and legacy variable injection helpers.

Services
--------
The following Symfony service IDs are provided by this bundle:

- `chameleon_system_cms_string_utilities.array_utility_service`
  - Implements `ChameleonSystem\CmsStringUtilitiesBundle\Interfaces\ArrayUtilityServiceInterface`
  - Compares two arrays for deep, order-insensitive equality.

- `chameleon_system_cms_string_utilities.url_utility_service`
  - Implements `ChameleonSystem\CmsStringUtilitiesBundle\Interfaces\UrlUtilityServiceInterface`
  - Adds or merges query parameters into URLs.

- `chameleon_system_core.variable_injection`
  - Legacy helper service for injecting variables into templates and content.

ArrayUtilityService
-------------------
ID: `chameleon_system_cms_string_utilities.array_utility_service`
Interface: `ChameleonSystem\CmsStringUtilitiesBundle\Interfaces\ArrayUtilityServiceInterface`

**Methods**
- `equal(array $array1, array $array2): bool`  Compare two arrays for deep equality (order-insensitive).


**Usage in code**
```php
use ChameleonSystem\CmsStringUtilitiesBundle\Interfaces\ArrayUtilityServiceInterface;

class MyService
{
    private $arrayUtil;

    public function __construct(ArrayUtilityServiceInterface $arrayUtil)
    {
        $this->arrayUtil = $arrayUtil;
    }

    public function compareArrays(array $a, array $b): bool
    {
        return $this->arrayUtil->equal($a, $b);
    }
}
```

UrlUtilityService
-----------------
ID: `chameleon_system_cms_string_utilities.url_utility_service`
Interface: `ChameleonSystem\CmsStringUtilitiesBundle\Interfaces\UrlUtilityServiceInterface`

**Methods**
- `addParameterToUrl(string $url, array $parameter): string`  Merge or add query parameters to a URL.


**Usage in code**
```php
use ChameleonSystem\CmsStringUtilitiesBundle\Interfaces\UrlUtilityServiceInterface;

class MyService
{
    private $urlUtil;

    public function __construct(UrlUtilityServiceInterface $urlUtil)
    {
        $this->urlUtil = $urlUtil;
    }

    public function buildUrl(string $baseUrl, array $params): string
    {
        return $this->urlUtil->addParameterToUrl($baseUrl, $params);
    }
}
```

Legacy Object Helpers
---------------------
For backwards compatibility, several legacy helper classes are available under the `objects/` directory (e.g. `TPkgCmsStringUtilities_PathUtils`, `TPkgCmsStringUtilities_HTML`, etc.).

Tests
-----
PHPUnit tests are provided under the `Tests/` directory to validate service behavior.

License
-------
This bundle is released under the same license as the Chameleon System.
