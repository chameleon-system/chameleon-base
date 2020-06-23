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

## Regenerate Autoclasses And Run Updates

Regenerate autoclasses by calling the console command `app/console chameleon_system:autoclasses:generate`.
Execute the updates by calling the console command `app/console chameleon_system:update:run`.

## Add A Tag To Mappers

All mappers (classes inheriting from IViewMapper) must now be tagged with `chameleon_system.mapper` if they are defined
as service.

## Make Legacy Services Public

All service classes that are loaded by `ServiceLocator::get()` must now be `public="true"` in their service definitions.

# Cleanup
# Informational
# Changed Features
## Changed Interfaces and Method Signatures

# Removed Features
# Deprecated Code Entities
# Removed Code Entities

The code entities in this list were marked as deprecated in previous releases and have now been removed.

