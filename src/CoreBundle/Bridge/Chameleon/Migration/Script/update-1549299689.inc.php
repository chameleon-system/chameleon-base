<h1>Build #1549299689</h1>
<h2>Date: 2019-02-04</h2>
<div class="changelog">
    - Add CMS main menu categories.
</div>
<?php

$categoryDef = <<<EOT
base # contents             # Contents              # Inhalte               # fas fa-file-alt
shop # products             # Products & categories # Produkte & Kategorien # fas fa-cubes
shop # productlists         # Product lists         # Produktlisten         # fas fa-boxes
shop # discounts            # Discounts & vouchers  # Rabatte & Gutscheine  # fas fa-gift
shop # orders               # Orders                # Bestellungen          # fas fa-shopping-basket
shop # checkout             # Checkout              # Checkout              # fas fa-truck-monster
base # externalusers        # Customers / users     # Kunden / Benutzer     # fas fa-user
shop # ratings              # Shop ratings          # Shop-Bewertungen      # fas fa-star
base # search               # Search                # Suche                 # fas fa-search
base # analytics            # Analytics             # Analyse               # fas fa-chart-pie
base # communication        # Communication         # Kommunikation         # fas fa-at
base # dataexchange         # Data exchange         # Datenaustausch        # fas fa-exchange-alt
base # layout               # Layout                # Layout                # far fa-address-card
base # internationalization # Internationalization  # Internationalisierung # fas fa-flag
base # internalusers        # Internal users        # Interne Benutzer      # fas fa-user-lock
base # logs                 # Logs                  # Logs                  # fas fa-clipboard-list
base # routing              # Routing               # Routing               # fas fa-random
base # system               # System                # System                # fas fa-cog
EOT;

$categoryLines = \explode(PHP_EOL, $categoryDef);
$position = 0;

$isShopSystem = \class_exists(TShop::class);

foreach ($categoryLines as $categoryLine) {
    $id = TCMSLogChange::createUnusedRecordId('cms_menu_category');
    [$systemType, $systemName, $nameEn, $nameDe, $iconFontCssClass] = \preg_split('/\s+#\s+/', $categoryLine);

    if (false === $isShopSystem && 'shop' === $systemType) {
        continue;
    }

    $data = TCMSLogChange::createMigrationQueryData('cms_menu_category', 'en')
        ->setFields([
            'id' => $id,
            'name' => $nameEn,
            'position' => $position,
            'system_name' => $systemName,
            'icon_font_css_class' => $iconFontCssClass,
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
