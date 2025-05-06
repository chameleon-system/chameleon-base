# Chameleon System TrackViewsBundle

## Overview

The TrackViewsBundle provides functionality to track and record views of objects (e.g., CMS pages, shop articles) within the Chameleon System. It includes Doctrine entities for storing view counts and history, an event listener for pixel-based automatic tracking, programmatic tracking API, and cron jobs for batching and updating view data.

## Key Features

- Persistent storage of view counts (`pkg_track_object`) and detailed view history (`pkg_track_object_history`) entities.
- Automatic tracking via a 1×1 GIF pixel (`TrackViewsListener`) on HTTP requests with the `trackviews` parameter.
- Renderable tracking pixel (`TPkgTrackObjectViews::Render()` / Twig function) for embedding in templates.
- Programmatic tracking (`TPkgTrackObjectViews::TrackObject()`).
- Cron jobs for aggregating history records and updating shop article view counters.

## Installation

1. Ensure the bundle is registered in your kernel:
   ```php
   // Symfony Flex (config/bundles.php)
   return [
       // ...
       ChameleonSystem\TrackViewsBundle\ChameleonSystemTrackViewsBundle::class => ['all' => true],
   ];
   ```
2. (Optional) Add the tracking pixel in your template:
   ```php
   echo TPkgTrackObjectViews::GetInstance()->Render();
   // Outputs: <img src="?pg=...&trackviews=1&rnd=..." width="1" height="1" />
   ```

## Configuration

Create or update `config/packages/chameleon_system_track_views.yaml`:
```yaml
chameleon_system_track_views:
  enabled: true             # Enable or disable the bundle
  target_table: 'pkg_track_object'  # Base table name for view storage (history table = target_table + '_history')
  time_to_live: 3600        # TTL in seconds for temporary view aggregation
```

## Services & Cron Jobs

- `chameleon_system_track_views.listener.track_views`: Listens to `kernel.request`; returns a 1×1 GIF on `?trackviews=1`.
- `chameleon_system_track_views.cronjob.collect_views_cronjob`: Aggregates and prunes view history records.
- `chameleon_system_track_views.cronjob.update_product_view_counter_cronjob`: Updates shop article view counters.

## Usage Examples

1. **Automatic Tracking** (pixel-based):
   ```php
   echo TPkgTrackObjectViews::GetInstance()->Render();
   ```

2. **Programmatic Tracking**:
   ```php
   $tracker = TPkgTrackObjectViews::GetInstance();
   $tracker->TrackObject(
       $oTableRecord,         // any TCMSRecord or entity-like record
       $bCountReloads = true, // count reloads
       $bAllowMultipleViewsPerPage = false
   );
   echo $tracker->Render();
   ```

4. **Twig Integration**
   In Twig templates, call the `track_views_pixel` function to render the tracking pixel:
   ```twig
   {{ track_views_pixel() }}
   ```

## Database Schema

The bundle provides two tables:

- **`pkg_track_object`**
  - `id` (UUID)
  - `table_name` (string)
  - `owner_id` (string)
  - `view_count` (integer)

- **`pkg_track_object_history`**
  - `id` (UUID)
  - `table_name` (string)
  - `owner_id` (string)
  - `datecreated` (datetime)
  - `data_extranet_user_id` (string)
  - `ip` (string)
  - `request_checksum` (string)

Doctrine ORM mappings are located in `Resources/config/doctrine/*.orm.xml`.

Customization
-------------
- Disable automatic pixel tracking: set `enabled: false` in your configuration.
- Override services by tagging/decorating `TrackViewsListener` or cron job services.
- Extend or replace `TPkgTrackObjectViews` logic by subclassing or implementing a custom tracking mechanism.
