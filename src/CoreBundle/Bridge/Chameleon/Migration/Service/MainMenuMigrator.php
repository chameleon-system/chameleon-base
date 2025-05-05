<?php
/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Bridge\Chameleon\Migration\Service;

use ChameleonSystem\CoreBundle\Bridge\Chameleon\Migration\Mapping\IconMapping;
use ChameleonSystem\CoreBundle\Util\FieldTranslationUtil;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;

class MainMenuMigrator
{
    /**
     * @var Connection
     */
    private $databaseConnection;

    /**
     * @var FieldTranslationUtil
     */
    private $fieldTranslationUtil;

    /**
     * @var array
     */
    private $iconMapping = [];

    /**
     * @var array
     */
    private $mainCategoryMapping = [];

    public function __construct(
        Connection $databaseConnection,
        FieldTranslationUtil $fieldTranslationUtil)
    {
        $this->databaseConnection = $databaseConnection;
        $this->fieldTranslationUtil = $fieldTranslationUtil;
        $this->iconMapping = IconMapping::ICON_MAPPING;

        $this->initMainCategoryMapping();
    }

    /**
     * Returns a mapping from old table icons based on file names to new icons based on an icon font.
     */
    public function getIconMapping(): array
    {
        return $this->iconMapping;
    }

    /**
     * Returns a mapping of old main menu content boxes to new sidebar menu categories.
     */
    private function initMainCategoryMapping(): void
    {
        $this->mainCategoryMapping = [
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
            'shop_discounts_and_vouchers' => 'discounts',
            'shop_order_process_settings' => 'checkout',
            'routing' => 'routing',
            'system_community' => 'externalusers',
        ];
    }

    /**
     * Returns a mapping of old main menu content boxes to new sidebar menu categories.
     */
    public function getMainCategoryMapping(): array
    {
        return $this->mainCategoryMapping;
    }

    /**
     * Creates new main menu items for all table entries in old content boxes for which no main menu item exists yet.
     * This method only works for standard content boxes and content boxes provided by $additionalMainCategoryMapping.
     * It is expected that all required main menu categories already exist when this method is called.
     *
     * @param array $additionalMainCategoryMapping Mapping from old content box system names to new main menu category
     *                                             system names
     */
    public function migrateUnhandledTableMenuItems(array $additionalMainCategoryMapping = []): void
    {
        $contentBoxRows = $this->databaseConnection->fetchAllAssociative('SELECT * FROM `cms_content_box`');
        $categoryRows = $this->databaseConnection->fetchAllAssociative('SELECT * FROM `cms_menu_category`');
        $categories = [];
        foreach ($categoryRows as $categoryRow) {
            $categories[$categoryRow['system_name']] = $categoryRow['id'];
        }

        $mainCategoryMapping = $this->getMainCategoryMapping();
        $mainCategoryMapping = \array_merge($additionalMainCategoryMapping, $mainCategoryMapping);

        foreach ($contentBoxRows as $contentBoxRow) {
            if (false === isset($mainCategoryMapping[$contentBoxRow['system_name']])) {
                continue;
            }

            $oldContentBoxId = $contentBoxRow['id'];
            $newCategorySystemName = $mainCategoryMapping[$contentBoxRow['system_name']];
            $newCategoryId = $categories[$newCategorySystemName];

            $this->createAllUnhandledOldMenuItemsForCategory($oldContentBoxId, $newCategoryId);
        }
    }

    /**
     * @throws DBALException
     */
    public function migrateUnhandledContentBoxes(): void
    {
        $contentBoxRows = $this->databaseConnection->fetchAllAssociative('SELECT * FROM `cms_content_box`');
        $mainCategoryMapping = $this->getMainCategoryMapping();

        foreach ($contentBoxRows as $contentBoxRow) {
            $contentBoxSystemName = $contentBoxRow['system_name'];
            if ('' === $contentBoxSystemName) {
                continue;
            }
            if (true === isset($mainCategoryMapping[$contentBoxSystemName])) {
                continue;
            }
            $this->migrateContentBox($contentBoxSystemName);
        }
    }

    /**
     * Creates a new main menu category with the same name as the content box with $oldContentBoxSystemName and the
     * same menu items.
     *
     * @throws DBALException
     */
    public function migrateContentBox(string $oldContentBoxSystemName): void
    {
        $query = 'SELECT * FROM `cms_content_box` WHERE `system_name` = :systemName';
        $row = $this->databaseConnection->fetchAssociative($query, ['systemName' => $oldContentBoxSystemName]);
        if (false === $row) {
            \TCMSLogChange::addInfoMessage(\sprintf('No content box found for system name "%s"', $oldContentBoxSystemName), \TCMSLogChange::INFO_MESSAGE_LEVEL_ERROR);

            return;
        }

        $newCategoryId = $this->createMenuCategoryFromContentBox($row);
        $oldContentBoxId = $row['id'];

        $this->createAllUnhandledOldMenuItemsForCategory($oldContentBoxId, $newCategoryId);
    }

    private function createMenuCategoryFromContentBox(array $row, array $additionalIconMapping = []): string
    {
        $systemName = $row['system_name'];

        $query = 'SELECT `position` FROM `cms_menu_category` ORDER BY `position` DESC';
        $lastPosition = (int) $this->databaseConnection->fetchOne($query);
        ++$lastPosition;

        $iconFontClass = $this->getFontIconStyleByImage($row['icon_list'], $additionalIconMapping);

        $englishLanguage = \TdbCmsLanguage::GetNewInstance();
        $englishLanguage->LoadFromField('iso_6391', 'en');

        $menuCategoryId = \TCMSLogChange::createUnusedRecordId('cms_menu_category');
        $data = \TCMSLogChange::createMigrationQueryData('cms_menu_category', 'en')
            ->setFields([
                'name' => \trim($row[$this->fieldTranslationUtil->getTranslatedFieldName('cms_menu_category', 'name', $englishLanguage)]),
                'system_name' => $systemName,
                'icon_font_css_class' => $iconFontClass,
                'position' => $lastPosition,
                'id' => $menuCategoryId,
            ])
        ;
        \TCMSLogChange::insert(__LINE__, $data);

        $germanLanguage = \TdbCmsLanguage::GetNewInstance();
        $germanLanguage->LoadFromField('iso_6391', 'de');

        $data = \TCMSLogChange::createMigrationQueryData('cms_menu_category', 'de')
            ->setFields([
                'name' => \trim($row[$this->fieldTranslationUtil->getTranslatedFieldName('cms_menu_category', 'name', $germanLanguage)]),
                'system_name' => $systemName,
                'icon_font_css_class' => $iconFontClass,
                'position' => $lastPosition,
            ])
            ->setWhereEquals([
                'id' => $menuCategoryId,
            ])
        ;
        \TCMSLogChange::update(__LINE__, $data);

        return $menuCategoryId;
    }

    private function createAllUnhandledOldMenuItemsForCategory(string $oldContentBoxId, string $newCategoryId, array $additionalIconMapping = []): void
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
        $result = $this->databaseConnection->executeQuery($query, ['contentBoxId' => $oldContentBoxId, '']);

        $position = $this->databaseConnection->fetchOne('SELECT MAX(`position`) + 1 FROM `cms_menu_item` WHERE `cms_menu_category_id` = ?', [$newCategoryId]) ?? 0;
        while (false !== $row = $result->fetchAssociative()) {
            $menuItemId = \TCMSLogChange::createUnusedRecordId('cms_menu_item');

            $iconFontClass = $row['icon_font_css_class'];

            if ('' === $iconFontClass) {
                $iconFontClass = $this->getFontIconStyleByImage($row['icon_list'], $additionalIconMapping);
            }

            $menuItemData = [
                'id' => $menuItemId,
                'name' => \trim($row['translation']),
                'target' => $row['id'],
                'target_table_name' => 'cms_tbl_conf',
                'icon_font_css_class' => $iconFontClass,
                'position' => $position,
                'cms_menu_category_id' => $newCategoryId,
            ];
            foreach ($languageList as $language) {
                if (true === \array_key_exists('translation__'.$language, $row)) {
                    $menuItemData['name__'.$language] = \trim($row['translation__'.$language]);
                }
            }

            $this->databaseConnection->insert('cms_menu_item', $menuItemData);
            ++$position;
        }
    }

    private function getFontIconStyleByImage(string $iconFilename, array $additionalIconMapping = []): string
    {
        $iconMapping = \array_merge($additionalIconMapping, $this->iconMapping);

        if ('' === $iconFilename || false === isset($iconMapping[$iconFilename])) {
            return '';
        }

        return $iconMapping[$iconFilename];
    }

    private function getAllSupportedLanguages(): array
    {
        $primaryLanguage = $this->databaseConnection->fetchOne('SELECT `translation_base_language_id` FROM `cms_config`');

        $query = 'SELECT `iso_6391`
          FROM `cms_language` AS l
          JOIN `cms_config_cms_language_mlt` AS mlt ON l.`id` = mlt.`target_id`
          WHERE l.`id` <> ?';
        $result = $this->databaseConnection->executeQuery($query, [$primaryLanguage]);

        $languageList = [];
        while (false !== $row = $result->fetchOne()) {
            $languageList[] = $row['iso_6391'];
        }
        $result->free();

        return $languageList;
    }
}
