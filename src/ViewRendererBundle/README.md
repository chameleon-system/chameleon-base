Chameleon System ViewRendererBundle
===================================

Overview
--------
The ViewRendererBundle is part ofthe core of the Chameleon System frontend module architecture. 
It provides:
- A Twig-based snippet and module rendering engine with support for named blocks, variable substitution, and template inheritance.
- A mapper pipeline that transforms domain objects into simple data structures consumed by templates.
- Unified handling of CSS, LESS, and JavaScript resources declared alongside templates.
- Dummy data support for offline snippet preview and development.
- Caching triggers for mapped data to enable efficient fragment caching.

Key Features
------------
- **Template Rendering**: Render from strings, files, or CMS module definitions via `ViewRenderer`.
- **Twig Integration**: Leverage Twig DSL (loop, conditionals, filters, includes, extends) for templates.
- **Mapper Architecture**: Define data requirements (`GetRequirements`) and mapping logic (`Accept`) in reusable mappers.
- **Resource Injection**: Declare `css`, `less`, `js`, and `include` sections in `config.yml` files for snippet packages.
- **Dummy Data**: Place `<snippet>.dummy.php` alongside templates for design-time data injection.
- **Module Creation**: Build composite modules by combining mappers and a template under `MTPkgViewRendererAbstractModuleMapper`.
- **Console Command**: `chameleon_system:less:compile` compiles LESS per portal with optional minification.
- **URL Sanitation**: `sanitizeurl` Twig filter prevents unsafe `javascript:` and `data:` URLs.
- **Legacy Support**: `TPkgSnippetRendererLegacy` bridges to the old `TViewParser` system.

Installation
------------
This bundle is shipped with the `chameleon-system/chameleon-base` package and installed already.  
To register manually (no Flex), add to `app/AppKernel.php`:
```php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = [
        // ...
        new ChameleonSystem\ViewRendererBundle\ChameleonSystemViewRendererBundle(),
    ];
    return $bundles;
}
```
Clear the cache:
```bash
php bin/console cache:clear
```

Usage
-----
**1) Render a Twig snippet from a string**
```php
use ChameleonSystem\ViewRendererBundle\interfaces\IPkgSnippetRenderer;

$template = '<h1>[{ block title }]Hello[{ endblock }]</h1>';
$renderer = IPkgSnippetRenderer::GetNewInstance($template, IPkgSnippetRenderer::SOURCE_TYPE_STRING);
$renderer->setVar('title', 'World');
$output = $renderer->render();
```

### Dummy Data

Snippets can be populated with dummy data for testing.

To define dummy data, place a file named `<snippetname>.dummy.php` in the same folder as the snippet:

```php
$foo = array(
  'title' => 'dummytitle'
);

return $foo;
```

**3) Create a custom module**
- Extend `MTPkgViewRendererAbstractModuleMapper`, inject source objects, and tag your mapper with `chameleon_system.mapper`.
- Configure your module in the Table Editor with snippet path and mapper service.

**4) Compile LESS assets**
```bash
php bin/console chameleon_system:less:compile [--minify-css]
```

Configuration
-------------
No additional YAML configuration is required.  
Declare snippet package resources in `config.yml` files alongside your templates:
```yaml
css:
  - /assets/snippets/mySnippet/style.css
less:
  - /assets/snippets/mySnippet/style.less
js:
  - /assets/snippets/mySnippet/script.js
include:
  - common/header
  - pkgArticleList
  - common/list
```

`less`, `css`, and `js` define resources relative to the webroot.

The `include` section loads resources from other packages.

#### Including Resources in Legacy Modules

Legacy modules must manually include resources using `getResourcesForSnippetPackage` in `TUserModelBaseCore`, typically inside `GetHtmlHeadIncludes()`:

```php
public function GetHtmlHeadIncludes()
{
    $aIncludes = parent::GetHtmlHeadIncludes();
    $aIncludes = array_merge($aIncludes, $this->getResourcesForSnippetPackage('userInput/form'));
    $aIncludes = array_merge($aIncludes, $this->getResourcesForSnippetPackage('textBlock'));

    return $aIncludes;
}
```

Extensibility
-------------
- **Mappers**: Tag services with `chameleon_system.mapper` to participate in the mapping chain.
- **Custom Engines**: Implement `IPkgSnippetRenderer` and register as a service to support non-Twig engines.
- **Resource Handling**: Provide a custom `IResourceHandler` to override CSS/JS aggregation.
- **Twig Extensions**: Add filters, functions, or node visitors in `Twig` directory and tag with `twig.extension`.
- **Event Hooks**: Subscribe to `chameleon_system.viewrenderer.post_render` for additional HTML transformations.
  
### URL Sanitation
Twig automatically escapes output for HTML contexts, but unsafe URI schemes (`javascript:`, `data:`) may still appear in attributes. Use the `sanitizeurl` Twig filter to sanitize URLs:
```twig
{% include 'pkgGreeting/introduction.html.twig' %}
```

Use `with` to pass additional data:

```twig
{% include 'foo.html.twig' with {'bar': 'baz'} %}
```

### Inheritance

Snippets can inherit from others using `extends` and `block`:

#### baseteaser.html.twig
```twig
<div class="teaser">
  <img src="{{ teaserimage }}" />
  {% block teasercontent %}---here is the content---{% endblock %}
</div>
```

#### articleteaser.html.twig
```twig
{% extends "baseteaser.html.twig" %}
{% block teasercontent %}
<span class="intro">{{ content }}</span>
{% endblock %}
```

#### hugearticleteaser.html.twig
```twig
{% extends "baseteaser.html.twig" %}
{% block teasercontent %}
<h1>title</h1>
<span class="huge intro">{{ content }}</span>
{% endblock %}
```

### Storage Locations

Snippets reside in one of three folders: Customer > Custom-Core > Core. Higher-level snippets override lower ones.

Each override requires its own dummy data.

Use logical structures like `lists`, `boxes`, `pkgXYZ`.

### Snippet Gallery

Chameleon includes a module listing and rendering all snippets (with dummy data, if available).

## The Mappers

Mappers transform data into a structure expected by the snippets. They extend `AbstractViewMapper`.

Instead of providing a `TShopArticle`, the snippet gets mapped values like title, image, description.

Different mappers can extract similar data from different source objects.

### Mapper Chains

Multiple mappers can be chained:

```text
Module -> ArticleMapper -> UserMapper -> Snippet
```

### Implementation

Each mapper must implement `GetRequirements` and `Accept`.

#### GetRequirements

Define source objects:

```php
$oRequirements->NeedsSourceObject("oShopArticle", "TShopArticle", null);
```

#### Accept

Perform mapping:

```php
$oArticle = $oVisitor->GetSourceObject("oShopArticle");
$oVisitor->SetMappedValue("title", $oArticle->GetName());
```

### Caching

Use `IMapperCacheTriggerRestricted` to register triggers:

```php
$oCacheTriggerManager->addTrigger("shop", $sShopId);
$oCacheTriggerManager->addTrigger("shop_article", $sArticleId);
```

Check `bCachingEnabled` before adding triggers to save resources.

## The ViewRenderer

`ViewRenderer` connects mappers and views. It's the main interface for rendering snippets.

New modules use this automatically. Manual use is also possible:

```php
$oViewRenderer = new ViewRenderer();
$oViewRenderer->AddSourceObject("oShopArticle", $oShopArticle);
$oViewRenderer->AddMapper(new ArticleToContentMapper());
$renderedHTML = $oViewRenderer->Render("article/article_detail.html.twig");
```
The filter mimics the behavior of `escape`, replacing disallowed schemes with `#`. Options match the contexts supported by `escape` (e.g., `'html'`, `'js'`).

## Creating a New Module

A module is a specialized mapper extending `MTPkgViewRendererAbstractModuleMapper`, and creates its own source objects.

Its `Accept` method is similar to `Execute` in older modules.

### Example: Wishlist Module

#### The Snippet: `noticelist.html.twig`

```twig
<div class="wishlist">
  <span class="title">
    {% trans with {"username":username} %}
      The wishlist of [{username}]:
    {% endtrans %}
  </span>
  <ul>
    {% for article in articles %}
      <li><a href="{{ article.link }}">{{ article.title }}</a></li>
    {% endfor %}
  </ul>
</div>
```

#### Dummy Data: `noticelist.dummy.php`

```php
$dummyData = array(
    "username" => "Dummy User",
    "articles" => array(
        array("link" => "#", "title" => "Dummy Article 1"),
        array("link" => "#", "title" => "Dummy Article 2")
    )
);

return $dummyData;
```

#### The Mapper: `TDataExtranetUser_to_Name_and_NoticeList_Mapper`

```php
class TDataExtranetUser_to_Name_and_NoticeList_Mapper extends AbstractViewMapper
{
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements)
    {
        $oRequirements->NeedsSourceObject("oExtranetUser", "TdbDataExtranetUser");
    }

    public function Accept(IMapperVisitorRestricted $oVisitor, $bCachingEnabled, IMapperCacheTriggerRestricted $oCacheTriggerManager)
    {
        $oExtranetUser = $oVisitor->GetSourceObject("oExtranetUser");
        $oVisitor->SetMappedValue("username", $oExtranetUser->fieldName);

        $oList = $oExtranetUser->GetFieldShopUserNoticeListList();
        $aArticles = array();

        while ($oItem = $oList->Next()) {
            $oArticle = TShopArticle::GetNewInstance($oItem->fieldShopArticleId);
            $aArticles[] = array(
                "title" => $oArticle->GetName(),
                "link" => $oArticle->GetDetailLink()
            );
        }

        $oVisitor->SetMappedValue("articles", $aArticles);
    }
}
```

Mappers should be services and need the tag `chameleon_system.mapper`.

Example:

```xml
<service id="chameleon_system_chameleon_shop_theme.mapper.schema_org_product" class="ChameleonSystem\ChameleonShopThemeBundle\Bridge\Chameleon\Mapper\SchemaOrgProductMapper">
    <tag name="chameleon_system.mapper"/>
</service>
```

#### The Module: `PkgExtranetNoticeListModule`

```php
class PkgExtranetNoticeListModule extends MTPkgViewRendererAbstractModuleMapper
{
    public function Accept(IMapperVisitorRestricted $oVisitor, $bCachingEnabled, IMapperCacheTriggerRestricted $oCacheTriggerManager)
    {
        $oActiveUser = TDataExtranetUser::GetInstance();
        $oVisitor->SetMappedValue("oExtranetUser", $oActiveUser);
    }
}
```

#### Backend Configuration

Create the module under `Template Module` with:

```
standard=pkgExtranet/DataExtranetUser/noticelist.html.twig;TDataExtranetUser_to_Name_and_NoticeList_Mapper
```

---

#### URL Sanitation

URLs are automatically sanitized by Twig (depending on the context, the filters escape('html_attr') or escape('js) need
to be used). This sanitation does however NOT include protection from malicious "javascript:" and "data:" URLs. To
protect against these URLs, use the `sanitizeurl` filter. This filter mimics the standard Twig `escape` filter and
replaces "javascript:" and "data:" URLs with a "#". Options for this filter are exactly the same as for `escape` (e.g.
use `|sanitizeurl("html_attr")` in HTML attributes.

This filter should be used for all URLs that are provided by the user (for persisted user content as well as GET and
POST parameters).


[^1]: http://twig.sensiolabs.org/
[^2]: http://fabien.potencier.org/
[^3]: http://symfony.com/
[^5]: http://twig.sensiolabs.org/doc/tags/include.html

