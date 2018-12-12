UPGRADE FROM 6.2 TO 6.3
=======================

# Essentials

## Changed Signatures

See the Changed Interfaces and Method Signatures section whether changes in signatures affect the project.

# Changed Features

## Symfony 3.4

The system now uses Symfony 3.4, which needs a few adjustments:

- In dev mode the debug toolbar will report deprecations concerning services that are retrieved through the
  ServiceLocator or the Symfony container directly. To be prepared for Symfony 4, ServiceLocator and container calls
  should be used as rarely as possible (no news here) and dependency injection should be preferred. Where dependency
  injection is not possible, services should be declared public explicitly. The deprecation warnings will also be
  logged, potentially leading to huge log files and if there is a large number of warnings, performance in the dev
  environment will degrade. Therefore it is recommended to deal with most of the deprecations.
- The scope concept is gone. Remove any scope references in service definitions (e.g. Chameleon modules used
  `scope="prototype"` in the past, which should have been changed to `shared="false"` when migrating to 6.2.x).

## Twig 2.x

The system now uses Twig 2.x. Please have a look at the Twig changelog for required adjustments, but major problems are
not expected.

## Logging

Any logging should now be done using `Monolog` (and `LoggerInterface`).
Desired differences in logging - like different log files - should be configured using Monolog or implemented using
its interfaces (like `HandlerInterface`, `ProcessorInterface`, ...).

NOTE that the old default logging into the database is not done anymore.
You can still configure this if needed with the service `cmsPkgCore.logHandler.database` (TPkgCmsCoreLogMonologHandler_Database).
Also note that the standard logging channel is not configured as "fingerscrossed" anymore. All messages there will simply
be logged everytime.
See below for the full legacy config.

This was largely changed in the two base packages (chameleon-base and chameleon-shop).

However deprecated service definitions for the old log (channel) handler classes still exist in 
`vendor/chameleon-system/chameleon-base/src/CmsCoreLogBundle/Resources/config/services.xml`.

The full config (in config.yml) that would replicate the legacy logging behavior of Chameleon 6.2 looks like this:

```
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
         - "core_dbal"
       level: warning
```

Example for changing/defining a log handler for a channel logging to a file:

Old in service.xml:
```
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
```
monolog:
   handlers:
       cms_updates:
           type: stream
           path: "%kernel.logs_dir%/core.log"
           channels:
               - "cms_update"
           level: info
```

This is then used in that service.xml as two service arguments instead of a reference to `cmsPkgCore.logChannel.cmsUpdates`:
```
    <argument type="service" id="logger"/>
    <tag name="monolog.logger" channel="cms_updates"/>
```

## TTools::GetModuleLoaderObject Returns New Object

The method `TTools::GetModuleLoaderObject` now returns a new `TModuleLoader` instance instead of the global module
loader. This instance will therefore only contain the module passed as argument, not all modules on the current page. 

## Csv2SqlBundle

- `\TPkgCsv2Sql::Import()`

Return type annotation fixed: is actually "array" but was "bool".

- `\TPkgCsv2SqlManager::SendErrorNotification()`

Log output is no longer collected and no longer sent as attachements with the notification mail.

# Changed Interfaces and Method Signatures

This section contains information on interface and method signature changes which affect backwards compatibility (BC).
Note that ONLY BC breaking changes are listed, according to our backwards compatibility policy.

## ChameleonSystem\CoreBundle\DataAccess\CmsPortalDomainsDataAccessInterface

- New method `getActivePortalCandidate()`.
- New method `getDomainDataByName()`.
- New method `getPortalPrefixListForDomain()`.

## \TTools

- Changed method `WriteLogEntry()`: parameter `$sLogFileName` is now ignored.
- Changed method `WriteLogEntrySimple()`: parameter `$sLogFileName` is now ignored.

## \ChameleonSystem\AmazonPaymentBundle\AmazonOrderReferenceObject

- Changed method `__construct()` is now using `LoggerInterface` as parameter type.

## \ChameleonSystem\AmazonPaymentBundle\AmazonPaymentGroupConfig

- Changed method `setLogger()` is now using `LoggerInterface` as parameter type.

# Deprecated Code Entities

It is recommended that all references to the classes, interfaces, properties, constants, methods and services in the
following list are removed from the project, as they will be removed in Chameleon 7.0. The deprecation notices in the
code will tell if there are replacements for the deprecated entities or if the functionality is to be entirely removed.

To search for deprecated code usage, [SensioLabs deprecation detector](https://github.com/sensiolabs-de/deprecation-detector)
is recommended (although this tool may not find database-related deprecations).

## Services

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

- Three (newly) defined logging channels are deprecated and only necessary for backwards compatibility: chameleon_security, chameleon_dbal, chameleon_api

## Constants

- \TCMSCronJob_CleanOrphanedMLTConnections::MLT_DELETE_LOG_FILE
- \TPkgCsv2SqlManager::IMPORT_ERROR_LOG_FILE

## Classes and Interfaces

- \IPkgCmsCoreLog
- \TPkgCmsCoreLog

## Properties

- \ChameleonSystem\CoreBundle\Controller\ChameleonController::$sGeneratedPage
- \ChameleonSystem\CoreBundle\Controller\ChameleonController::$postRenderVariables
- \TPkgCsv2Sql::$sLogFileName

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
- \TCMSCronJob::getLogger()
- \TCMSLogChange::getUpdateLogger()
- \TPkgCmsCoreSendToHost::setLogRequest()
- \TPkgCmsException_Log::getLogger()
- \TPkgCsv2Sql::CreateLogFileName()
- \TPkgCsv2Sql::GetLogFile()
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
