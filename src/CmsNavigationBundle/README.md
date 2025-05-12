Chameleon System CmsNavigationBundle
=====================================

The **CmsNavigationBundle** delivers a comprehensive solution for rendering navigational menus in Chameleon CMS. It provides:
- Web-modules for primary navigation and subnavigation endpoints.
- View mappers for language selection and login/logout items.
- A node API (`TPkgCmsNavigationNode`) to build and traverse navigation trees.
- Built-in caching, ACL, and template mapping support.

Table of Contents
-----------------
- [Overview](#overview)
- [Bundle Registration](#bundle-registration)
- [Module Usage](#module-usage)
  - [Primary Navigation](#primary-navigation)
  - [Subnavigation](#subnavigation)
- [View Mappers](#view-mappers)
  - [Language Selection](#language-selection)
  - [Login/Logout Mapper](#loginlogout-mapper)
- [Node API](#node-api)
- [Twig Templates](#twig-templates)
- [Caching & ACL](#caching--acl)
- [Extensions & Custom Mappers](#extensions--custom-mappers)

## Overview

Use **CmsNavigationBundle** to render dynamic menus based on the portal’s navigation trees (`cms_tree`, `cms_tree_node`). You can insert navigation modules into pagedefs, configure Twig templates, and leverage view mappers for common UI elements.

## Module Usage 

Add a module to your static page template configuration (`cms_tpl_module`) of type **CMS Navigation** or **CMS Sub Navigation**.

### Primary Navigation
- **classname**: `MTPkgCmsNavigation`
- **view_mapper_config**: e.g. `standard=/common/navigation/standard.html.twig`
- **view_mapping**: Optional custom paths.
- **cms_usergroup_mlt**, **cms_portal_mlt**: Restrict to user groups or portals.

### Subnavigation
- **classname**: `MTPkgCmsSubNavigation`
- **view_mapper_config**: e.g. `standard=/common/navigation/subnavigation.html.twig`

Both modules inject an `aTree` variable (array of `TPkgCmsNavigationNode`) into Twig.

## View Mappers

### Language Selection
Mapper: `TPkgCmsNavigation_LanguageSelection`
- Requires source `aTree` (array)
- Appends a dropdown of available languages to the navigation tree.

### Login/Logout Mapper
Mapper: `TPkgCmsNavigation_LoginMapper`
- Requires `aTree`
- Adds login/logout links and user-group-aware items.

## Node API

All navigation data is represented by `TPkgCmsNavigationNode`:
```php
$node = new TPkgCmsNavigationNode();
$node->id                    // record ID
$node->sLink                 // URL
$node->sTitle                // link text
$node->sSeoTitle             // SEO title
$node->sNavigationIconClass  // CSS icon class
$node->sRel                  // link rel attribute
$node->bIsActive             // active flag
$node->setChildren(array $children) // nested nodes
```

## Twig Templates

Render `aTree` recursively:
```twig
<ul>
  {% for node in aTree %}
    <li class="{% if node.bIsActive %}active{% endif %}">
      <a href="{{ node.sLink }}" title="{{ node.sSeoTitle }}">
        {{ node.sTitle }}
      </a>
      {% if node.getChildren()|length > 0 %}
        {# include sub-template or recurse #}
      {% endif %}
    </li>
  {% endfor %}
</ul>
```

## Caching & ACL

- Primary/Subnavigation endpoints use `IMapperCacheTriggerRestricted` to add triggers on:
  - `cms_tree_node`, `cms_tree`, `cms_tpl_page`
  - Active page, user groups, login state
- Ensure correct `NeedsSourceObject` in your mapper.

## Extensions & Custom Mappers

To add custom navigation behavior, extend `AbstractViewMapper`, implement `GetRequirements()` and `Accept()`, then reference your mapper in `view_mapper_config`:
```
customMapper=MyBundle\Mapper\MyCustomNavigationMapper
```
