<h1>Build #1549299689</h1>
<h2>Date: 2019-02-04</h2>
<div class="changelog">
    - Add CMS main menu categories.
</div>
<?php

$categoryDef = <<<EOT
contents # Contents # Inhalte
products # Products & categories # Produkte & Kategorien
productlists # Product lists # Produktlisten
discounts # Discounts & vouchers # Rabatte & Gutscheine
orders # Orders # Bestellungen
checkout # Checkout # Checkout
externalusers # Customers / users # Kunden / Benutzer
ratings # Shop ratings # Shop-Bewertungen
search # Search # Suche
analytics # Analytics # Analyse
communication # Communication # Kommunikation
dataexchange # Data exchange # Datenaustausch
layout # Layout # Layout
internationalization # Internationalization # Internationalisierung
internalusers # Internal users # Interne Benutzer
logs # Logs # Logs
routing # Routing # Routing
system # System # System
EOT;

$categoryLines = \explode(PHP_EOL, $categoryDef);
$position = 0;

foreach ($categoryLines as $categoryLine) {
    $id = TCMSLogChange::createUnusedRecordId('cms_menu_category');
    [$systemName, $nameEn, $nameDe] = \explode(' # ', $categoryLine);

    $data = TCMSLogChange::createMigrationQueryData('cms_menu_category', 'en')
        ->setFields([
            'id' => $id,
            'name' => $nameEn,
            'position' => $position,
            'system_name' => $systemName,
        ])
    ;
    TCMSLogChange::insert(__LINE__, $data);

    $data = TCMSLogChange::createMigrationQueryData('cms_menu_category', 'de')
        ->setFields([
            'name' => $nameDe,
        ])
        ->setWhereEquals([
            'id' => $id,
        ])
    ;
    TCMSLogChange::update(__LINE__, $data);

    ++$position;
}
