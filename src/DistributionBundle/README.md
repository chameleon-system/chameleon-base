Chameleon System DistributionBundle
===================================

Overview
--------
The DistributionBundle provides essential bootstrap commands and version management utilities for Chameleon System. It enables initial backend user creation and post-update version consistency checks across Chameleon packages.

Installation
------------
This bundle is included in the `chameleon-system/chameleon-base` package. No additional installation steps are required beyond installing `chameleon-system/chameleon-base` via Composer.

Bundle Registration
-------------------
This bundle is part of the `chameleon-system/chameleon-base` package.

Symfony Flex (4+) auto-registers bundles. Without Symfony Flex, add it to `app/AppKernel.php` manually:

```php
public function registerBundles()
{
    $bundles = [
        // ...
        new ChameleonSystem\\DistributionBundle\\ChameleonSystemDistributionBundle(),
    ];
    return $bundles;
}
```

Features
--------
* **Initial Backend User Creation**
  - Console command `chameleon_system:bootstrap:create_initial_backend_user`.
  - Interactive or environment-driven setup of the first admin user.
  - Updates existing `cms_user` references to use the new user ID.
* **Version Consistency Check**
  - Composer script hook `PostUpdateVersionCheck::checkVersion` to compare installed Chameleon packages.
  - Reports major or minor version mismatches between `chameleon-base` and other Chameleon packages.

Services & Commands
-------------------
1. **CreateInitialBackendUserCommand** (`chameleon_system:bootstrap:create_initial_backend_user`)
   - Class: `ChameleonSystem\\DistributionBundle\\Command\\CreateInitialBackendUserCommand`
   - Prompts for username/password (or uses `APP_INITIAL_BACKEND_USER_NAME` and `APP_INITIAL_BACKEND_USER_PASSWORD`).
2. **InitialBackendUserCreator**
   - Class: `ChameleonSystem\\DistributionBundle\\Bootstrap\\InitialBackendUserCreator`
   - Handles DB insertion, password hashing, and linking to languages/portals.
3. **PostUpdateVersionCheck**
   - Class: `ChameleonSystem\\DistributionBundle\\VersionCheck\\PostUpdateVersionCheck`
   - Filters and matches package versions using `ChameleonVersion` and `ChameleonPackageFilter`.

Composer Integration
--------------------
To enable version checks automatically, add to your project's `composer.json`:

```json
"scripts": {
  "post-update-cmd": [
    "ChameleonSystem\\DistributionBundle\\VersionCheck\\PostUpdateVersionCheck::checkVersion"
  ]
}
```

Extending
---------
* Subclass `InitialBackendUserCreator` for custom user defaults.
* Implement additional Composer script hooks for other lifecycle events.

License
-------
This bundle is released under the same license as the Chameleon System.
