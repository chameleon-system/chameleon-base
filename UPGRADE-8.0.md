UPGRADE FROM 7.1 to 8.0
=======================

# Essentials

The steps in this chapter are required to get the project up and running in version 8.0.
It is recommended to follow these steps in the given order.

## PHP Version

Minimum required PHP version is 8.2. The recommended PHP version is 8.3.
At the moment PHP 8.4 is not yet supported.

## Mandatory Routes

Add the following mandatory backend routes to your `config/routing.yaml`:

```yaml
chameleon_system_security:
    resource: "@ChameleonSystemSecurityBundle/src/Controller/"
    type:     attribute

app_logout:
    path: /cms/logout
    methods: GET
```

## Change Or Remove Deprecated Code (Symfony)

You must change some code which was deprecated in previous Symfony versions and is now removed. Do this now with a working
Chameleon 7.1 project. Any change should also be working with "old" Symfony 4.4.

# Specific changes

- `Deprecated` tag in service xml files now needs to include a `package` and `version` attribute
- Routes looking like `FoobarController:exampleAction` are no longer supported. Use `FoobarController::exampleAction` instead.
  Search for `_controller:` and `_controller :` in your code and fix missing double colons found in controller service calls.
  You also made need the Tag: `<tag name="controller.service_arguments" />` for your controllers.
- `InputFilterUtil` has two new methods: `getFilteredGetInputArray` and `getFilteredPostInputArray`. Use them if you expect the value to be an array instead of a scalar value.
  - This is due to a change in symfony's ParameterBag, which does not support arrays on its `query.get` and `request.get` methods anymore. You now have to use the `all` method for expected arrays.
  - If you are using the `InputFilterUtil` class, there is currently a fallback so the project won't crash immediately. However, this fallback will be removed in the future.

# Twig Changes

The twig environment service now is inlined.
To access it without service injection, you need to use ServiceLocator::get('chameleon_system_snippet_renderer.snippet_renderer')::getTwigEnvironment() as wrapper.

Change the twig error routing from `config/routing_dev.yml`

```yaml
_errors:
    resource: '@TwigBundle/Resources/config/routing/errors.xml'
    prefix:   /_error
```

to:

```yaml
_errors:
    resource: '@FrameworkBundle/Resources/config/routing/errors.xml'
    prefix: /_error
```
# New mandatory bundles

- `AppKernel::registerBundles` now needs the return type "iterable".
- add `new \Symfony\Bundle\SecurityBundle\SecurityBundle(),` to the bundles in `AppKernel::registerBundles` before the chameleon bundles
- add `new \ChameleonSystem\SecurityBundle\ChameleonSystemSecurityBundle(),
  new \ChameleonSystem\CmsBackendBundle\ChameleonSystemCmsBackendBundle(),
  new \KnpU\OAuth2ClientBundle\KnpUOAuth2ClientBundle(),
  new \ChameleonSystem\EcommerceStatsBundle\ChameleonSystemEcommerceStatsBundle(),
  new \ChameleonSystem\ImageEditorBundle\ChameleonSystemImageEditorBundle(),
  new \ChameleonSystem\MarkdownCmsBundle\ChameleonSystemMarkdownCmsBundle(),
  new \ChameleonSystem\CmsDashboardBundle\ChameleonSystemCmsDashboardBundle()` bundles to the `AppKernel::registerBundles` method at the end.

# Removed bundles

- remove `new \ChameleonSystem\UpdateCounterMigrationBundle\ChameleonSystemUpdateCounterMigrationBundle()` from the `AppKernel::registerBundles` method

### Backend user rights changed to symfony security voters

You need to use the SecurityHelperAccess service to check for user rights. The old access manager is no longer available.

See the [backend permissions documentation](docs/backend-permissions.md) for more information.

Examples how to migrate the old accessManager calls to securityHelperAccess:

before:

```php
  $isUserInTableUserGroup = $activeUser->oAccessManager->user->IsInGroups($tableObject->fieldCmsUsergroupId);
  $isEditAllowed = $activeUser->oAccessManager->HasEditPermission($tableObject->fieldName);
  $isShowAllReadonlyAllowed = $activeUser->oAccessManager->HasShowAllReadOnlyPermission($tableObject->fieldName);

  return true === $isUserInTableUserGroup && (true === $isEditAllowed || true === $isShowAllReadonlyAllowed);

  if (false === $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_ACCESS, $tableObject->fieldName)) {
      return false;
  }
```

after:

```php
  /** @var SecurityHelperAccess $securityHelper */
  $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
  if (false === $securityHelper->isGranted(CmsUserRoleConstants::CMS_USER)) {
      return false;
  }

  if (true === $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_EDIT, $tableObject->fieldName)) {
      return true;
  }

  if (true === $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_ACCESS, $tableObject->fieldName)) {
      return true;
  }
```

### List Of Removed Or Changed Code

- Configuration classes: TreeBuilder must be constructed with an argument. (search for new TreeBuilder())
- Event dispatcher (`EventDispatcherInterface`): The argument order is swapped. (search for `->dispatch(` )
- Session: Instead of `getSession()` `hasSession()` should be used for a null check. (search for ->getSession( with a following null check)
- Some event classes have been renamed. 
  specifically: 
  - `FilterResponseEvent` (use `ResponseEvent`)
  - `GetResponseEvent` (use `RequestEvent`)
  - `GetResponseForExceptionEvent` (use `ExceptionEvent`)
  - `PostResponseEvent` (use `TerminateEvent`)
  - `GetRequestEvent`
- Also note that the event class should match the event type (i.e. RequestEvent for "kernel.request").
- Change the event base class `Symfony\Component\EventDispatcher\Event` to `\Symfony\Contracts\EventDispatcher\Event`.
- The namespace of the `Symfony\Component\Translation\TranslatorInterface` changed to `Symfony\Contracts\Translation\TranslatorInterface`.
- Take care that all yaml string values have quotes. For example in any config.yml.
- Signature of `Iterator` has changed. Make sure `current()` and `next()` match the signature
- pass by reference and return by reference has been removed almost everywhere - search for `function &`, `&$` and `=&`.
- search for Regex `=[ ]*\&[\\]{0,1}\w`
- search for `protected function _NewElement\(\$aData\)[ ]*[^\:]{0,1}` (must return the specific Tdb item)
- search for `public static function GetList\(.*\)` (must return the specific list type)
- search for `public static function GetDefaultQuery\(.*\)` (must return `string`)
- search for `public function Previous()` (must return `bool|Tdb....`)
- search for `public function Current()` (must return `bool|Tdb....`)
- search for `public functiocn Next()` (must return `bool|Tdb....`)
- search for `public function GetRequirements()` (must return `void`)
- search for `public function Accept(` (must return `void`)
- `\ChameleonSystem\DebugBundle\ChameleonSystemDebugBundle` removed. The logging of database connections can no longer be done the way done in the bundle
- doctrine update [maybe]s. This also means configuration with `chameleon_system_debug` can no longer be used
  - replace `->fetchAll(` with `->fetchAllAssociative(`
  - replace `->fetchArray` with `->fetchNumeric(`
  - replace `->fetchAssoc` with `->fetchAssociative(`
-  `framework.session.cookie_samesite: lax` added to `src/CoreBundle/Resources/config/project-config.yaml`
- translation files moved to `translations/` (from `src/Resources/translations/` or `src/Resources/<BundleName>/translations/`) in
  the project or a bundle. Inside bundles `Resources/translations/` it is still supported but no longer recommended.
- `kernel.root_dir` (was the `app` folder) has been removed. Use `%kernel.project_dir%` instead (is the project root folder). So `%kernel.root_dir%` becomes `%kernel.project_dir%/app/`
- replace `RequestStack::getMasterRequest()` with `RequestStack::getMainRequest()`
- replace `KernelEvent::isMasterRequest()` with `KernelEvent::isMainRequest()`
- replace `HttpKernelInterface::MASTER_REQUEST` with `HttpKernelInterface::MAIN_REQUEST`
- `@TwigBundle/Resources/config/routing/errors.xml` no longer exists - replace it by `@FrameworkBundle/Resources/config/routing/errors.xml` in your `config/routing*.yaml`
- `GetNewInstance` now type hints. Search for overwritten methods that do not have a return type hint (regex search `function GetNewInstance\((.*)\).*[^:]`)
- `\ChameleonSystem\core\DatabaseAccessLayer\EntityListInterface` changed - so if you are using it, make sure to update your methods
- `\ChameleonSystem\core\DatabaseAccessLayer\EntityListPagerInterface` changed - update implementations
- removed `\TPkgCmsVirtualClassManager::UpdateAllVirtualClasses`
- Add `var/` and remove `app/cache/*` and `!app/cache/.gitkeep` from your .gitignore. Delte the `app/cache` folder.
- Twig File-Loader requires templates to be in the Form `@BundleName/path/to/file/relative/to/Resources/views/.html.twig` where `@BundleName` is the name without `Bundle`.
  This is used in debug data collectors - so search for `<tag name="data_collector"`. Example `<tag name="data_collector" template="@ChameleonSystemElastic/Profiler/layout.html.twig" id="chameleon_system_elastic.search" priority="20"/>`
- the event `chameleon_system_core.filter_content` was removed because it was only used if flushing was enabled. `You may use kernel.response` instead.
  Search for `CoreEvents::FILTER_CONTENT`.
- `\TPkgCustomSearchResultItemList::AddCacheParameters` removed
- `\TPkgCustomSearchResultItemList::AddClearCacheTriggers` removed
- `\TPkgImageHotspotItem::AddClearCacheTriggers` removed
- `\TCMSFieldWYSIWYG::getModifiedToolbarByUser` - dropped user parameter.
- `\TCMSTableEditorPortal::linkPortalToUser` now takes the user id instead of the user
- `\TPkgShopPaymentTransactionContextEndPoint::getCmsUser` was removed
- `\TGlobalBase::$aLangaugeIds` removed
- `\TGlobal::GetLanguageIdList` removed - use `SecurityHelperAccess::getUser()?->getAvailableEditLanguages()` instead
- `\TCMSUser::GetSessionVarName` removed
- `\MTLoginEndPoint::Login` removed
- `\TCMSUser::Login` removed
- `\TCMSUser::Logout` removed
- `\TCMSUser::RelesaseOpenLogs` removed
- `\TCMSUser::getCmsUserId` removed
- `\MTLoginEndPoint::postLoginRedirect` removed
- `\MTLoginEndPoint::IsUserAlreadyLoggedIn` removed
- `\ChameleonSystem\CoreBundle\CoreEvents::BACKEND_LOGIN_SUCCESS` removed
- `\ChameleonSystem\CoreBundle\CoreEvents::BACKEND_LOGIN_FAILURE` removed
- `\ChameleonSystem\CoreBundle\CoreEvents::BACKEND_LOGOUT_SUCCESS` removed
- `\TGlobal->oUser` access via magic `__get` removed.
- `\CMSModuleChooser::$oAccessManager` removed.
- `\TCMSUser::$oAccessManager` removed.
- `\TCMSUser::_LoadAccessManager` removed.
- `\TAccessManager` removed.
  rights are checked via the `SecurityHelperAccess` service now. For example,
  change `$oAccessManager->PermitFunction('cms_template_module_edit')` to `$securityHelper->isGranted('CMS_RIGHT_CMS_TEMPLATE_MODULE_EDIT')`
  so add `CMS_RIGHT_` in front of the old right name from the datbase and make it uppercase.
- `\TAccessManagerUser` removed.
- `\TAccessManagerEditLanguages` removed.
- `\TAccessManagerExtraFunctions` removed.
- `\TAccessManagerGroups` removed.
- `\TAccessManagerPermissions` removed.
- `\TAccessManagerPortals` removed.
- `\TAccessManagerRoles` removed.  
  Use `$user = ServiceLocator::get(SecurityHelperAccess::class)->getUser()
  $roles = $user->getRoles();
  $roleIds = array_keys($roles);
  $roleIdsEscaped = implode(',', array_map(fn($id) => "'".addslashes($id)."'", $roleIds));` instead.
- `\MTLoginEndPoint::Logout` removed (logout works by redirecting to the logout url)
- `\TCMSUser::SetAsActiveUser` removed - switch user by using Symfony impersonate.
- `\TCMSTableEditorCMSUser::SwitchToUser` removed - switch user by using Symfony impersonate.
- `\TCMSUser::CMSUserDefined` removed - use  `ServiceLocator::get(SecurityHelperAccess::class)->isGranted(ChameleonSystem\SecurityBundle\Voter\CmsUserRoleConstants::CMS_USER)` instead
- `\TCMSUser::ValidSessionKey` removed
- `\TCMSUser::GetUserSessionKey` removed
- `\ChameleonSystem\CoreBundle\Service\LanguageServiceInterface::getActiveEditLanguage` removed. Use `\ChameleonSystem\CmsBackendBundle\BackendSession\BackendSessionInterface::getCurrentEditLanguageIso6391` 
- `\TPkgImageHotspotItem::AddClearCacheTriggers` removed
- `\TShopVariantDisplayHandler::AddClearCacheTriggers` removed
- `\ChameleonSystem\core\DatabaseAccessLayer\EntityList::__construct` no longer accepts `null` for the two last parameters (`$queryParameters` and `$queryParameterTypes`))
- `\ChameleonSystem\CoreBundle\Controller\ChameleonController::$moduleLoader` removed
- `\ChameleonSystem\CoreBundle\Controller\ChameleonController::getBlockAutoFlushToBrowser()` removed
- `\ChameleonSystem\CoreBundle\Controller\ChameleonController::PreOutputCallbackFunction()` removed
- `\ChameleonSystem\CoreBundle\Controller\ChameleonController::SetBlockAutoFlushToBrowser()` removed
- `\ChameleonSystem\CoreBundle\Controller\ChameleonControllerInterface::FlushContentToBrowser()` removed
- `\TModuleLoader::SetEnableAutoFlush()` removed
- `\TModuleLoader::getModuleESIPath()` removed
- `\TCMSChangeLogArchiver` removed - use `\ChameleonSystem\CmsChangeLogBundle\DataAccess\CmsChangeLogDataAccess::deleteOlderThan()` instead
- `ChameleonSystem\CoreBundle\Event\RecordChangeEvent:getTableId` removed - use `getTableName` instead
- `CMS_VERSION_MAJOR` removed - use the Twig filter `cms_version` instead
- `CMS_VERSION_MINOR` removed - use the Twig filter `cms_version` instead
- `SidebarBackendModule::toggleCategoryOpenState` removed
- `BackendLoginEvent` and `BackendLogoutEvent` removed - use `Symfony\Component\Security\Http\Event\LoginSuccessEvent` and `Symfony\Component\Security\Http\Event\LogoutEvent` instead
- `TPkgDependencyInjection` removed - use `ChameleonSystem\CoreBundle\ServiceLocator` instead.
- `TPkgCmsFileManager_FileSystem` and `IPkgCmsFileManager` are deprecated now. Use `Symfony\Component\Filesystem\Filesystem` instead.
- `MTPkgExternalTracker_MTShopArticleCatalogCore` removed
- `TextBlockLookup::getText` removed - use `TextBlockLookup::getRenderedText` instead
- `TextBlockLookup::getTextFromTextBlock` removed - use `TextBlockLookup::getRenderedTextFromTextBlock` instead
- `TextBlockLookup::getHeadlineFormTextBlock` removed - use `TextBlockLookup::getHeadlineFromTextBlock` instead
- `DatabaseAccessLayerCmsTplPage` removed
- `idna_convert` removed - use the bundle `algo26-matthias/idna-convert` instead (ToUnicode and ToIdn)
- `TCacheManager` removed - use `ChameleonSystem\CoreBundle\Service\CacheService` search for `TCachemanager::`
- `TPkgCmsCoreSendToHost::setLogRequest` removed
- `TGlobalBase::CountCalls` / `TGlobal::CountCalls` removed
- `TGlobalBase::GetRewriteParameter` / `TGlobal::GetRewriteParameter` removed
- `TGlobalBase::GetController()` / `TGlobal::GetController` removed removed - use `\ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.chameleon_controller')` instead
- We want to get rid of `TGlobal::Translate`. Search and replace: 
  - `\TGlobal::Translate(` -> `\ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans(`
  - `TGlobal::Translate(` -> `\ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans(`
- `TPkgCmsResultCacheManager()` removed - search `new TPkgCmsResultCacheManager()` and replace it with `\ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_cms_result_cache.bridge_chameleon_service.data_base_cache_manager')` instead
- The Mapper `chameleon_system_shop.mapper.social.social_share_privacy` isn't available anymore, remove `chameleon_system_shop.mapper.social.social_share_privacy` from VirtualDbExtension
- The Twig function "url" crashed when recived "null", so check for element is null or defined prior to using it
- `CHAMELEON_URL_GOOGLE_JQUERY` removed because loading an external resource is not recommended. Use the local jQuery version instead.
- `CHAMELEON_ENABLE_FLUSHING` removed
- `\ChameleonSystem\CoreBundle\Controller\ChameleonNoAutoFlushController` removed
- `RequestInfoServiceInterface` is now fully typed. If you extended it, make sure to update your code.
- `TGlobalBase::isFrontendJSDisabled` was removed. Use `ServiceLocator::get('chameleon_system_core.request_info_service')->isFrontendJsDisabled()` instead.
- Prepared for DBAL Update:
  - `executeUpdate` -> `executeStatement`
  - `query` -> `executeQuery`
  - `execute` -> `executeQuery`
  - `fetch()` -> `fetchAssociative`
  -  `fetch(\PDO::FETCH_COLUMN)` -> `fetchNumeric`
  - `fetch(\PDO::FETCH_ASSOC)` -> `fetchAssociative`
  - `fetchColumn` -> `fetchOne` 
- `MySQLLegacySupport` changed
  - `MySqlLegacySupport::fetch_array` Default: `MYSQL_BOTH` is now `MYSQL_ASSOC` (`MYSQL_BOTH` is no longer supported)
  - `MySqlLegacySupport::result` is no longer supported!
- `antiSpam` removed
  - `TTools::EncodeEMail` removed
  - `TCMSTextFieldEndPoint::_ReplaceEMailLinks` removed
  - `AddAntispamIncludesListener` removed
  - `init_spam_class` removed
  - `CHAMELEON_EMAIL_PRINT_SECURITY_LEVEL` can be removed from config.inc.php
  - `TCMSLogChange::addShopSystemPage` removed
- `AddJqueryIncludeListener` removed (check if you have a custom listener, you should remove it or adjust it to load jQuery only in the frontend)
- `CHAMELEON_URL_JQUERY` removed (remove it from config.inc.php)
- `CMSTreeNodeSelectWYSIWYG` removed
- `treenodeselect.pagedef.php` removed
- `CMSDeliverFile` removed
- `CMSGMap` removed
- `CMSiconList` removed

## jQuery in Theme

From now on, each website or theme must include and manage its own version of jQuery if required.
This allows for more flexibility and prevents unnecessary loading of jQuery on projects that do not use it. 
Please make sure to adjust your custom themes as needed.

## jQuery and jQuery Migrate

jQuery was upgraded to the latest 3.7.1 version. jQuery Migrate was removed. 
If you have custom backend code that relies on jQuery or uses older libraries, you need to check it for incompatibilities with the newer version
or missing migrate library.

## LazyLoading
- LazyLoading in the default shop theme and SELL theme changed. If your system is based on these themes, check your code for data-src=" and remove data- prefix and add `loading="lazy"` instead.

## Further Migrations
- This list might not be complete. Also take a look at the official Symfony migration documentation:
  https://github.com/symfony/symfony/blob/6.4/UPGRADE-6.0.md

## Migrating to doctrine ORM

This is NOT recommended yet. The ORM is not yet fully supported. However, if you want to try it, follow these steps:

First, make sure that all property tables have a matching parent key field in the target table. If not, add it.
Add `ChameleonSystemDataAccessBundle` to your AppKernel.

Then, run the following command to generate the doctrine entities:

```bash
app/console chameleon_system:autoclasses:dump
```
Validate the entities

```bash
app/console doctrine:mapping:info
```
Validate the mapping

```bash
app/console doctrine:schema:validate
```

## Adjust Composer Dependencies

In `composer.json`, adjust version constraints for all Chameleon dependencies from `~7.1.0` to `~8.0.0` and run
`composer update`.

Remove the file `app/autoload.php`. It is no longer used by the system (see below).

## Annotation support

The functionality "annotation support" was removed. A deprecated function `AnnotationRegistry::registerLoader()` was called. 
If needed annotations can still be configured and used directly in a project.
However with php > 8 you should use attributes instead.
