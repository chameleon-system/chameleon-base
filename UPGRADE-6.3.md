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

We are now using standard logging for Symfony applications using `Monolog` and `Psr\Log\LoggerInterface`.
Desired differences in logging - like different log files - should be configured using Monolog or implemented using
its interfaces (like `HandlerInterface`, `ProcessorInterface`, ...).

This is already done in most places in Chameleon itself (chameleon-base and chameleon-shop).

Project code should also be adapted to this. See below for a migration example.
Note that if there is no handler configuration in your project nothing will be written.
A minimum config could be a stream handler logging to `%kernel.logs_dir%/%kernel.environment%.log`.

Note that log messages are no longer written to database.
You can still configure this if needed with the service `cmsPkgCore.logHandler.database` (TPkgCmsCoreLogMonologHandler_Database).
If you use this without channel restriction you must at least explicitly exclude the channel "doctrine".
Also note that the standard logging channel is not configured as "fingerscrossed" anymore. All messages there will simply
be logged everytime.
See below for the full legacy config.

However deprecated service definitions for the old log (channel) handler classes still exist in 
`vendor/chameleon-system/chameleon-base/src/CmsCoreLogBundle/Resources/config/services.xml`.
These will be removed in a later release.

The menu entries in the backend ("logs", "log channel definition") are now hidden - that is: they are no longer 
assigned to the category window ("Logs"). To show them again you can assign them again.

Migration example for changing/defining a log handler for a channel logging to a file:

Old in service.xml:
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
```

New in config.yml - changed the channel name slightly:
```yaml
monolog:
   handlers:
       cms_updates:
           type: stream
           path: "%kernel.logs_dir%/core.log"
           channels:
               - "cms_update"
           level: info
```

This is then used in that services.xml as two service arguments instead of a reference to `cmsPkgCore.logChannel.cmsUpdates`:
```xml
    <argument type="service" id="logger"/>
    <tag name="monolog.logger" channel="cms_updates"/>
```

The full config (in config.yml) that would replicate the legacy logging behavior of Chameleon 6.2 looks like this:

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

## Font Awesome Icons

The icons of Font Awesome have been added.
They will replace all file icons and the glyphicons of Bootstrap3 in the backend.

During migration, icons for main menu items will be replaced with matching Font Awesome icons. 

Where icons cannot be matched, a default icon will be used; the database migrations will tell which icons could not be assigned. To manually assign an icon to a menu item representing a table, navigate to the table settings of this table and fill out the field "Icon Font CSS class". To manually assign an icon to a menu item representing a backend module, do this in the "CMS modules" menu respectively. See other menu items on what to write into these fields.

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

If the project uses custom tables or backend modules, menu items for these will be created in the "Other" menu category.
Revise these items and move them to a fitting "real" category and delete unnecessary menu items.

From a technical point no further changes are required if the project is a shop system. Projects that are pure CMS systems
should consider removing menu categories that are nevertheless created during migration. Note that there will be some
error messages during migration that complain about missing shop tables and modules in this case, which can be ignored. 

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
- \TCMSCronJob_CleanOrphanedMLTConnections::MLT_DELETE_LOG_FILE
- \TCMSTableEditorEndPoint::DELETE_REFERENCES_REVISION_DATA_WHITELIST_SESSION_VAR
- \TPkgCsv2SqlManager::IMPORT_ERROR_LOG_FILE

## Classes and Interfaces

- \IPkgCmsCoreLog
- \MTMenuManager
- \TCMSContentBox
- \TCMSContentBoxItem
- \TCMSFieldMediaProperties
- \TCMSFontImage
- \TCMSFontImageList
- \TCMSMenuItem
- \TCMSMenuItem_Module
- \TCMSMenuItem_Table
- \THTMLFileBrowser
- \TPkgCmsCoreLog
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
- jqModal.js 
- jqDnR.js
- jquery.form.js (new version 4.2.2 located in src/CoreBundle/Resources/public/javascript/jquery/jquery-form-4.2.2/jquery.form.min.js).
- jquery.selectboxes.js
- jQueryUI (everything in path src/CoreBundle/Resources/public/javascript/jquery/jQueryUI; drag and drop still used in the template engine).
- pngForIE.htc
- pNotify (new version 3.2.0 located in src/CoreBundle/Resources/public/javascript/pnotify-3.2.0/)
- respond.min.js
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
