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

## Mailer Peer Security

The default value of config key `chameleon_system_core: mailer: peer_security` was changed from "permissive" to "strict".
Using this setting SMTP connections verify SSL/TLS certificate validity so that invalid or self-signed certificates are rejected.

## DoctrineBundle

We now use the DoctrineBundle. While we do not use many of its features yet (especially no ORM mapping), initializing
the database connection is now handled by Doctrine. In practice the most noticeable difference is that the connection
is opened lazily, so that more console commands can be executed without a working database connection.

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

# Deprecated Code Entities

It is recommended that all references to the classes, interfaces, properties, constants, methods and services in the
following list are removed from the project, as they will be removed in Chameleon 7.0. The deprecation notices in the
code will tell if there are replacements for the deprecated entities or if the functionality is to be entirely removed.

To search for deprecated code usage, [SensioLabs deprecation detector](https://github.com/sensiolabs-de/deprecation-detector)
is recommended (although this tool may not find database-related deprecations).

## Services

- chameleon_system_core.pdo

## Container Parameters

- chameleon_system_core.pdo.enable_mysql_compression
- chameleon_system_core.pdo.mysql_attr_compression_name
- chameleon_system_core.pdo.mysql_attr_init_command

## Bundle Configuration

- chameleon_system_debug: backtrace_enabled
- chameleon_system_debug: backtrace_limit

## Constants

None.

## Classes and Interfaces

None.

## Properties

- \ChameleonSystem\CoreBundle\Controller\ChameleonController::$sGeneratedPage
- \ChameleonSystem\CoreBundle\Controller\ChameleonController::$postRenderVariables

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

## JavaScript Files and Functions

None.

## Translations

- chameleon_system_core.field_options.option_value_true
- chameleon_system_core.field_options.option_value_false

## Database Tables

None.

## Database Fields

None.

## Backend Theme Library

The Backend was upgraded to Bootstrap 4.1.3.

See the [Bootstrap Migration Guide](https://getbootstrap.com/docs/4.1/migration/) for needed changes to your backend modules.

During the upgrade to Bootstrap 4 the following styles where checked and these are our findings:

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
