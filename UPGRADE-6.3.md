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

## TTools::GetModuleLoaderObject Returns New Object

The method `TTools::GetModuleLoaderObject` now returns a new `TModuleLoader` instance instead of the global module
loader. This instance will therefore only contain the module passed as argument, not all modules on the current page. 

# Changed Interfaces and Method Signatures

This section contains information on interface and method signature changes which affect backwards compatibility (BC).
Note that ONLY BC breaking changes are listed, according to our backwards compatibility policy.

## ChameleonSystem\CoreBundle\DataAccess\CmsPortalDomainsDataAccessInterface

- New method `getActivePortalCandidate()`.
- New method `getDomainDataByName()`.
- New method `getPortalPrefixListForDomain()`.

## TCMSFieldDate/TCMSFieldDateToday

The field classes now use the language independent SQL date format in frontend rendering instead of always using a german date format. 
For backwards compatibility reasons they work with the german date format, too. 

# Deprecated Code Entities

It is recommended that all references to the classes, interfaces, properties, constants, methods and services in the
following list are removed from the project, as they will be removed in Chameleon 7.0. The deprecation notices in the
code will tell if there are replacements for the deprecated entities or if the functionality is to be entirely removed.

To search for deprecated code usage, [SensioLabs deprecation detector](https://github.com/sensiolabs-de/deprecation-detector)
is recommended (although this tool may not find database-related deprecations).

## Services

None.

## Container Parameters

None.

## Constants

None.

## Classes and Interfaces

None.

## Properties

- \ChameleonSystem\CoreBundle\Controller\ChameleonController::$sGeneratedPage
- \ChameleonSystem\CoreBundle\Controller\ChameleonController::$postRenderVariables
- \TCMSFieldLookupFieldTypes::sFieldHelpTextHTML

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
- \TTools::AddStaticPageVariables()
- \TCMSFieldLookup::enableComboBox()

## JavaScript Files and Functions

- $.blockUI();
- $.unblockUI();

Use CHAMELEON.CORE.showProcessingDialog() and CHAMELEON.CORE.hideProcessingDialog() instead.

## Translations

- chameleon_system_core.field_options.option_value_true
- chameleon_system_core.field_options.option_value_false
- chameleon_system_core.record_lock.lock_owner_fax
- chameleon_system_core.fields.lookup.no_matches

## Database Tables

None.

## Database Fields

None.

## Backend Theme Library

The Backend was upgraded to Bootstrap 4.1.3.

See the [Bootstrap Migration Guide](https://getbootstrap.com/docs/4.1/migration/) for needed changes to your backend modules.

During the upgrade to Bootstrap 4 the following styles where checked and these are our findings:

.img-responsive -> .img-fluid
- TCMSFieldMedia
- TCSMFieldFMapCoordinate

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

.pull-rigt -> .float-right
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
