# ChameleonSystem GoogleAnalyticsBundle

## Opt-out/Opt-in

When using Universal Analytics, it is possible to allow the user to opt-out from analytics. To do this, add the following HTML snippet to the page:

```html
<div class="span4 ga-optout-optin-link" style="display: none">
    <a class="ga-optout-link"><?= $translator->trans('chameleon_system_theme_shop_standard.google_analytics.opt_out') ?></a>
    <a class="ga-optin-link"><?= $translator->trans('chameleon_system_theme_shop_standard.google_analytics.opt_in') ?></a>
</div>
```

Of course the snippet may be modified, but it is important that the "ga-*" classes are assigned to the <a> tags and that they are enclosed by an element with the "ga-optout-optin-link" class. Invisibility by default is recommended for the case that the tracker is deactivated.