Chameleon System ExternalTrackerGoogleAnalyticsBundle
=====================================================

Overview
--------
This bundle extends the ExternalTracker framework to output Google Analytics tracking code (Universal Analytics or GA4) based on events recorded in the `pkg_external_tracker` table. It lets you manage tracking purely via CMS configuration, without adding code to templates.
It does not support consent management without an additional consent bundle.

Features
--------
- **Google Analytics 4 (GA4)**
  - Injects `gtag.js` snippet with `gtag('config', …)`
  - Sends ecommerce events (`add_to_cart`, `purchase`) via `gtag('event', …)`
  - Supports `debug_mode` flag for debug view

Installation
------------
This bundle is included in the `chameleon-system/chameleon-base` metapackage. No further Composer commands are needed.

Bundle Registration
-------------------
Symfony Flex (4+) auto-registers bundles. Without Symfony Flex, add it to `app/AppKernel.php` manually:

```php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = [
        // …
        new ChameleonSystem\\ExternalTrackerGoogleAnalyticsBundle\\ChameleonSystemExternalTrackerGoogleAnalyticsBundle(),
    ];
    return $bundles;
}
```

Configuration
-------------
Create one or more tracker entries in the CMS:

1. In table `pkg_external_tracker`, add a new record:
   - **type**: `class`
   - **resource**: fully-qualified class name:
     - For GA4:
       `ChameleonSystem\\ExternalTrackerGoogleAnalyticsBundle\\Bridge\\Chameleon\\ExternalTracker\\ExternalTrackerGoogleAnalyticsGa4`
   - **identifier**: your GA Tracking ID (e.g. `UA-XXXXX-Y` or `G-XXXXXXXX`)
   - **active**: `1`
2. Assign the record to desired portals/languages via the MLT field.

The GA4 tracker should already be in the database. You just need to add your GA Tracking ID to it.

Event Handling
--------------
Both GA implementations read recorded events via `TPkgExternalTrackerState`:
- **Add to Cart** and **Remove from Cart** events
- **Purchase** events with full ecommerce data
GA4 formatter sends JSON payloads to `gtag('event', …)`

License
-------
This bundle is released under the same license as the Chameleon System.
