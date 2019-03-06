<h1>Build #1551797432</h1>
<h2>Date: 2019-03-06</h2>
<div class="changelog">
    - ref #335 migrate unhandled main menu items to sidebar
</div>
<?php
// load icon mapping from external file
include __DIR__.'/includes/iconMapping.inc.php';

class migrateUnhandledMenuItems
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $databaseConnection;

    /**
     * @var array
     */
    private $mainCategoryMapping;

    /**
     * @var array
     */
    private $iconMapping;

    public function __construct(Doctrine\DBAL\Connection $databaseConnection, array $mainCategoryMapping, array $iconMapping)
    {
        $this->databaseConnection = $databaseConnection;
        $this->mainCategoryMapping = $mainCategoryMapping;
        $this->iconMapping = $iconMapping;
    }

    public function executeMigration(): void
    {
        // get all main categories
        $statement = $this->databaseConnection->executeQuery('SELECT * FROM `cms_content_box` ORDER BY `name` ASC');

        while (false !== $row = $statement->fetch()) {
            if (isset($this->mainCategoryMapping[$row['system_name']])) {
                $sidebarCategorySystemName = $this->mainCategoryMapping[$row['system_name']];
                $query = 'SELECT * FROM `cms_menu_category` WHERE `system_name` = :systemName';
                $newMenuGroupId = $this->databaseConnection->fetchColumn($query, ['systemName' => $sidebarCategorySystemName]);
                $oldMenuGroupId = $row['id'];

                $this->createAllUnhandledOldMenuItemsForGroup($oldMenuGroupId, $newMenuGroupId);
            } else {
                $newMenuGroupId = $this->createMenuCategory($row);
                $oldMenuGroupId = $row['id'];

                $this->createAllUnhandledOldMenuItemsForGroup($oldMenuGroupId, $newMenuGroupId);
            }
        }
        $statement->closeCursor();
    }

    private function createMenuCategory(array $row): string
    {
        $systemName = $row['system_name'];

        if ('' === $systemName) {
            $systemName = TTools::sanitizeFilename($row['name']);
        }

        $query = 'SELECT `position` FROM `cms_menu_category` ORDER BY `position` DESC';
        $lastPosition = (int) $this->databaseConnection->fetchColumn($query);
        $lastPosition++;

        $iconFontClass = $this->getFontIconStyleByImage($row['icon_list']);

        // create missing menu group
        $menuGroupId = TCMSLogChange::createUnusedRecordId('cms_menu_category');
        $data = TCMSLogChange::createMigrationQueryData('cms_menu_category', 'en')
            ->setFields([
                'name' => $row['name__en'],
                'system_name' => $systemName,
                'icon_font_css_class' => $iconFontClass,
                'position' => $lastPosition,
                'id' => $menuGroupId,
            ])
        ;
        TCMSLogChange::insert(__LINE__, $data);

        $data = TCMSLogChange::createMigrationQueryData('cms_menu_category', 'de')
            ->setFields([
                'name' => $row['name'],
                'system_name' => $systemName,
                'icon_font_css_class' => $iconFontClass,
                'position' => $lastPosition,
            ])
            ->setWhereEquals([
                'id' => $menuGroupId,
            ])
        ;
        TCMSLogChange::update(__LINE__, $data);

        return $menuGroupId;
    }

    private function createAllUnhandledOldMenuItemsForGroup(string $oldMenuGroupId, string $newMenuGroupId): void
    {
        $languageList = $this->getAllSupportedLanguages();

        $query = "SELECT DISTINCT `cms_tbl_conf`.* 
                FROM `cms_tbl_conf`
               WHERE `cms_tbl_conf`.`cms_content_box_id` = :contentBoxId
                 AND `cms_tbl_conf`.`id` NOT IN (
                     SELECT `cms_menu_item`.`target` 
                       FROM `cms_menu_item` 
                      WHERE `cms_menu_item`.`target` = `cms_tbl_conf`.`id` 
                        AND `cms_menu_item`.`target_table_name` = 'cms_tbl_conf'
                   )
            ORDER BY `cms_tbl_conf`.`translation` ASC
";
        $statement = $this->databaseConnection->executeQuery($query, ['contentBoxId' => $oldMenuGroupId, '']);

        $position = 0;
        while (false !== $row = $statement->fetch()) {
            $menuItemId = TCMSLogChange::createUnusedRecordId('cms_menu_item');

            $iconFontClass = $row['icon_font_css_class'];

            if ('' === $iconFontClass) {
                $iconFontClass = $this->getFontIconStyleByImage($row['icon_list']);
            }

            $menuItemData = [
                'id' => $menuItemId,
                'name' => $row['translation'],
                'target' => $row['id'],
                'target_table_name' => 'cms_tbl_conf',
                'icon_font_css_class' => $iconFontClass,
                'position' => $position,
                'cms_menu_category_id' => $newMenuGroupId,
            ];
            foreach ($languageList as $language) {
                if (true === \array_key_exists("translation__$language", $row)) {
                    $menuItemData["name__$language"] = $row["translation__$language"];
                }
            }

            $this->databaseConnection->insert('cms_menu_item', $menuItemData);
            $position++;
        }
    }

    private function getFontIconStyleByImage(string $iconFilename): string
    {
        if ('' === $iconFilename || false === isset($this->iconMapping[$iconFilename])) {
            return 'fas fa-file';
        }

        return $this->iconMapping[$iconFilename];
    }

    private function getAllSupportedLanguages(): array
    {
        $primaryLanguage = $this->databaseConnection->fetchColumn('SELECT `translation_base_language_id` FROM `cms_config`');

        $query = 'SELECT `iso_6391`
          FROM `cms_language` AS l
          JOIN `cms_config_cms_language_mlt` AS mlt ON l.`id` = mlt.`target_id`
          WHERE l.`id` <> ?';
        $statement = $this->databaseConnection->executeQuery($query, [$primaryLanguage]);

        $languageList = [];
        while (false !== $row = $statement->fetch()) {
            $languageList[] = $row['iso_6391'];
        }
        $statement->closeCursor();

        return $languageList;
    }
}

$databaseConnection = TCMSLogChange::getDatabaseConnection();

// mapping of old main category groups to new sidebar menu groups
$mainCategoryMapping = array(
    'system_website' => 'contents',
    'shop_article' => 'products',
    'shop_orders' => 'orders',
    'customer_shop-einstellungen' => 'system',
    'system_newsletter' => 'communication',
    'shop_user' => 'externalusers',
    'system_search' => 'search',
    'system_individual_lists_settings' => 'system',
    'shop_donation_vouchers' => 'discounts',
    'system_portal_settings' => 'system',
    'system_admin' => 'system',
    'system_user_management' => 'internalusers',
    'system_logs' => 'logs',
    'shop_article_list_filter' => 'productlists',
    'system_press' => 'contents',
    'shop_discount_and_vouchers' => 'discounts',
    'shop_order_process_settings' => 'checkout',
    'routing' => 'routing',
    'system_community' => 'externalusers',
);

$migrateUnhandledMenuItems = new migrateUnhandledMenuItems($databaseConnection, $mainCategoryMapping, $iconMapping);
$migrateUnhandledMenuItems->executeMigration();
