UPGRADE FROM 6.2 TO 6.3
=======================

# Essentials

## Changed Signatures

See the Changed Interfaces and Method Signatures section whether changes in signatures affect the project.

# Changed Features

## Symfony 3.4

The system now uses Symfony 3.4, which needs a few adjustments:

In dev mode the debug toolbar will report deprecations concerning services that are retrieved through the
ServiceLocator or the Symfony container directly. To be prepared for Symfony 4, ServiceLocator and container calls
should be used as rarely as possible (no news here) and dependency injection should be preferred. Where dependency
injection is not possible, services should be declared public explicitly. The deprecation warnings will also be
logged, potentially leading to huge log files and if there is a large number of warnings, performance in the dev
environment will degrade. Therefore it is recommended to deal with most of the deprecations.

The scope concept is gone. Remove any scope references in service definitions (e.g. Chameleon modules used
`scope="prototype"` in the past; if you didn't change these services to use `shared="false"` during the migration to
Chameleon 6.2.x, it needs to be done now).

In `app/config/routing_dev.yml`, replace this route import:

```yaml
_configurator:
    resource: "@SensioDistributionBundle/Resources/config/routing/webconfigurator.xml"
    prefix:   /_configurator
```

with this one:

```yaml
_errors:
    resource: '@TwigBundle/Resources/config/routing/errors.xml'
    prefix:   /_error
```

## Twig 2.x

The system now uses Twig 2.x. Please have a look at the Twig changelog for required adjustments, but major problems are
not expected.

## Logging

Chameleon now logs messages similar to other Symfony applications, using `Psr\Log\LoggerInterface` and `Monolog`.

### Logging Configuration

We recommend using the standard Symfony logging configuration as a base point from where to adjust to project needs.
The application will then log to the logs dir (`app/logs/` by default).

Add the following lines to `app/config/config_dev.yml`:

```yaml
monolog:
  handlers:
    main:
      type: stream
      path: "%kernel.logs_dir%/%kernel.environment%.log"
      level: debug
      channels: ["!event"]
    # uncomment to get logging in your browser
    # you may have to allow bigger header sizes in your Web server configuration
    #firephp:
    #    type: firephp
    #    level: info
    #chromephp:
    #    type: chromephp
    #    level: info
    console:
      type: console
      process_psr_3_messages: false
      channels: ["!event", "!doctrine", "!console"]
```

Add the following lines to `app/config/config_prod.yml`:

```yaml
monolog:
  handlers:
    main:
      type: fingers_crossed
      action_level: error
      handler: nested
      excluded_404s:
        # regex: exclude all 404 errors from the logs
        - ^/
    nested:
      type: stream
      path: "%kernel.logs_dir%/%kernel.environment%.log"
      level: debug
    console:
      type: console
      process_psr_3_messages: false
      channels: ["!event", "!doctrine"]
    deprecation:
      type: stream
      path: "%kernel.logs_dir%/%kernel.environment%.deprecations.log"
    deprecation_filter:
      type: filter
      handler: deprecation
      max_level: info
      channels: ["php"]
```

Add the following lines to `app/config/config_test.yml`:

```yaml
monolog:
  handlers:
    main:
      type: stream
      path: "%kernel.logs_dir%/%kernel.environment%.log"
      level: debug
      channels: ["!event"]
```

See the Monolog documentation on how to change logging behaviour, e.g. logging to different files or modifying log
entries with formatters and processors.

### Adjust Logger Retrieval

There is a number of classes and methods which were used for logging in previous Chameleon releases (and still work)
but are now deprecated, especially `IPkgCmsCoreLog`, `TPkgCmsCoreLog`, `TTools::WriteLogEntry` and 
`TTools::WriteLogEntrySimple`. It is recommended to switch to standard logging as soon as possible. Do this as follows:
- Where dependency injection is possible, inject the `logger` service and typehint `Psr\Log\LoggerInterface`.
- To use a specific channel, add a tag like this: `<tag name="monolog.logger" channel="order_payment_amazon"/>`
- Where dependency injection cannot be used, retrieve the logger by service locator like this: `ChameleonSystem\CoreBundle\ServiceLocator::get('logger')`.
  The return value can always be typehinted with `Psr\Log\LoggerInterface`.
- To retrieve a specific log channel, use a call like this: `ChameleonSystem\CoreBundle\ServiceLocator::get('monolog.logger.custom_channel')`.

Migration example:

Old in services.xml (logger writing to a file):

```xml
<service id="cmsPkgCore.logHandler.files" class="Monolog\Handler\StreamHandler" public="false">
    <argument>%kernel.logs_dir%/core.log</argument>
    <argument>200</argument>
</service>
        
<service id="cmsPkgCore.logDriver.cmsUpdates" class="Monolog\Logger" public="false">
    <argument>core.cmsUpdates</argument>
    <call method="pushHandler">
        <argument type="service" id="cmsPkgCore.logHandler.files"/>
    </call>
</service>

<service id="cmsPkgCore.logChannel.cmsUpdates" class="TPkgCmsCoreLog">
    <argument type="service" id="monolog.logger.cms_update"/>
</service>

<service id="myService" class="MyClass">
    <argument type="service" id="cmsPkgCore.logChannel.cmsUpdates" />
</service>

```

New in services.xml:

```xml
<service id="myService" class="MyClass">
    <argument type="service" id="logger"/>
    <tag name="monolog.logger" channel="cms_update"/>
</service>
```

So the service definition just states the channel in which to log. Directing log output to e.g. a file is then handled
in the logging configuration in `config_<env>.yml`.

### Legacy: Restore Database Logging

Log messages are no longer written to database and the old database logging infrastructure will be removed in
a future Chameleon release. If database logging is still needed in the project, restore by using the following config:

```yaml
monolog:
   handlers:
     database:
       type: service
       id: cmsPkgCore.logHandler.database
       channels:
         - "core_security"
         - "core_cms_updates"
         - "core_cronjobs"
         - "core_api"
 
     database_for_fingers_crossed:
       type: service
       id: cmsPkgCore.logHandler.database
 
     standard:
       type: fingers_crossed
       handler: database_for_fingers_crossed
       channels:
         - "core_standard"
 
     dbal:
       type: stream
       path: "%kernel.logs_dir%/dbal.log"
       channels:
         - "doctrine"
       level: warning
```

Note that the `doctrine` channel MUST be excluded from database logging.

Log-related menu items in the backend ("logs", "log channel definition") are now hidden. To display these items in the
new sidebar menu, create menu items assigned to the corresponding tables. To display these items in the classic main
menu (which itself is no longer visible by default), assign the tables to the "Logs" content box.

## New ImageCropBundle

Chameleon now ships with a bundle that provides support for image cutouts. Install it as follows (this is required if
the ChameleonShopThemeBundle is used, otherwise this step is optional):

- Add `new \ChameleonSystem\ImageCropBundle\ChameleonSystemImageCropBundle()` to the AppKernel.
- In a terminal, navigate to `<project root>/src/extensions/snippets-cms/` and create a symlink:

  ```bash
  ln -s ../../../vendor/chameleon-system/chameleon-base/src/ImageCropBundle/Resources/views/snippets-cms/imageCrop
  ```

- Navigate to `<project root>/src/extensions/objectviews/TCMSFields` (create directory if it doesn't exist yet and
  create a symlink:
  ```bash
  ln -s ../../../../vendor/chameleon-system/chameleon-base/src/ImageCropBundle/Resources/views/objectviews/TCMSFields/TCMSFieldMediaWithImageCrop
  ```

- Run updates in the Chameleon backend.
- Run assets:install console command.
- Clear Symfony cache.

## Mailer Peer Security

The default value of config key `chameleon_system_core: mailer: peer_security` was changed from "permissive" to "strict".
Using this setting SMTP connections verify SSL/TLS certificate validity so that invalid or self-signed certificates are rejected.

## DoctrineBundle

We now use the DoctrineBundle. While we do not use many of its features yet (especially no ORM mapping), initializing
the database connection is now handled by Doctrine. In practice the most noticeable difference is that the connection
is opened lazily, so that more console commands can be executed without a working database connection.

Add the DoctrineBundle to the AppKernel:

```php
    $bundles = array(
    ...
    new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
    ...
    );
```

This change requires to add the following configuration to `/app/config/config.yml`:

```yaml
doctrine:
  dbal:
    host:           '%database_host%'
    port:           '%database_port%'
    dbname:         '%database_name%'
    user:           '%database_user%'
    password:       '%database_password%'
    driver:         'pdo_mysql'
    server_version: '5.7'
    charset:        'utf8'
    default_table_options:
      charset: 'utf8'
      collate: 'utf8_unicode_ci'
```

The parameters should already be defined.

Please note that the parameter `chameleon_system_core.pdo.enable_mysql_compression` no longer works. To use compression,
add the configuration value `doctrine: options: 1006: 1`.

The DoctrineBundle provides a profiler in the Web Debug Toolbar. Therefore the Chameleon database profiler is now
disabled by default, as its main benefit is now the backtrace feature. To enable it again, set the configuration key
`chameleon_system_debug: database_profiler: enabled: true`. Backtrace will then be enabled by default.

The `backtrace_enabled` and `backtrace_limit` keys
were moved under the `database_profiler` key (e.g. `chameleon_system_debug: database_profiler: backtrace_enabled`
instead of `chameleon_system_debug: backtrace_enabled`). Existing configuration should be adjusted.

## Access-denied Page

The access-denied page should now support an additionally mapped parameter `loginFormAction` as a target action for the shown 
login form. This avoids another and then unexpected AccessDeniedException when logging in from the access-denied page.
Login still worked in that case however.
If you use your own theme check the files `webModules/MTExtranet/accessDenied.view.php` and `snippets/common/userInput/form/formLoginStandard.html.twig`
and adapt them according to the changes in **chameleon-shop-theme-bundle**. 

## TTools::GetModuleLoaderObject Returns New Object

The method `TTools::GetModuleLoaderObject` now returns a new `TModuleLoader` instance instead of the global module
loader. This instance will therefore only contain the module passed as argument, not all modules on the current page. 

## Csv2SqlBundle

- `\TPkgCsv2SqlManager::SendErrorNotification()`

Log output is no longer collected and no longer sent as attachments with the notification email.

## RevisionManagementBundle

This bundle is not supported anymore.

## RequestInfoService

- New method `getRequestId()`.

## ckEditor

The custom skin moonocolor is deprecated.
If you have an extension of chameleonConfig.js please be sure to change the skin to 
`config.skin = 'moono-lisa';` and the color to: `config.uiColor = '#f0f3f5';`

# Changed Interfaces and Method Signatures

This section contains information on interface and method signature changes which affect backwards compatibility (BC).
Note that ONLY BC breaking changes are listed, according to our backwards compatibility policy.

## ChameleonSystem\CoreBundle\DataAccess\CmsPortalDomainsDataAccessInterface

- New method `getActivePortalCandidate()`.
- New method `getDomainDataByName()`.
- New method `getPortalPrefixListForDomain()`.

## TCMSFieldDate/TCMSFieldDateToday

The field classes now use the language independent SQL date format in frontend rendering instead of always using
German date format. For backwards compatibility reasons they work with German date format too.

## \TCMSTableWriter

- Method `changeTableEngine()` now also changes the table config accordingly.

## \TTools

- Changed method `WriteLogEntry()`: parameter `$sLogFileName` is now ignored.
- Changed method `WriteLogEntrySimple()`: parameter `$sLogFileName` is now ignored.

## Backend Theme Library

The Backend was upgraded to Bootstrap 4.1.3.

See the [Bootstrap Migration Guide](https://getbootstrap.com/docs/4.1/migration/) for required changes to your backend modules.
To give an impression on which style changes might be required in project code, the following list contains CSS class
changes we performed during the upgrade to Bootstrap 4:

.img-responsive -> .img-fluid
- TCMSFieldMedia
- TCMSFieldGMapCoordinate

btn-default -> btn-secondary
- Some TCMSField types and TCMSTableEditors so check yours.

.pull-left -> .float-left
- TCMSFieldDocument
- TCMSFieldDocumentProperties
- TCMSFieldMediaProperties
- TCMSFieldModuleInstance
- MTHeader
- header navigation
- footer
- Added CSS class migration in TCMSRender::DrawButton method for backwards compatibility

.input-sm -> .form-control-sm
- Almost all TCMSField classes and Twig templates
- Some list managers
- Some TCMSTableEditor classes

.table-condensed -> .table-sm
- TCMSFieldDocumentProperties
- TCMSFieldMediaProperties
- TFullGroupTable

New: .page-item + .page-link
- Pagination in TFullGroupTable

.pull-right -> .float-right
- TFullGroupTable
- MTHeader
- MTTableditor

.input-group-addon -> .input-group-append
- Field types using the text length counter addon (varchar)

.navbar-default -> .navbar-light
- TCMSTableManager and the layout manager iframes

.navbar-toggle -> .navbar-toggler
- header navbar

.col-md-* -> .col-lg-*
- header
- login

Not found anywhere (so you might want to skip this search, too):
- well
- thumbnail
- list-line
- page-header
- dl-horizontal
- blockquote
- btn-xs
- btn-group-justified
- btn-group-xs
- breadcrumb
- center-block
- img-responsive
- img-rounded
- form-horizontal
- radio
- checkbox
- input-lg
- control-label
- hidden/visible-xs, sm, md, lg
- label
- navbar-form
- navbar-btn
- progress-bar*

## Backend JQuery

JQuery that is used in the Chameleon backend was upgraded to version 3.3.1. For backwards compatibility
jquery.migrate 1.4.1 was added, but will be removed in a future Chameleon version.

## Backend Modals

To open modals in the backend, use `CHAMELEON.CORE.showModal()` now, which expects a CSS class that determines the
modal size (one of `modal-sm`, `modal-md`, `modal-lg`, `modal-xl`, `modal-xxl`). This function will then open a
Bootstrap modal.

All `CreateModal...` JavaScript methods now call this method internally, determining the respective classes in a
backwards-compatible way from the `width` argument (if in doubt, `modal-xxl` is used).

Please check custom calls of `CreateModal...` methods and remove width/height settings where possible.

## Backend Tree Path Rendering

Tree paths are now rendered using Bootstrap 4 breadcrumb styles.
Check your code for the CSS class "treeField" and if found, change the HTML to ol/li list with breadcrumb classes.
See TCMSTreeNode::GetTreeNodePathAsBackendHTML() for an example. 

## Icons

The famfamfam icon library is deprecated.
Please check your code to any reference to the directory "/icons/".
The folder exists twice, globally and inside a theme directory and both are deprecated.

The icons of Font Awesome have been added as a replacement.
They will replace all file icons and the glyphicons of Bootstrap3 in the backend.

During migration, icons for main menu items will be replaced with matching Font Awesome icons. 

Where icons cannot be matched, a default icon will be used; the database migrations will tell which icons could not be assigned. To manually assign an icon to a menu item representing a table, navigate to the table settings of this table and fill out the field "Icon Font CSS class". To manually assign an icon to a menu item representing a backend module, do this in the "CMS modules" menu respectively. See other menu items on what to write into these fields.

## File Type Icons in WYSIWYG

The image based file type icons where replaced by ["dmhendricks/file-icon-vectors"](https://github.com/dmhendricks/dmhendricks/file-icon-vectors).
If you replaced the old icons with custom icons, you should check the CSS for that, because the HTML for the downloads changed slightly.
The new icon is a SPAN with CSS classes, instead of a background image of the download link.
If you have no custom icons the new icons should fit without any changes.

Now you are able to overwrite a twig template which renders the download link: "/common/download/download.html.twig"
(backend and frontend)

## Backend Pagedef Configuration

The backend now provides a new sidebar menu which will replace the classic main menu in a future version.
If your project uses custom pagedef files (`*.pagedef.php`), consider adding the sidebar to these files. This is
done by adding the following line after the module list definition:

```php
    addDefaultSidebar($moduleList);
```

There are additional helper methods to simplify adding typical backend modules, but it is optional to use these methods.
See `src/CoreBundle/private/library/classes/pagedefFunctions.inc.php` for reference.

## Main Menu Changes

This release of Chameleon System features a new main menu that is displayed as a sidebar while the old main menu (now
called "classic main menu" is deprecated and will be removed in a future release. The new menu was restructured and some
menu items were renamed to improve comprehensibility of the menu structure.

The content boxes of the classic main menu are unchanged to give users time to get accustomed to the new menu. It
didn't make sense to keep old menu item names though, so be aware that some menu items were changed. The changes
should be quite straightforward.

Also some menu items that were located in the top bar were now moved to the sidebar. Finally the backend modules that
were called in a popup window, like navigation, product search index generation and sanity check, now open inline.

## Home Page Changes

As the main menu is now displayed in the sidebar, the classic main menu was replaced by a welcome screen (which we plan
to replace by a dashboard in a future Chameleon release). If users would like to keep the classic main menu yet, it can
be restored by setting the config value `chameleon_system_core: backend: home_pagedef: 'main'` in `config.yml`.

# Deprecated Code Entities

It is recommended that all references to the classes, interfaces, properties, constants, methods and services in the
following list are removed from the project, as they will be removed in Chameleon 7.0. The deprecation notices in the
code will tell if there are replacements for the deprecated entities or if the functionality is to be entirely removed.

To search for deprecated code usage, [SensioLabs deprecation detector](https://github.com/sensiolabs-de/deprecation-detector)
is recommended (although this tool may not find database-related deprecations).

## Services

- chameleon_system_cms_core_log.cronjob.cleanup_cronjob
- chameleon_system_core.pdo
- cmsPkgCore.logChannel.apilogger
- cmsPkgCore.logChannel.cmsUpdates
- cmsPkgCore.logChannel.cronjobs
- cmsPkgCore.logChannel.dbal
- cmsPkgCore.logChannel.security
- cmsPkgCore.logChannel.standard
- cmsPkgCore.logDriver.apilogger
- cmsPkgCore.logDriver.cmsUpdates
- cmsPkgCore.logDriver.cronjobs
- cmsPkgCore.logDriver.dbal
- cmsPkgCore.logDriver.security
- cmsPkgCore.logDriver.standard
- cmsPkgCore.logHandler.database
- cmsPkgCore.logHandler.dbal
- cmsPkgCore.logHandler.files
- cmsPkgCore.logHandler.fingerscrossed
- pkgShopPaymentPayone.logChannel.apilogger
- pkgShopPaymentPayone.logChannel.standard
- pkgShopPaymentPayone.logDriver.apilogger
- pkgShopPaymentPayone.logDriver.standard

## Container Parameters

- chameleon_system_core.pdo.enable_mysql_compression
- chameleon_system_core.pdo.mysql_attr_compression_name
- chameleon_system_core.pdo.mysql_attr_init_command

## Bundle Configuration

- chameleon_system_debug: backtrace_enabled
- chameleon_system_debug: backtrace_limit

## Log channels

- Three newly defined log channels are deprecated and only necessary for backwards compatibility: chameleon_security, chameleon_dbal, chameleon_api

## Constants

- \CMS_ACTIVE_REVISION_MANAGEMENT
- \PATH_FILETYPE_ICONS
- \PATH_FILETYPE_ICONS_LOW_QUALITY
- \PKG_CMS_CORE_LOG_DEFAULT_MAX_AGE_IN_SECONDS
- \PKG_CMS_CORE_LOG_DEFAULT_MAX_AGE_IN_SECONDS_LEVEL_BELOW_WARNING
- \TCMSCronJob_CleanOrphanedMLTConnections::MLT_DELETE_LOG_FILE
- \TCMSTableEditorEndPoint::DELETE_REFERENCES_REVISION_DATA_WHITELIST_SESSION_VAR
- \TPkgCsv2SqlManager::IMPORT_ERROR_LOG_FILE
- \URL_FILETYPE_ICONS
- \URL_FILETYPE_ICONS_LOW_QUALITY

## Classes and Interfaces

- \ChameleonSystem\CmsCoreLogBundle\Bridge\Chameleon\ListManager\TCMSListManagerLogEntries
- \ChameleonSystem\CmsCoreLogBundle\Command\LogShowCommand
- \IPkgCmsCoreLog
- \MTMenuManager
- \TCMSContentBox
- \TCMSContentBoxItem
- \TCMSFieldMediaProperties
- \TCMSFontImage
- \TCMSFontImageList
- \TCMSMediaTreeNode
- \TCMSMenuItem
- \TCMSMenuItem_Module
- \TCMSMenuItem_Table
- \THTMLFileBrowser
- \TPkgCmsCoreLog
- \TPkgCmsCoreLogCleanupCronJob
- \TPkgCmsCoreLogMonologHandler_Database
- \TPkgSnippetRenderer_TranslationNode
- \TPkgSnippetRenderer_TranslationTokenParser
- \TTemplateTools

## Properties

- \ChameleonSystem\CoreBundle\Controller\ChameleonController::$sGeneratedPage
- \ChameleonSystem\CoreBundle\Controller\ChameleonController::$postRenderVariables
- \TAccessManagerPermissions::$revisionManagement
- \TFullGroupTable::$iconSortASC
- \TFullGroupTable::$iconSortDESC
- \TPkgCsv2Sql::$sLogFileName
- \TCMSFieldLookupFieldTypes::$sFieldHelpTextHTML
- \TCMSFile::sTypeIcon
- \TCMSTableEditorChangeLog::$oOldFields

## Methods

- \ChameleonSystem\CoreBundle\Controller\ChameleonController::GetPostRenderVariables()
- \ChameleonSystem\CoreBundle\Controller\ChameleonController::PreOutputCallbackFunctionReplaceCustomVars()
- \ChameleonSystem\CoreBundle\CronJob\CronJobFactory::setCronJobs()
- \ChameleonSystem\CoreBundle\Interfaces\TransformOutgoingMailTargetsServiceInterface::setEnableTransformation()
- \ChameleonSystem\CoreBundle\Interfaces\TransformOutgoingMailTargetsServiceInterface::setSubjectPrefix()
- \ChameleonSystem\CoreBundle\ModuleService\ModuleResolver::addModule()
- \ChameleonSystem\CoreBundle\ModuleService\ModuleResolver::addModules()
- \ChameleonSystem\CoreBundle\ModuleService\ModuleResolver::getModules()
- \ChameleonSystem\CoreBundle\Service\TransformOutgoingMailTargetsService::setEnableTransformation()
- \ChameleonSystem\CoreBundle\Service\TransformOutgoingMailTargetsService::setSubjectPrefix()
- \CMSTemplateEngine::GetLastRevisionNumber()
- \CMSTemplateEngine::GetMainNavigation()
- \CMSTemplateEngine::LoadRevisionData()
- \MTHeader::addTabToUrlHistory()
- \MTTableEditor::ActivateRevision()
- \MTTableEditor::AddNewRevision()
- \MTTableEditor::GetLastRevisionNumber()
- \MTTableEditor::LoadRevisionData()
- \MTTableManager::getAutocompleteRecordList()
- \TAccessManager::HasRevisionManagementPermission()
- \TCMSCronJob::getLogger()
- \TCMSDownloadFileEndPoint::GetDownloadLink()
- \TCMSFieldColorPicker::isFirstInstance()
- \TCMSFieldLookup::enableComboBox()
- \TCMSLogChange::getUpdateLogger()
- \TCMSTreeNode::GetPageTreeConnectionDateInformationHTML()
- \TGlobal::GetURLHistory()
- \TPkgCmsCoreSendToHost::setLogRequest()
- \TPkgCmsException_Log::getLogger()
- \TPkgCsv2Sql::CreateLogFileName()
- \TPkgCsv2Sql::GetLogFile()
- \TTools::AddStaticPageVariables()
- \TCMSTableEditorChangeLog::savePreSaveValues()
- gcf_CMSUserWithImage()
- gcf_GetPublishedIcon()

## JavaScript Files and Functions

- bootstrap-colorpicker (new version 3.0.3 located in src/CoreBundle/Resources/public/javascript/jquery/bootstrap-colorpicker-3.0.3).
- chosen.jquery.js
- flash.js
- html5shiv.js
- jqModal.js 
- jqDnR.js
- jquery.form.js (new version 4.2.2 located in src/CoreBundle/Resources/public/javascript/jquery/jquery-form-4.2.2/jquery.form.min.js).
- jquery.selectboxes.js
- jQueryUI (everything in path src/CoreBundle/Resources/public/javascript/jquery/jQueryUI; drag and drop still used in the template engine).
- maskedinput.js
- pngForIE.htc
- pNotify (new version 3.2.0 located in src/CoreBundle/Resources/public/javascript/pnotify-3.2.0/)
- respond.min.js
- rwd.images.js
- src/CoreBundle/Resources/public/javascript/mainNav.js
- THTMLFileBrowser.js
- THTMLTable.js

- $.addOption() (jquery.selectboxes plugin)
- $.bgiframe()
- $.blockUI()
- $.everyTime()
- $.jBreadCrumb()
- $.jqDnR() (part of jqModal)
- $.jqM() (jqModal)
- $.oneTime()
- $.stopTime()
- $.tagInput()
- $.unblockUI()
- $.wTooltip()
- ActivateRecordRevision()
- AddNewRevision()
- CreateModalIFrameDialogFromContentWithoutClose()
- PublishViaAjaxCallback()
- SaveNewRevision()
- SetChangedDataMessage()
- showMLTField()
- SwitchEditPortal()
- SwitchEditPortalCallback()

## Frontend Assets

There are some frontend styles, images and javascript helpers located in the core, 
that are deprecated because they are outdated and replaced by frontend themes or will move to the bundle.

- web_modules/MTConfigurableFeedbackCore (will be moved to bundle)
- web_modules/MTExtranet
- web_modules/MTFAQListCore
- web_modules/MTFeedbackCore
- web_modules/MTGlobalListCore
- web_modules/MTNewsletterSignupCore

## Translations

- chameleon_system_core.action.return_to_main_menu
- chameleon_system_core.field_options.option_value_false
- chameleon_system_core.field_options.option_value_true
- chameleon_system_core.fields.lookup.no_matches
- chameleon_system_core.record_lock.lock_owner_fax
- chameleon_system_core.record_revision.action_confirm_restore_revision
- chameleon_system_core.record_revision.action_create_page_revision
- chameleon_system_core.record_revision.action_new_revision
- chameleon_system_core.record_revision.action_load_page_revision
- chameleon_system_core.record_revision.action_load_revision
- chameleon_system_core.record_revision.action_restore_revision
- chameleon_system_core.record_revision.based_on
- chameleon_system_core.record_revision.confirm_restore_revision
- chameleon_system_core.record_revision.description
- chameleon_system_core.record_revision.header_new_revision
- chameleon_system_core.record_revision.last_used_date
- chameleon_system_core.record_revision.name
- chameleon_system_core.record_revision.new_revision_help
- chameleon_system_core.record_revision.new_revision_number
- chameleon_system_core.record_revision.no_revision_exists
- chameleon_system_core.record_revision.revision_number
- chameleon_system_core.template_engine.header_revision
- pkg_cms_core_log.log_table.field_level
- pkg_cms_core_log.log_table.select_level_all
- pkg_cms_core_log.log_table.select_level_all_errors
- pkg_cms_core_log.log_table.select_level_only

## Database Tables

- cms_content_box
- cms_font_image
- cms_record_revision
- cms_tbl_conf_cms_role7_mlt

## Database Fields

- cms_module.cms_content_box_id
- cms_module.icon_font_css_class
- cms_module.show_as_popup
- cms_tbl_conf.cms_content_box_id
- cms_tbl_conf.cms_record_revision_id
- cms_tbl_conf.cms_role7_mlt
- cms_tbl_conf.icon_font_css_class
- cms_tpl_module.revision_management_active

## Flash Messages

- TABLEEDITOR_REVISION_SAVED
