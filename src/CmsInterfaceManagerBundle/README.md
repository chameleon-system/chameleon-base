Chameleon System CmsInterfaceManagerBundle
==========================================

The **CmsInterfaceManagerBundle** provides a flexible framework for defining and executing custom import and export tasks inside Chameleon CMS. 
It allows you to register interface manager classes via the `cms_interface_manager` table and implement your business logic by extending provided base classes.

Table of Contents
-----------------
- [Overview](#overview)
- [Database Tables](#database-tables)
- [Creating an Import Interface](#creating-an-import-interface)
  - [1. Add a Record in `cms_interface_manager`](#1-add-a-record-in-cms_interface_manager)
  - [2. Create Your PHP Class](#2-create-your-php-class)
  - [3. Implement Methods](#3-implement-methods)
  - [4. Executing the Import](#4-executing-the-import)
- [Creating a CSV Export Interface](#creating-a-csv-export-interface)
  - [1. Add a Record in `cms_interface_manager`](#1-add-a-record-in-cms_interface_manager-1)
  - [2. Create Your PHP Class](#2-create-your-php-class-1)
  - [3. Implement Methods](#3-implement-methods-1)
  - [4. Executing the Export](#4-executing-the-export)
- [Accessing Parameters](#accessing-parameters)
- [Error Handling and Messages](#error-handling-and-messages)
- [Notes](#notes)

## Overview

The **CmsInterfaceManagerBundle** delivers:
- A factory (`TCMSInterfaceManager`) to create manager instances by **ID** or **system name**.
- A base class (`TCMSInterfaceManagerBase`) to implement **import** logic.
- A subclass (`TCMSInterfaceManagerBaseExportCSV`) to bootstrap **CSV export** workflows.
- Automatic loading of key/value parameters from the `cms_interface_manager_parameter` table.
- Structured execution flow: `Init()`, `RunImport()` (calls `PrepareImport()`, `PerformImport()`, `Cleanup()`), with error tracking and message reporting.

## Database Tables

- `cms_interface_manager`: Defines interface records. Key fields:
  - `systemname`: Unique identifier for your interface.
  - `class`: PHP class name (must extend `TCMSInterfaceManagerBase` or its CSV export subclass).
- `cms_interface_manager_parameter`: Stores parameters per interface record. Accessible via `$this->GetParameter('key')` in your class.

## Creating an Import Interface

1. **Add a Record in `cms_interface_manager`**
   - Set **systemname** (e.g. `my_import`) and **class** (e.g. `My\Bundle\MyImportInterface`).

2. **Create Your PHP Class**
   - Location: `src/CmsInterfaceManagerBundle/objects/db/TCMSInterfaceManager/MyImportInterface.php`

3. **Implement Methods**
   Extend `TCMSInterfaceManagerBase` and override:
   ```php
   class MyImportInterface extends TCMSInterfaceManagerBase
   {
       public function Init(): void
       {
           parent::Init();
           // Custom initialization...
       }

       protected function PrepareImport(): bool
       {
           // Setup temp tables, download feeds, etc.
           return true; // false on preparation failure
       }

       protected function PerformImport(): bool
       {
           // Execute your import logic...
           return true; // false on import failure
       }

       protected function Cleanup(bool $success): bool
       {
           // Always called, even on failure. Clean resources.
           return $success;
       }
   }
   ```

4. **Executing the Import**
   ```php
   /** @var TCMSInterfaceManagerBase $oInterface */
   $oInterface = TCMSInterfaceManager::GetInterfaceManagerObjectBySystemName('my_import');
   $oInterface->Init();
   if ($oInterface->RunImport()) {
       foreach ($oInterface->GetEventInfos() as $message) {
           echo $message;
       }
   } else {
       // Handle error...
   }
   ```

## Creating a CSV Export Interface

Follow the import steps, but extend `TCMSInterfaceManagerBaseExportCSV`:

1. **Add a Record in `cms_interface_manager`**
   - Set **class** to your export manager class (e.g. `My\Bundle\MyCSVExport`).

2. **Create Your PHP Class**
   ```php
   class MyCSVExport extends TCMSInterfaceManagerBaseExportCSV
   {
       protected function GetDataList(): TCMSRecordList
       {
           return TdbMyEntityList::GetList();
       }

       protected function GetFieldMapping(): array
       {
           // [columnName => SQL definition]
           return [
               'id'         => 'INT NOT NULL',
               'title'      => 'VARCHAR(255)',
               'created_at' => 'DATETIME',
           ];
       }

       protected function GetExportRowFromDataObject($oDataObject): array
       {
           // Map fields to CSV row
           return [
               $oDataObject->sqlData['id'],
               $oDataObject->sqlData['title'],
               $oDataObject->sqlData['created_at'],
           ];
       }
   }
   ```

3. **Executing the Export**
   ```php
   /** @var TCMSInterfaceManagerBaseExportCSV $oExport */
   $oExport = TCMSInterfaceManager::GetInterfaceManagerObjectBySystemName('my_csv_export');
   $oExport->Init();
   if ($oExport->RunImport()) {
       echo 'CSV created: '.$oExport->sCSVFilePath;
       echo implode("\n", $oExport->GetEventInfos());
   }
   ```

## Accessing Parameters

Parameters from `cms_interface_manager_parameter` are automatically loaded. Inside your class:
```php
$value = $this->GetParameter('your_key');
```

## Error Handling and Messages

- Use `$this->bHasErrors = true;` to mark errors.
- Append messages with `$this->aMessages[] = 'Detail message';`.
- Retrieve user-facing messages via `GetEventInfos()`.

## Notes

- All database operations use the legacy `MySqlLegacySupport` class.
- Temporary tables are prefixed with `_tmpexport` and a random hash.
- CSV files are saved in `PATH_OUTBOX` and encoded as UTF-8.
