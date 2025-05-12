Chameleon System CookieConsentBundle
====================================

Overview
--------
This bundle provides a basic cookie notice banner for your site. It displays a short message informing users about cookie usage, an accept button, and an optional "Learn more" link to your privacy policy system page. No advanced consent management (such as category-based preferences) is included.

Note: If you use any third party service or analytics tracking you will need a separate bundle with cookie consent handling for that.

## Installation

### Step 1: Enable the Bundle

Enable the bundle by adding the following line in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...

public function registerBundles()
{
    $bundles = array(
        // ...
       new \ChameleonSystem\CookieConsentBundle\ChameleonSystemCookieConsentBundle(),
    );
}
```

It might be required to run `app/console assets:install --symlink --relative web` afterwards to create the symlink to the asset directory.

### Step 2: Configure the Bundle

The bundle uses a Chameleon system page to display the privacy policy when the user clicks the "more" button.
By default, the system page "privacy-policy" is used, but this can be configured (see below). Assure that this system
page exists.

**Configuration:**

```yaml
chameleon_system_cookie_consent:
    position: "bottom" # banner position: 'top' or 'bottom'
    theme: "classic" # banner style (e.g. 'classic', 'edgeless')
    bg_color: "#363636" # text background color
    button_bg_color: "#46a546" # button background color
    button_text_color: "#ffffff" # button text color
    privacy_policy_system_page_name: "privacy-policy" # system page to display on button click
```

For further customization, create `snippets/CookieConsent/footer.html.twig` in your theme.

To change styles, overwrite `snippets/CookieConsent/header.html.twig` and link to your stylesheet, or remove the link
completely if you add the styles in a LESS or SCSS file.

    The text translations may be overwritten by adding translation files with the same keys in `app/Resources/translations`.
    See: [https://symfony.com/doc/2.0/cookbook/bundles/override.html](https://symfony.com/doc/2.0/cookbook/bundles/override.html)

Services
--------
- **chameleon_system_cookie_consent.add_cookie_consent_includes_listener**
  - Injects banner markup via events:
    - `chameleon_system_core.html_includes.header` → `onGlobalHtmlHeaderInclude`
    - `chameleon_system_core.html_includes.footer` → `onGlobalHtmlFooterInclude`
  - Class: `ChameleonSystem\\CookieConsentBundle\\EventListener\\AddCookieConsentIncludesListener`

Twig Template Overrides
-----------------------
Customize Twig snippets by placing overrides in your theme:
```
private/framework/snippets/CookieConsent/header.html.twig
private/framework/snippets/CookieConsent/footer.html.twig
```

Key Classes
-----------
- `AddCookieConsentIncludesListener`: core listener injecting banner includes.
- `Configuration`: defines the configuration tree.
- `ChameleonSystemCookieConsentExtension`: loads services and applies configuration.
- `Resources/config/services.xml`: service definitions.

Usage
-----
1. Ensure a CMS system page exists for your privacy policy (default: `privacy-policy`).
2. The bundle will render a "Learn More" link to that page.
3. Customize styles via Twig overrides or add custom CSS.

License
-------
This bundle is released under the same license as the Chameleon System.