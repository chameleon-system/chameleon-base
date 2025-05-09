Chameleon System GenericTableExportBundle
=========================================

Overview
--------
The GenericTableExportBundle provides a flexible, configurable way to export database tables or custom queries via Twig templates. Exports can be written to the filesystem or streamed as a browser download in various formats (e.g., CSV, XML).

Installation
------------
The bundle is included with `chameleon-system/chameleon-base`. No separate Composer install is required.

Bundle Registration
-------------------
Symfony Flex (4+) auto-registers bundles. Without Symfony Flex, add it to `app/AppKernel.php`:

```php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = [
        // ...
        new ChameleonSystem\GenericTableExportBundle\ChameleonSystemGenericTableExportBundle(),
    ];
    return $bundles;
}
```

Backend Configuration
---------------------
1. In the CMS backend, open the **Table Editor** for the `pkg_generic_table_export` table (System Name: GenericTableExport).
2. Create a new export configuration and set the following fields:
   - **System Name**: Unique identifier for this export (used in code: `GetInstanceFromSystemName`).
   - **CMS Table Configuration**: Select the table to export (CMS `cms_tbl_conf` record).
   - **Restriction**: Optional SQL `WHERE` clause to restrict the exported rows.
   - **View Path**: (optional) Subdirectory under `Resources/views/snippets-cms/pkgGenericTableExport` for custom Twig templates.
   - **Header View**: (optional) Twig template for the export header (e.g., `pkgShopOrderFullHeader.csv.twig`).
   - **View**: Twig template for each record row (e.g., `pkgShopOrderFull.csv.twig`).
   - **Mapper Configuration**: (optional) Semicolon-separated list of mapper identifiers to apply (e.g., `ShopOrder;ShopExtranetUser`).
   - **Export Filename**: Base name for the generated file (without extension).
   - **UTF-8 Decode**: If checked, output will be decoded to ISO-8859-1.

3. Save the record. In the table editor menu, two export actions appear:
   - **Export to Server**: writes the file to `cmsdata/export/`.
   - **Export and Download**: streams the file as a browser download.

Twig Templates
--------------
Default templates are provided under `Resources/views/snippets-cms/pkgGenericTableExport`:
- `pkgCmsChangeLogFullHeader.csv.twig` / `pkgCmsChangeLogFull.csv.twig` – ChangeLog CSV export.
- `pkgNewsletterUserFullHeader.csv.twig` / `pkgNewsletterUserFull.csv.twig` – Newsletter users CSV export.
- `pkgShopExtranetUserHeader.csv.twig` / `pkgShopExtranetUser.csv.twig` – Extranet users CSV export.
- `pkgShopOrderFull.xml.twig` – Shop orders XML export.

Copy and customize these templates or add new ones in a custom subdirectory and set **View Path** accordingly.

Available Mappers
-----------------
The core mapper (`GenericTableExportMapper`) makes raw SQL data available to templates as `sqlData`. Additional, profile-specific mappers can be enabled via **Mapper Configuration**:
- `Mapper_PkgCmsChangelog` – Formats changelog rows (table names, user names, change types).
- `Mapper_PkgNewsletterGroup` – Adds salutation and group info to newsletter user exports.
- `Mapper_ShopExtranetUser` – Includes billing and shipping address data for extranet users.
- `Mapper_ShopOrder` – Appends formatted billing/shipping addresses and order item details for shop orders.

Programmatic API
----------------
Use PHP to load an export configuration and trigger exports:

```php
use TdbPkgGenericTableExport;

// Load configuration by system name
/** @var TdbPkgGenericTableExport|null $export */
$export = TdbPkgGenericTableExport::GetInstanceFromSystemName('your_system_name');
if (null !== $export) {
    // Write export file to server (returns bool)
    $success = $export->WriteExportToFile(null, $decodeUtf8 = false);

    // Stream download (sends headers and exits)
    $export->WriteExportToDownload(null, $decodeUtf8 = false);
}
```

Exports without an `$id` argument export the full list. Passing an ID exports a single record.

Files are written to:
```
PATH_CMS_CUSTOMER_DATA/export/<id>-<exportFilename>.<extension>
```

License
-------
This bundle is released under the same license as the Chameleon System.
