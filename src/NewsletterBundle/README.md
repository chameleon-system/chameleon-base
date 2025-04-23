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

## Configuration

### Adding additional substitutions for newsletter texts

Newsletter pages can include placeholders, that can be substituted individually on a per user basis. For example, you might want to add the user's name by placing a `[{salutation}], [{firstname}], [{lastname}]` in a text field.
The newsletter package will substitute those placeholders when rendering and/or sending the newsletter.

If you want to substitute additional placeholder in newsletters, you can implement your own `\ChameleonSystem\NewsletterBundle\PostProcessing\PostProcessorInterface` and tag it in the service
container using the tag name `chameleon_system_newsletter.post_processor`.

If you have existing extensions doing this by extending `\TCMSNewsletterCampaign` you should move your code into a new post processor, otherwise it will only work in the sent mails, but not on the
linked html version, that will be displayed in the browser.