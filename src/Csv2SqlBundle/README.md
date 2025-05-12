Chameleon System Csv2SqlBundle
==============================

Overview
--------

The Csv2SqlBundle provides a simple framework to import CSV files into database tables. You define import handlers in the CMS backend (via the
`pkg_csv2sql` table), map CSV columns to database fields, validate incoming data, and perform bulk inserts—either via MySQL’s LOAD DATA INFILE or a shell
fallback. Import runs can be triggered manually or via the built-in CronJob.

Installation
------------
This bundle is part of the `chameleon-system/chameleon-base` package.

Bundle Registration
-------------------
Add it to the `registerBundles()` method in `app/AppKernel.php`:

```php
public function registerBundles()
{
    $bundles = [
        // …
        new ChameleonSystem\Csv2SqlBundle\ChameleonSystemCsv2SqlBundle(),
    ];
    return $bundles;
}
```

## Configuration

No additional YAML config is required. The extension prepends a Monolog channel named csv2sql for logging.

## Defining Import Handlers

1. In the Chameleon backend, open the **CSV-to-SQL** module (table `pkg_csv2sql`).
2. Create a record for each import:
   * **Name**: descriptive label
   * **File pattern**: filename or wildcard in `cmsdata/tmp` to watch
   * **Target table**: database table name
   * **Fields**: comma-separated list of table columns, matching CSV order
   * **Options**: flags for REPLACE vs INSERT, header row, charset, etc.
3. Save your handler.

## Runtime Process

* **Validation**: `TPkgCsv2SqlManager::ValidateAll()` checks each CSV for presence and row-level data errors.
* **Import**: `TPkgCsv2SqlManager::ImportAll()` loops over handlers and calls each’s `Import()` method.
* **Bulk SQL**: The `BulkSql` implementations (`TPkgCmsBulkSql_LoadDataInfile` or `TPkgCmsBulkSql_SimpleSql`) write CSV rows to a temp file and
execute `LOAD DATA LOCAL INFILE`. On failure it falls back to a shell call.
* **Error Reporting**: Any validation or import errors are collected and, if present, emailed via the mail profile `shop-import-data`. Logs are
written to the `csv2sql` Monolog channel.

## CronJob Integration

To automate, register the CronJob TPkgCsv2Sql_TCMSCronJob in the CMS’s cron job table. It will call `TPkgCsv2SqlManager::ProcessAll()` on schedule.

## Key Classes & Interfaces

* `TPkgCsv2SqlManager`: Orchestrates validation, import, and error notification.
* `TPkgCsv2Sql` / `TdbPkgCsv2sql` & `TdbPkgCsv2sqlList`: Backend record and list of import handlers.
* `IPkgCmsBulkSql`: Interface for bulk import drivers.
* `TPkgCmsBulkSql_LoadDataInfile`: Uses MySQL LOAD DATA LOCAL INFILE.
* `TPkgCmsBulkSql_SimpleSql`: Fallback that inserts rows one by one.
* `TPkgCsv2Sql_TCMSCronJob`: Cronjob wrapper for scheduled imports.

## Logging

Monolog channel csv2sql is available for debug and info:

```yaml
monolog:
    channels: [csv2sql]
    handlers:
        csv2sql:
            type: stream
            path: '%kernel.logs_dir%/%kernel.environment%.csv2sql.log'
            level: info
            channels: [csv2sql]
```

## Extending

* Implement new bulk import strategies by creating a class matching `IPkgCmsBulkSql` and swapping in your handler.
* Customize email notifications by editing the `shop-import-data` mail profile in the backend.

## License

This bundle is released under the same license as the Chameleon System.
