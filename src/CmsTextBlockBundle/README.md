Chameleon System CmsTextBlockBundle
===================================

The CmsTextBlockBundle provides reusable text block functionality for Chameleon System projects.
It allows you to define and render content blocks (text and headlines) via a unique system name.

Services
--------
The following Symfony service IDs are provided by this bundle:

- **chameleon_system_cms_text_block.text_block_lookup**
  - Implements `ChameleonSystem\CmsTextBlockBundle\Interfaces\TextBlockLookupInterface`
  - Retrieves and renders text block content and headlines.
  - Methods:
    - `getRenderedText(string $systemName, int $textContainerWidth = 1200, array $placeholders = []): string`
    - `getHeadline(string $systemName): string`
    - `getTextBlock(string $systemName): TdbPkgCmsTextBlock|null`

- **chameleon_system_cms_text_block.mapper.get_text**
  - Class `TPkgCmsTextBlockMapper_GetText`
  - Tagged with `chameleon_system.mapper` for view mapping.
  - Requirements:
    - `name` (string): system name of the text block.
    - `maxwidth` (int, default 600): maximum width for HTML rendering.
  - Provides mapped values:
    - `title`: block headline.
    - `text`: block content (with width constraint).

Examples
--------
Inject the TextBlockLookup service in Symfony (XML):

```xml
<service id="App\Service\ContentRenderer" class="App\Service\ContentRenderer">
    <argument type="service" id="chameleon_system_cms_text_block.text_block_lookup"/>
</service>
```

Usage in PHP:

```php
use ChameleonSystem\CmsTextBlockBundle\Interfaces\TextBlockLookupInterface;

class ContentRenderer
{
    private TextBlockLookupInterface $lookup;

    public function __construct(TextBlockLookupInterface $lookup)
    {
        $this->lookup = $lookup;
    }

    public function renderBlock(string $name): string
    {
        return $this->lookup->getRenderedText($name);
    }
}
```

Tests
-----
PHPUnit tests are available under the `Tests/` directory.

License
-------
This bundle is released under the same license as the Chameleon System.