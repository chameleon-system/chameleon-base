UPGRADE FROM 7.0 to 7.1
=======================

# Essentials

The steps in this chapter are required to get the project up and running in version 7.1.
It is recommended to follow these steps in the given order.

## Prepare Project

Be sure to install (locally) the latest release of the Chameleon 7.0.x branch before continuing migrating.

Logout from the Chameleon backend.

## Adjust Composer Dependencies

In `composer.json`, adjust version constraints for all Chameleon dependencies from `~7.0.0` to `~7.1.0` and run
`composer update`.

If your project requires `sensio/generator-bundle`, then remove it as the bundle has no support for symfony 4. You will
also need to remove `\Sensio\Bundle\DistributionBundle\SensioDistributionBundle` and 
`\Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle` from your `AppKernel`.

Remove `\Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle` from your AppKernel as well.

## Update your config*.yml

If you are referencing parameters via `%parametername%` in your config files, you will need to make sure that these parameters
are quoted.

## Upgrade you scripts section

In `composer.json`, change the following lines under `symfony-scripts`

```
"Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::clearCache",
"Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::installAssets",
"Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::prepareDeploymentTarget"
```

to:

```
"app/console cache:clear",
"app/console assets:install --symlink --relative web"
```

Then remove the line

`"ChameleonSystem\\DistributionBundle\\VersionCheck\\PostUpdateVersionCheck::checkVersion"`

from the `post-update-cmd` section.

## Regenerate Autoclasses And Run Updates

Regenerate autoclasses by calling the console command `app/console chameleon_system:autoclasses:generate`.
Execute the updates by calling the console command `app/console chameleon_system:update:run`.

## Add A Tag To Mappers

All mappers (classes inheriting from AbstractViewMapper) must now be tagged with `chameleon_system.mapper` if they are defined
as service.

## Make Legacy Services Public

All service classes that are loaded by `ServiceLocator::get()` must now be `public="true"` in their service definitions.
You can use the Chameleon System Upgrade Helper tool to determine the services that should be made public: https://github.com/bestform/Chameleon-System-Upgrade-Helper

## Correct Cronjob Hierarchy

Cronjob classes need to extend the class TdbCmsCronjobs. Currently the often used class TCMSCronJob does not have all the necessary field.
So searching for "TCMSCronJob" in the project code should give you a complete list for this task.
Furthermore every cronjob should only calls the parent constructor like so:

Before:
```
class ... extends TCMSCronJob {

 public function __construct()
    {
        parent::TCMSCronJob();
    }
}

// or

class ...Cronjob extends TdbCmsCronjobs {

public function __construct()
    {
        parent::TdbCmsCronjobs();
    }
}
```
Should Be:
```
class ...Cronjob extends TdbCmsCronjobs {

 // If construct needed
 public function __construct(string $...)
    {
        parent::__construct();
        $this->... = $..;
    }
}
```
# Cleanup
# Informational
# Changed Features
## Changed Interfaces and Method Signatures

# Removed Features
# Deprecated Code Entities

* ExtranetUserProviderInterface::getActiveUser: null as a return value is deprecated
* TCMSChangeLogArchiver

# Removed Code Entities

The code entities in this list were marked as deprecated in previous releases and have now been removed.

