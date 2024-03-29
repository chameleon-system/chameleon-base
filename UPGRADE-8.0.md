UPGRADE FROM 7.1 to 8.0
=======================

# Essentials

The steps in this chapter are required to get the project up and running in version 8.0.
It is recommended to follow these steps in the given order.

## Change Or Remove Deprecated Code (Symfony)

You must change some code which was deprecated in previous Symfony versions and is now removed. Do this now with a working
Chameleon 7.1 project. Any change should also be working with "old" Symfony 4.4.

### List Of Removed Or Changed Code

- Configuration classes: TreeBuilder must be constructed with an argument. (search for new TreeBuilder())
- Event dispatcher (`EventDispatcherInterface`): The argument order is swapped. (search for ->dispatch( )
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
- search for `=[ ]*\&[\\]{0,1}\w`
- search for `protected function _NewElement\(\$aData\)[ ]*[^\:]{0,1}` (must return the specific Tdb item)
- search for `public static function GetList\(.*\)` (must return the specific list type)
- search for `public static function GetDefaultQuery\(.*\)` (must return `string`)
- search for `public function Previous()` (must return `bool|Tdb....`)
- search for `public function Current()` (must return `bool|Tdb....`)
- search for `public functiocn Next()` (must return `bool|Tdb....`)
- `\ChameleonSystem\DebugBundle\ChameleonSystemDebugBundle` removed. The logging of database connections can no longer be done the way done in the bundle
- doctrine update [maybe]s.
  - replace `->fetchAll(` with `->fetchAllAssociative(`
  - replace `->fetchArray` with `->fetchNumeric(`
  - replace `->fetchAssoc` with `->fetchAssociative(`
-  `framework.session.cookie_samesite: lax` added to `src/CoreBundle/Resources/config/project-config.yaml`
- translation files moved to `translations/` (from `src/Resources/translations/` or `src/Resources/<BundleName>/translations/`) in
  the project or a bundle. Inside bundles `Resources/translations/` ist still supported but no longer recommended.
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
- `\TPkgCustomSearchResultItemList::AddCacheParameters` removed
- `\TPkgCustomSearchResultItemList::AddClearCacheTriggers` removed
- `\TPkgImageHotspotItem::AddClearCacheTriggers` removed
- `\TCMSFieldWYSIWYG::getModifiedToolbarByUser` - dropped user parameter.
- `\TCMSTableEditorPortal::linkPortalToUser` now takes the user id instead of the user
- `\TPkgShopPaymentTransactionContextEndPoint::getCmsUser` was removed
- `\TGlobalBase::$aLangaugeIds` removed
- `\TGlobal::GetLanguageIdList` deprecated
- `\TCMSUser::GetSessionVarName` removed
- `\MTLoginEndPoint::Login` removed
- `\TCMSUser::Login` removed
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
- `\TAccessManagerUser` removed.
- `\TAccessManagerEditLanguages` removed.
- `\TAccessManagerExtraFunctions` removed.
- `\TAccessManagerGroups` removed.
- `\TAccessManagerPermissions` removed.
- `\TAccessManagerPortals` removed.
- `\TAccessManagerRoles` removed.
- `\MTLoginEndPoint::Logout` removed (logout works by redirecting to the logout url)
- `\TCMSUser::Logout` removed
- `\TCMSUser::SetAsActiveUser` removed - switch user by using Symfony impersonate.
- `\TCMSTableEditorCMSUser::SwitchToUser` removed - switch user by using Symfony impersonate.
- `\TCMSUser::CMSUserDefined` removed - is `isGranted(\ChameleonSystem\SecurityBundle\CmsUser\UserRoles::CMS_USER)`
- `\TCMSUser::ValidSessionKey` removed
- `\TCMSUser::GetUserSessionKey` removed
- `\ChameleonSystem\CoreBundle\Service\LanguageServiceInterface::getActiveEditLanguage` removed. Use `\ChameleonSystem\CmsBackendBundle\BackendSession\BackendSessionInterface::getCurrentEditLanguageIso6391` 
- `\TPkgImageHotspotItem::AddClearCacheTriggers` removed
- `\TShopVariantDisplayHandler::AddClearCacheTriggers` removed
- `\ChameleonSystem\core\DatabaseAccessLayer\EntityList::__construct` no longer accepts `null` for the two last parameters (`$queryParameters` and `$queryParameterTypes`))
This list might not be complete. Also take a look at the official Symfony migration documentation:
https://github.com/symfony/symfony/blob/5.4/UPGRADE-5.0.md

## Migrating to doctrine ORM
First, make sure that all property tables have a matching parent key field in the target table. If not, add it.
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

# Removed Features

## Annotation support

The functionality "annotation support" was removed. This file was calling a
deprecated function `AnnotationRegistry::registerLoader()`. If needed annotations can still be configured and used
directly in a project.
However with php > 8 you should use attributes instead.

# Newly Deprecated Code Entities
# Removed Code Entities

The code entities in this list were marked as deprecated in previous releases and have now been removed.

