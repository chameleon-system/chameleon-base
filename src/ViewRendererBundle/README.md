# Chameleon System ViewRendererBundle

## Chameleon View Renderer

As of Chameleon 4, a new system for creating frontend modules replaces the existing one. As part of this change, the way templates are created and managed has also been revised.

The new system uses Twig[^1] as the default template engine. However, the architecture allows for additional template systems to be added in the future.

## Twig

Twig is a template system developed by Fabien Potencier[^2], the creator of Symfony[^3].

Twig has its own domain-specific language (DSL), which allows view logic like loops and conditionals to be implemented directly in the template.

Direct object handling (e.g., `TShopArticle`) no longer occurs in the template. Instead, the template expects pre-prepared data (e.g., title, price, description). It is irrelevant to the template where this data comes from. This is handled by mappers.

### Inserting Values

Values in Twig templates are wrapped in double curly braces and replaced during the rendering process:

```html
<h1>Hello, {{ name }}.</h1>
```

### Filters

Inserted values can be passed through filters using the pipe (`|`) character. Filters can be chained together.

A full list can be found at [Twig Filters Documentation](http://twig.sensiolabs.org/doc/filters/index.html).

Some useful filters:

#### raw

By default, all values are escaped before output. This can be disabled using the `raw` filter—useful for HTML entities:

```html
<div>{{ dropdown_element|raw }}</div>
```

#### default

The `default` filter lets designers specify fallback values:

```twig
{{ greeting|default('Why hello there') }} good sir.
```

#### trans

The `trans` filter is implemented by Chameleon and internally uses `TGlobal::Translate`:

```twig
{{ message|trans }}
```

`trans` is typically used as a tag for better readability:

```twig
{% trans %}Why hello good sir.{% endtrans %}
```

It also supports placeholders:

```twig
{% trans with {'name': name} %}
Why hello good sir. People call me [{name}].
{% endtrans %}
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

### Including CSS/JS/LESS

Snippet packages can include their own CSS, LESS, and JS files via a `config.yml` file in the snippet folder:

```yaml
less:
  - /assets/snippets/shopFilter/shopFilterItem.less
  - /assets/snippets/shopFilter/shopFilter.less
css:
  - /assets/snippets/shopFilter/shopFilterItem.css
js:
  - /assets/snippets/shopFilter/shopFilterItem.js
include:
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

### Includes

Snippets can include other snippets using the `include` tag[^5]:

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

## Creating a New Module

A module is a specialized mapper extending `MTPkgViewRendererAbstractModuleMapper`, and creates its own source objects.

Its `Accept` method is similar to `Execute` in older modules.

### Example: Wishlist Module

#### The Snippet: `noticelist.html.twig`

```twig
<div class="wishlist">
  <span class="title">
    {% trans with {"username":username} %}
      Der Merkzettel von [{username}]:
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

