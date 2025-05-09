Chameleon System CommentBundle
==============================

Overview
--------
The CommentBundle enables frontend user comments on any CMS‐managed object. It provides:

- A comment posting and listing module
- Configurable comment types
- Reporting and moderation features
- Email notifications for new and reported comments

Installation
------------

Note: The bundle is already registered with Chameleon System by default.

Install via Composer:

    composer require chameleon-system/chameleon-base

Bundle Registration
-------------------
For Symfony Flex (4+) the bundle is auto-registered.
For earlier Symfony versions or without Flex, add it to your AppKernel:

```php
public function registerBundles()
{
    $bundles = [
        // ...
        new ChameleonSystem\CommentBundle\ChameleonSystemCommentBundle(),
    ];
    return $bundles;
}
```

## Copy Views

To override frontend templates, copy the provided views into your project:

```bash
cp -R vendor/chameleon-system/chameleon-base/src/CommentBundle/install/tocopy/private/framework/modules/MTPkgComment/views \
       private/framework/modules/MTPkgComment/views

cp -R vendor/chameleon-system/chameleon-base/src/CommentBundle/install/tocopy/private/extensions/library/classes/pkgComment/views/db \
       private/extensions/library/classes/pkgComment/views/db
```

## Creating a Comment Module

1. Use the provided `MTPkgComment` (in `install/tocopy`) or subclass `MTPkgCommentCore`.
2. Register the module in the CMS backend and assign views for commenting and reporting.

## Defining Comment Types

Each comment type binds comments to a specific CMS object:

1. Create a class extending `TPkgCommentType` and override `GetActiveItem()`.
2. Add a record in the `pkg_comment_type` table with `className` = your PHP class.

## Configuring Comment Modules

Use pkg_comment_module_config records to configure:

* `comment_type`: select the comment type
* `moderation`: require admin approval
* `notification_email`: email address for notifications
* `per_page`: comments per page
* `system_page_announce`: page for handling reports

## Reporting Comments

1. Create or designate a frontend page with the reporting view.
2. In the backend, set this page as System Page `announcecomment`.
3. Customize the `reportcomment` email template in `Resources/views/emails`.

## Key Classes & Tables

* `TdbPkgComment`, `TdbPkgCommentList`: comment records and collections
* `TdbPkgCommentType`: comment type definitions
* `TdbPkgCommentModuleConfig`: per-module settings
* `MTPkgComment`: frontend module logic
* `TPkgCommentType`: base class for comment types

## Extending Functionality

* Override `MTPkgComment::AddCustomDataToCommentBeforeSave()` to inject extra fields
* Override `MTPkgComment::ValidateCommentData()` for custom validation

## License

This bundle is released under the same license as the Chameleon System.
