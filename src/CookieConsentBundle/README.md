# Chameleon System CookieConsentBundle

This bundle integrates the Cookie Consent javascript plugin from [https://cookieconsent.insites.com/](https://cookieconsent.insites.com/).

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

It might be required to run `composer symfony-scripts` afterwards to create the symlink to the asset directory.

### Step 2: Configure the Bundle

The bundle uses a Chameleon system page to display the privacy policy when the user clicks the "more" button.
By default, the system page "privacy-policy" is used, but this can be configured (see below). Assure that this system
page exists.

**Configuration:**

```yaml
chameleon_system_cookie_consent:
    position: "bottom" # position of the consent text
    theme: "classic" # theme (see the documentation of the cookie consent plugin)
    bg_color: "#363636" # text background color
    button_bg_color: "#46a546" # button background color
    button_text_color: "#ffffff" # button text color
    privacy_policy_system_page_name: "privacy-policy" # system page to display on button click
```

For further customization, create `snippets/CookieConsent/footer.html.twig` in the theme.

To change styles, overwrite `snippets/CookieConsent/header.html.twig` and link to your stylesheet, or remove the link
completely if you add the styles in a LESS or SCSS file.

The text translations may be overwritten by adding translation files with the same keys in `app/Resources/translations`.
See: [https://symfony.com/doc/2.0/cookbook/bundles/override.html](https://symfony.com/doc/2.0/cookbook/bundles/override.html)