Chameleon System NewsletterBundle
=================================

Installation
------------

Copy these module files into your project folders:
* `NewsletterBundle/Resources/views/webModules/MTPkgNewsletterSignout/MTPkgNewsletterSignout.class.php` to `src/framework/modules/MTPkgNewsletterSignout/`   
* `NewsletterBundle/Resources/views/webModules/MTPkgNewsletterSignup/MTPkgNewsletterSignup.class.php` to `src/framework/modules/MTPkgNewsletterSignup/`

Before adding a module like above on a frontend page, add the NewsletterBundle's resource path to the corresponding theme's
snippet chain, e.g.:  
```PHP
$connection = TCMSLogChange::getDatabaseConnection();
$myThemeName = 'Your Favor Theme';
$yourThemeId = $connection->fetchOne('SELECT `pkg_cms_theme_id` FROM `cms_config` WHERE `name` = ?', ['name' => $myThemeName]);
TCMSLogChange::addToSnippetChain('@ChameleonSystemNewsletterBundle/Resources/views', '@ChameleonSystemCoreBundle/Resources/views', [$yourThemeId]);
```
Please note, that the field "company" is not activated by default in the signUp twig template.

To set a field mandatory simply set this flag to the field in the backend.
