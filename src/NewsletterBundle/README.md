NewsletterBundle
================

Overview
--------
The NewsletterBundle adds newsletter subscription, unsubscription, and campaign delivery capabilities to the Chameleon System.
It provides backend CMS interfaces for managing newsletter campaigns, frontend modules for user signup/signout, personalized placeholder substitution, and automatic sending via Cronjobs.

Key features:
- Frontend signup module (`MTPkgNewsletterSignup`) and signout module (`MTPkgNewsletterSignout`)
- Backend CMS editor for creating/editing `pkg_newsletter_campaign` records (subject, content, placeholders, schedule)
- Placeholder substitution for personalized content (e.g., `[{firstname}]`, `[{lastname}]`, `[{salutation}]`)
- Extensible post-processing pipeline for custom placeholders (`PostProcessorInterface`)
- CMS Cronjob integration (`send_newsletter_cronjob`) to dispatch newsletters to subscribers
- Subscriber export service by group (`NewsletterGroupSubscriberExportService`)
- View mappers for customizing module data and Twig integration for form rendering

Installation
------------
The bundle is included in `chameleon-system/chameleon-base`; no additional Composer installation is needed.

1. Copy frontend module classes:
   ```bash
   cp \
     vendor/chameleon-system/chameleon-base/src/NewsletterBundle/Resources/views/webModules/MTPkgNewsletterSignout/MTPkgNewsletterSignout.class.php \
     src/framework/modules/MTPkgNewsletterSignout/
   cp \
     vendor/chameleon-system/chameleon-base/src/NewsletterBundle/Resources/views/webModules/MTPkgNewsletterSignup/MTPkgNewsletterSignup.class.php \
     src/framework/modules/MTPkgNewsletterSignup/
   ```

2. Add bundle views to your theme’s snippet chain:
   ```php
   // in a migration or update script
   $conn = TCMSLogChange::getDatabaseConnection();
   $themeId = $conn->fetchOne(
       'SELECT `pkg_cms_theme_id` FROM `cms_config` WHERE `name` = ?',
       ['MyThemeName']
   );
   TCMSLogChange::addToSnippetChain(
       '@ChameleonSystemNewsletterBundle/Resources/views',
       '@ChameleonSystemCoreBundle/Resources/views',
       [$themeId]
   );
   ```

3. (Optional) Register the bundle in `app/AppKernel.php` if not using Flex auto-registration:
   ```php
   // app/AppKernel.php
   public function registerBundles()
   {
       $bundles = [
           // ...
           new ChameleonSystem\NewsletterBundle\ChameleonSystemNewsletterBundle(),
       ];
       return $bundles;
   }
   ```

4. Update database and CMS structures:
   ```bash
   php bin/console doctrine:schema:update --force
   php bin/console cms:update
   ```

5. Clear cache and publish assets:
   ```bash
   php bin/console cache:clear
   php bin/console assets:install --symlink
   ```

Usage
-----
1. Create or modify **Newsletter Campaigns** in the backend (**Table Editor** > `pkg_newsletter_campaign`):
   - Set **subject**, **content** (rich text or Markdown), and **send date/time**
   - Insert placeholders (`[{firstname}]`, etc.) for personalization

2. Embed **Signup** or **Signout** modules on frontend pages (**Table Editor** > `cms_tpl_module_instance`):
   - Choose **Module Type** `MTPkgNewsletterSignup` or `MTPkgNewsletterSignout`
   - Configure form fields and spot name, then save

3. Mark form fields as mandatory in the **Table Editor** (e.g., email, salutation, name)

Cronjob Integration
------------------
The bundle defines a CMS Cronjob service `send_newsletter_cronjob` (class `TCMSCronJobSendNewsletter`). Use the CMS Cron scheduler or run manually:
```bash
php bin/console chameleon:cron:run send_newsletter_cronjob
```
This dispatches all scheduled campaigns to active subscribers.

Configuration
-------------
### Custom Placeholder Post-Processing
Implement `PostProcessorInterface` for additional placeholder substitution. Tag your service:
```xml
<service id="app.newsletter.custom_post_processor" class="App\Newsletter\CustomPostProcessor">
  <tag name="chameleon_system_newsletter.post_processor"/>
</service>
```
These post-processors run on both HTML email content and the linked browser version.

### Export Subscribers
Use the `NewsletterGroupSubscriberExportService` to export CSV lists of subscribers per group. Inject via service ID:
```php
$exportService = ServiceLocator::get('chameleon_system_newsletter.service.newsletter_group_subscriber_export_service');
``` 

### ZIP Import
The Bundle can now handle to import newsletter created with canvas. To activate this functionality, the projects configuration needs
to implement:
```
chameleon_system_newsletter:
  import_newsletter_from_zip:
    enabled: true
```
On setting to true, the newsletter campaigns now contain a new WYSIWYG field with an upload button. The canvas ZIP can be uplaoded here.
The bundle generated a temp folder in which the unzipped files will be stored. Images are getting saved into the chameleon media manager 
into a folder named "Newsletter Importe". The html files within the canvas zip are getting extracted and the src and href tags, which contain
the original image links are getting replaced with version with the image urls from out chameleon system.

The WYSIWYG field shows after the import the newsletter design. However, the field is set to readonly, so adjustements have to be mad ein canva.

*important* when activated in the config, the zip import html is getting used for the Newsletter when getting send out

Extensibility
-------------
- **View Mappers**: Tag mappers in `PostProcessing` or `Bridge/Chameleon` with `chameleon_system.mapper` to tailor module data for frontend rendering.
- **Event Listener**: `NewsletterPostProcessorListener` listens on `kernel.response` to apply post-processors.
- **Services**: Override or decorate services in `NewsletterBundle/Service`, `PostProcessing`, and `Bridge` namespaces.

License
-------
This bundle is released under the same license as the Chameleon System. See the LICENSE file in the project root.