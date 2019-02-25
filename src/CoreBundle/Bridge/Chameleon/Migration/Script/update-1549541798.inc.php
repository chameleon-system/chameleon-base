<h1>Build #1549541798</h1>
<h2>Date: 2019-02-07</h2>
<div class="changelog">
    - Rename tables and modules
</div>
<?php

$tableList = <<< EOT
cms_tpl_page # Pages # Seiten
pkg_multi_module_set # Multimodules # Multimodule
pkg_comment_type # Comment types # Kommentartypen
shop_article # Products # Produkte
shop_category # Product categories # Produktkategorien
shop_manufacturer # Manufacturer / brands # Hersteller / Marken
shop_manufacturer_module_conf # Manufacturer & brands module configuration # Hersteller / Marken Moduleinstellungen
shop_variant_set # Variant sets # Variantensets
pkg_shop_article_preorder # Preordered products # Vorgemerkte Produkte
shop_article_group # Product groups # Warengruppen
shop_article_type # Product types # Produkttypen
shop_article_marker # Product characteristics # Produktmerkmale
shop_contributor # Contributors # Beitragende Personen
shop_contributor_type # Contributor types # Arten von beitragenden Personen
shop_article_document_type # Document types for products # Dokumentarten bei Produkten
pkg_shop_listfilter # List filters # Listenfilter
pkg_shop_listfilter_item_type # Filter types # Filtertypen
shop_module_article_list_filter # Product selection filters # Produktauswahlfilter
shop_module_articlelist_orderby # Sort order for product lists # Produktlistensortierung
pkg_shop_payment_transaction_type # Financial transaction types # Finanztransaktionstypen
shop_shipping_type # Shipping types # Versandkostenarten
cms_locals # Regions # Regionen
pkg_shop_rating_service_widget_config # Rating widgets # Bewertungs-Widgets
pkg_shop_rating_service # Rating services # Bewertungsdienste
shop_search_log # Search log # Suchanfragen
shop_search_indexer # Shop search index status # Shop-Suchindex-Status
shop_search_query # Shop search index # Shop-Suchindex
pkg_external_tracker # External tracking services # Externe Tracking-Dienste
pkg_cms_changelog_set # Changelog # Änderungen nachverfolgen
pkg_shop_statistic_group # Sales groups # Umsatzgruppen
pkg_cms_core_log_channel # Log channels # Log-Channels
pkg_shop_payment_ipn_message_trigger # IPN forwarding # IPN-Weiterleitungen
pkg_newsletter_robinson # Newsletter blacklist # Newsletter-Sperrliste
pkg_newsletter_group # Newsletter subscriber lists # Newsletter-Empfängerlisten
shop # Shop configuration # Shop-Konfiguration
cms_portal # Portals / Websites # Portale / Websites
pkg_cms_counter # System counters # Systemzähler
cms_interface_manager # Configure imports / exports # Importe / Exporte konfigurieren
pkg_csv2sql # Import from CSV # Von CSV importieren
pkg_generic_table_export # Generic table export # Generischer Tabellenexport
pkg_cms_theme # Website themes # Website Themes
pkg_csm_theme_block # Theme blocks # Theme-Blöcke
cms_message_manager_message_type # System message types # Systemnachrichtentypen
shop_variant_display_handler # Variant choices # Auswahlmöglichkeiten für Varianten
shop_variant_type_handler # Variant value display # Anzeige von Variantenwerten
cms_url_alias # Redirects # Weiterleitungen
cms_user # CMS users # CMS Benutzer
EOT;

$moduleList = <<< EOT
articlesearchindex # Generate product search index # Produktsuchindex generieren
shopstats # Sales # Umsätze
sanitycheckbundle # Check system # System prüfen
Interface # Run imports / exports # Importe / Exporte ausführen
EOT;

$tableLines = \explode(PHP_EOL, $tableList);
foreach ($tableLines as $tableLine) {
    [$name, $translationEn, $translationDe] = \explode(' # ', $tableLine);

    $data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'en')
        ->setFields([
            'translation' => $translationEn,
        ])
        ->setWhereEquals([
            'name' => $name,
        ])
    ;
    TCMSLogChange::update(__LINE__, $data);

    $data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'de')
        ->setFields([
            'translation' => $translationDe,
        ])
        ->setWhereEquals([
            'name' => $name,
        ])
    ;
    TCMSLogChange::update(__LINE__, $data);
}

$moduleLines = \explode(PHP_EOL, $moduleList);
foreach ($moduleLines as $moduleLine) {
    [$uniqueCmsName, $nameEn, $nameDe] = \explode(' # ', $moduleLine);

    $data = TCMSLogChange::createMigrationQueryData('cms_module', 'en')
        ->setFields([
            'name' => $nameEn,
        ])
        ->setWhereEquals([
            'uniquecmsname' => $uniqueCmsName,
        ])
    ;
    TCMSLogChange::update(__LINE__, $data);

    $data = TCMSLogChange::createMigrationQueryData('cms_module', 'de')
        ->setFields([
            'name' => $nameDe,
        ])
        ->setWhereEquals([
            'uniquecmsname' => $uniqueCmsName,
        ])
    ;
    TCMSLogChange::update(__LINE__, $data);
}
