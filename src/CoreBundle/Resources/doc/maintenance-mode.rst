Maintenance mode
================

The maintenance mode can be activated to signal extended downtimes of the page to the (frontend) user.
It is activated in the backend (CMS settings > Turn off all websites) or with a console command (chameleon_system:maintenance_mode:activate).
If activated any frontend request is normally cancelled early on with checking a marker file and showing
an appropriate system down page.

Technical details
-----------------

On activation this setting is written to the database and the marker file is written
(see \ChameleonSystem\CoreBundle\Maintenance\MaintenanceMode\MaintenanceModeService::activate()).
The marker file is checked at the end of the boot process (see \chameleon::boot()). If found the file
`/web/maintenance.php` is rendered.
The location of the marker file is configured with a constant `PATH_MAINTENANCE_MODE_MARKER`.

Now there are two potential problems with this: file stat cache and multi-node environments.
Both can show an outdated state.

File stat cache
---------------

Php potentially caches stat information about files. So it might go unnoticed if such a file is changed (created/deleted).
In order to mitigate this every time the file is detected an `clearstatcache()` is called to refresh
the cache information - at least for the next request. The cache is _not_ cleared here for normal requests (if the
file is not found).

Later in the startup process when the database is available and the config information from there is queried and cached
the database field on the maintenance mode is checked. If that is (unexpectedly) active the file stat cache is cleared again
and a redirect to the same page performed - which then should end up on the maintenance mode page early.

Multi-node environments
-----------------------

For installations with multiple server instances this is complicated a bit: Now regulary another node can change
the maintenance mode marker file unnoticed. However the database state is natively shared between nodes and is generally
authoritative. With the above second check only one additional property is needed for this to work properly:
The location of the `PATH_MAINTENANCE_MODE_MARKER` file must be specified in a directory that is also shared between nodes.

