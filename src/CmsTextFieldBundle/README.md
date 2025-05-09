Chameleon System CmsTextFieldBundle
===================================

Installation
------------
Note: The bundle is already registered with Chameleon System by default.

Install via Composer:

    composer require chameleon-system/chameleon-base

Bundle Registration
-------------------
For Symfony Flex (4+) the bundle is auto-registered.
For earlier Symfony versions or without Flex, add it to your AppKernel:

    // app/AppKernel.php
    public function registerBundles()
    {
        $bundles = [
            // ...
            new ChameleonSystem\CmsTextFieldBundle\ChameleonSystemCmsTextFieldBundle(),
        ];
        return $bundles;
    }

Configuration
-------------
The following configuration is supported (default values shown):

```yaml
chameleon_system_cms_text_field:
  # If false, <script> tags are stripped for security
  allow_script_tags: false
```

Usage
-----
Process WYSIWYG content by instantiating the endpoint and calling `GetText`:

```php
use TCMSTextFieldEndPoint;

$endpoint = new TCMSTextFieldEndPoint($rawHtmlContent);
$rendered = $endpoint->GetText(
    $thumbnailWidth = 600,
    $includeClearDiv = true, // deprecated
    $aCustomVars = [],
    $imageGroup = 'lightbox',
    $effects = []
);
echo $rendered;
```

Key Classes
-----------
- `TCMSTextFieldEndPoint`  Main processor for WYSIWYG fields.
- `TPkgCmsTextfieldImage`  View mapper handling image thumbnails and responsive sources.
- Config parameter `chameleon_system_cms_text_field.allow_script_tags` controls script tag stripping.

Features
--------

* Adds the class 'cmsLinkSurroundsImage' to all links that enclose an image tag to allow custom styling or javascript 
  event handling.
* Adds the class 'external' to all links linking to an offsite target to allow special styling.
* Adds the class 'cmsanchor' to all anchors.
* Replaces all mailto: links with an obfuscating JavaScript to prevent bot indexing.
* Renders Chameleon internal page links as SEO URL.
* Renders 3 types of Chameleon document links, <span> based and placeholder based '[{123...}]'.
* Replaces invalid DIVs that where added by the old WYSWIYGPro under some circumstances.
* Replaces empty align properties with 'bottom'.
* Parses image tags and renders images or external video embed codes.
* Removes \<script\> tags (disabled by default).

pkgCmsTextblock
---------------

If the package pkgCmsTextblock is installed, it will use the hook: _ReplaceCmsTextBlockInString to replace placeholders 
in format [{fooBar}].
