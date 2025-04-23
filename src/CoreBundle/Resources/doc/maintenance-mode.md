# Maintenance Mode

Maintenance mode can be activated to signal extended downtimes of the page to the frontend user.
It is activated in the backend (CMS settings > Turn off all websites) or with a console command (chameleon_system:maintenance_mode:activate).
If active, any frontend request is cancelled early on, showing an appropriate maintenance page.

## Technical Details

On activation this setting is written to the database and a marker file is written
(see \ChameleonSystem\CoreBundle\Maintenance\MaintenanceMode\MaintenanceModeService::activate()).
The marker file is checked during the boot process before the Symfony container is built. This
allows to show maintenance mode even if the container can't be built at this time. If found the file
`/web/maintenance.php` is rendered.
The location of the marker file is configured with a constant `PATH_MAINTENANCE_MODE_MARKER`.

## Multi-node Environments

For installations with multiple server instances the maintenance marker must be located in a shared directory.

Internally this kind of setup requires additional precautions, as creating or deleting the marker file on one node might
go unnoticed on other nodes because of PHP's stat cache. There are two problems we need to avoid:

- Maintenance mode could be activated by an administrator, but other nodes still serve requests regularly.
- Maintenance mode could be deactivated by an administrator, but other nodes still serve the maintenance page.

We want to avoid the performance overhead of clearing the stat cache for every regular request. So we solve the first
problem as follows: As soon as the Symfony container is available during the boot process we re-check the state of the
maintenance mode flag in the cms_config table. By reading this information from TdbCmsConfig (which is cached), we do not
need an additional database query, so this check is cheap. If the database tells us that maintenance mode is active, we
know that the stat cache is stale, so we clear it. Then we redirect to the same page, which leads to the early maintenance
check in chameleon.php showing the maintenance page.
Note that we always assume that all nodes in a Chameleon cluster either share a common database or there is some external synchronization process that works transparently for Chameleon.

We solve the second problem by clearing the stat cache for every request that would end in maintenance mode. Delivering the
static maintenance page is quite cheap, so we can afford the overhead of clearing the cache.