Chameleon System MarkdownCmsBundle
==================================

Overview
--------
The MarkdownCmsBundle integrates a Markdown editor and parser into the Chameleon System backend and frontend.
Key features:
- **Toast UI Markdown Editor** in backend record views and lookups, with toolbar and live preview.
- **Markdown-Text** field type (`CMSFIELD_MARKDOWNTEXT`) for multi-line text columns.
- Twig filter `markdown` for converting Markdown to safe HTML (`{{ content|markdown|raw }}`).
- CMS link parsing for `[Title](table|id)` syntax, resolving articles, pages, products, and documents.
- Extension on `cms_document` to generate proper download filenames including file extensions.

Installation
------------
- Bundle is included in `chameleon-system/chameleon-base`; no composer require needed.
- Register in `app/AppKernel.php` (if not using Flex auto-registration):
  ```php
  // app/AppKernel.php
  public function registerBundles()
  {
      $bundles = [
          // ...
          new ChameleonSystem\MarkdownCmsBundle\ChameleonSystemMarkdownCmsBundle(),
      ];
      return $bundles;
  }
  ```
- Run the **CMS update** to install the new field type and snippet chain.
- Install and publish assets:
  ```bash
  php bin/console assets:install --symlink
  ```
- Clear cache:
  ```bash
  php bin/console cache:clear
  ```

Assets: Toast UI Editor
-----------------------
Bundled Toast UI assets are under `Resources/public/toastuimarkdowneditor`:
- `toastui-editor-all.min.js`, CSS, plugins, and i18n files (`de-de.min.js`, etc.).

To update:
1. Download the latest assets from https://uicdn.toast.com/editor/latest/.
2. Copy into `Resources/public/toastuimarkdowneditor`.

Field Type: Markdown-Text
-------------------------
To use the editor on a field:
1. In the Table Editor, change the field type of the desired field to the type `Markdown-Text`.
2. Save; the UI will render a rich Markdown editor instead of a plain textarea.

Twig Filter: `markdown`
-----------------------
Convert Markdown to HTML in Twig:
```twig
{{ record.fieldMarkdown | markdown | raw }}
```

Programmatic API
-----------------
Inject `chameleon_system_markdown_cms.markdown_parser_service` (implements `MarkdownParserServiceInterface`):
```php
use ChameleonSystem\MarkdownCmsBundle\Bridge\Chameleon\Service\MarkdownParserServiceInterface;

class MyService {
    public function __construct(private MarkdownParserServiceInterface $parser) {}

    public function render(string $md): string
    {
        return $parser->parse($md);
    }
}
```

Link Parsing
------------
Use CMS link syntax in Markdown:
```
[Article](pkg_article|<id>)
[Page](cms_tpl_page|<id>)
[Product](shop_article|<id>)
[Document](cms_document|<id>)
```
Parser rewrites these to valid URLs; document links open in a new tab.

Document Download Filenames
---------------------------
`cms_document` is extended by `DocumentFileDownload` to append file extensions to download names.

Logging & Errors
----------------
Broken or missing links fall back to the portal’s 404 page URL. No special logging is required.

License
-------
This bundle is released under the same license as the Chameleon System.