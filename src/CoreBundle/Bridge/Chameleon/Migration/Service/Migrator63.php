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

use Doctrine\DBAL\Connection;

class Migrator63
{
    const DEFAULT_ICON_CLASS = 'fas fa-file';

    /**
     * @var Connection
     */
    private $databaseConnection;

    /**
     * @var array
     */
    private $iconMapping = [];

    /**
     * @var array
     */
    private $mainCategoryMapping = [];

    /**
     * @var \TTools
     */
    private $tools;

    public function __construct(
        Connection $databaseConnection,
        \TTools $tools)
    {
        $this->databaseConnection = $databaseConnection;
        $this->tools = $tools;

        $this->setIconMapping();
        $this->setMainCategoryMapping();
    }

    public function addIconMapping(array $iconMapping): void
    {
        $this->iconMapping = \array_merge($iconMapping, $this->iconMapping);
    }

    private function setIconMapping(): void
    {
        $this->iconMapping = array(
            'accept.png' => 'fas fa-check-circle',
            'action_refresh.gif' => 'fas fa-sync',
            'action_save.gif' => 'far fa-save',
            'action_stop.gif' => 'fas fa-times-circle',
            'add.png' => 'fas fa-plus-circle',
            'application_cascade.png' => 'fas fa-clone',
            'application_form_magnify.png' => 'fab fa-searchengin',
            'application_form.png' => 'fas fa-list',
            'application_get.png' => 'fas fa-upload',
            'application_go.png' => 'fas fa-external-link-square-alt',
            'application_side_expand.png' => 'fas fa-file-export',
            'application_side_list.png' => 'fas fa-boxes',
            'application_side_tree.png' => 'fas fa-cogs',
            'application_view_detail.png' => 'fas fa-truck',
            'arrow_out.png' => 'far fa-arrow-alt-circle-right',
            'arrow_refresh.png' => 'fas fa-exchange-alt',
            'arrow_switch.png' => 'fas fa-random',
            'award_star_add.png' => 'fas fa-medal',
            'basket_error.png' => 'fas fa-shopping-basket',
            'basket.png' => 'fas fa-tasks',
            'box-orange.png' => 'fas fa-gift',
            'brick_edit.png' => 'fas fa-file-alt',
            'brick.png' => 'fas fa-palette',
            'bricks.png' => 'fas fa-cubes',
            'bug_error.png' => 'fas fa-bug',
            'building_edit.png' => 'far fa-comments',
            'calculator_edit.png' => 'far fa-object-ungroup',
            'cart_go.png' => 'fas fa-check-double',
            'chart_curve_edit.png' => 'fas fa-chart-bar',
            'chart_curve_go.png' => 'fas fa-chart-line',
            'chart_organisation.png' => 'fas fa-bars',
            'chart_pie.png' => 'fas fa-chart-pie',
            'clock_add.png' => 'fas fa-business-time',
            'cog.png' => 'fas fa-truck-monster',
            'color_swatch.png' => 'fas fa-braille',
            'color_wheel.png' => 'fas fa-desktop',
            'comment.gif' => 'fas fa-comments',
            'comments.png' => 'fas fa-comment-dots',
            'comment_yellow.gif' => 'far fa-comment',
            'database_gear.png' => 'fas fa-filter',
            'database_lightning.png' => 'fas fa-poo-storm',
            'database_link.png' => 'fas fa-search',
            'database_table.png' => 'fas fa-table',
            'date_go.png' => 'fas fa-eye',
            'email_add.png' => 'fas fa-mail-bulk',
            'email_edit.png' => 'fas fa-envelope',
            'email_go.png' => 'fas fa-user-edit',
            'email_link.png' => 'fas fa-at',
            'eye.png' => 'far fa-eye',
            'file_font_truetype.gif' => 'fas fa-fingerprint',
            'folder_brick.png' => 'fas fa-shapes',
            'folder_edit.png' => 'fas fa-file-alt',
            'folder_star.png' => 'fas fa-gift',
            'folder_user.png' => 'fas fa-users',
            'group_gear.png' => 'fas fa-user-cog',
            'group.png' => 'fas fa-users',
            'icon_attachment.gif' => 'far fa-file',
            'icon_component.gif' => 'fas fa-th-large',
            'icon_extension.gif' => 'fas fa-puzzle-piece',
            'icon_history.gif' => 'fas fa-history',
            'icon_package_get.gif' => 'fas fa-screwdriver',
            'icon_security.gif' => 'fas fa-users-cog',
            'icon_world.gif' => 'fas fa-flag',
            'image_edit.png' => 'far fa-file-image',
            'images.png' => 'fab fa-usb',
            'interface_installer.gif' => 'fas fa-layer-group',
            'layout_content.png' => 'fas fa-desktop',
            'layout.png' => 'fas fa-haykal',
            'lorry.png' => 'far fa-money-bill-alt',
            'medal_gold_2.png' => 'fas fa-star',
            'money_add.png' => 'fas fa-money-check-alt',
            'money_euro.png' => 'fas fa-hand-holding-usd',
            'money.png' => 'fas fa-money-bill-wave',
            'monitor_edit.png' => 'fas fa-cog',
            'newspaper.png' => 'fas fa-user-check',
            'package.png' => 'fas fa-truck-loading',
            'page_edit.png' => 'fas fa-clipboard-list',
            'page_html.gif' => 'fas fa-globe-americas',
            'page_next.gif' => 'fas fa-file-export',
            'page_package.gif' => 'far fa-play-circle',
            'page_text.gif' => 'fas fa-terminal',
            'page_white_find.png' => 'far fa-list-alt',
            'page_white_key.png' => 'fas fa-wrench',
            'photo.png' => 'far fa-address-card',
            'photos.png' => 'far fa-images',
            'pictures.png' => 'far fa-address-card',
            'pilcrow.png' => 'fas fa-pencil-ruler',
            'prohibited.png' => 'fas fa-user-slash',
            'report.png' => 'fas fa-clipboard-list',
            'resultset_next.png' => 'fas fa-play',
            'server.png' => 'fas fa-store-alt',
            'sitemap_color.png' => 'fas fa-boxes',
            'status_busy.png' => 'fas fa-user-shield',
            'textfield.png' => 'fas fa-edit',
            'user_comment.png' => 'fas fa-user-edit',
            'user_gray.png' => 'fas fa-user-tie',
            'user.png' => 'fas fa-user',
            'user_suit.png' => 'fas fa-user-lock',
            'weather_clouds.png' => 'fas fa-cloud',
            'world_edit.png' => 'fas fa-map-marked-alt',
            'world.png' => 'fas fa-home',
            'wrench.png' => 'fas fa-industry',
            'map.png' => 'fas fa-map-marked',
            'weather_sun.png' => 'fas fa-sun',
            'weather_snow.png' => 'fas fa-snowflake',
            'house.png' => 'fas fa-home',
            'user_female.png' => 'fas fa-user',
            'cake.png' => 'fas fa-birthday-cake',
            'image.gif' => 'fas fa-image',
            'gb.png' => 'fas fa-flag-usa',
            'de.png' => 'far fa-flag',
            'layout_header.png' => 'fas fa-object-group',
            'icon_get_world.gif' => 'fas fa-download',
            'application_form_edit.png' => 'fas fa-newspaper',
            'page_white_text.png' => 'far fa-newspaper',
            'list_extensions.gif' => 'fas fa-clipboard-list',
            'arrow_divide.png' => 'fas fa-random',
            'map_magnify.png' => 'fas fa-map-marked-alt',
            'icon_user.gif' => 'fas fa-user',
            'user_orange.png' => 'far fa-user',
            'medal_gold_3.png' => 'fas fa-star-half-alt',
            'pencil.png' => 'fas fa-pen-square',
            'group_key.png' => 'fas fa-user-friends',
            'world_go.png' => 'fas fa-file-import',
            'tick.png' => 'fas fa-clipboard-check',
            'chart_bar.png' => 'fas fa-chart-bar',
            'star.png' => 'fas fa-star',
            'angel.png' => 'fas fa-venus',
            'film.png' => 'fas fa-film',
            'mountain.png' => 'fas fa-mountain',
            'clock.png' => 'far fa-clock',
            'group_go.png' => 'fas fa-user-friends',
            'list_world.gif' => 'fas fa-globe-europe',
            'hike.gif' => 'fas fa-hiking',
            'book_open.png' => 'fas fa-book-open',
            'book.png' => 'fas fa-book',
            'books.png' => 'fas fa-book',
            'folder.png' => 'fas fa-folder',
            'folder_link.png' => 'fas fa-folder-open',
            'flag_blue.png' => 'far fa-flag',
            'tag_blue_edit.png' => 'fas fa-tags',
            'office-document.png' => 'fas fa-file-alt',
            'knewsticker.png' => 'fas fa-bullhorn',
            'kcmdf.png' => 'fas fa-object-ungroup',
            'news.png' => 'far fa-newspaper',
            'weather-few-clouds.png' => 'fas fa-cloud-sun',
            'thumbnail.png' => 'fas fa-images',
        );
    }

    /**
     * Returns map all table icons with new font icons based on the file names.
     *
     * @return array
     */
    public function getIconMapping(): array
    {
        return $this->iconMapping;
    }

    /**
     * Returns mapping of old main category groups to new sidebar menu groups.
     */
    private function setMainCategoryMapping(): void
    {
        $this->mainCategoryMapping = array(
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
    }

    public function addMainCategoryMapping(array $mainCategoryMapping): void
    {
        $this->mainCategoryMapping = \array_merge($mainCategoryMapping, $this->mainCategoryMapping);
    }

    /**
     * Returns mapping of old main category groups to new sidebar menu groups.
     */
    public function getMainCategoryMapping(): array
    {
        return $this->mainCategoryMapping;
    }

    public function migrateUnhandledTableMenuItems(): void
    {
        // get all main categories
        $statement = $this->databaseConnection->executeQuery('SELECT * FROM `cms_content_box` ORDER BY `name` ASC');

        $mainCategoryMapping = $this->getMainCategoryMapping();

        while (false !== $row = $statement->fetch()) {
            if (isset($mainCategoryMapping[$row['system_name']])) {
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
            $systemName = $this->tools->sanitizeFilename($row['name']);
        }

        $query = 'SELECT `position` FROM `cms_menu_category` ORDER BY `position` DESC';
        $lastPosition = (int) $this->databaseConnection->fetchColumn($query);
        ++$lastPosition;

        $iconFontClass = $this->getFontIconStyleByImage($row['icon_list']);

        // create missing menu group
        $menuGroupId = \TCMSLogChange::createUnusedRecordId('cms_menu_category');
        $data = \TCMSLogChange::createMigrationQueryData('cms_menu_category', 'en')
            ->setFields([
                'name' => \trim($row['name__en']),
                'system_name' => $systemName,
                'icon_font_css_class' => $iconFontClass,
                'position' => $lastPosition,
                'id' => $menuGroupId,
            ])
        ;
        \TCMSLogChange::insert(__LINE__, $data);

        $data = \TCMSLogChange::createMigrationQueryData('cms_menu_category', 'de')
            ->setFields([
                'name' => \trim($row['name']),
                'system_name' => $systemName,
                'icon_font_css_class' => $iconFontClass,
                'position' => $lastPosition,
            ])
            ->setWhereEquals([
                'id' => $menuGroupId,
            ])
        ;
        \TCMSLogChange::update(__LINE__, $data);

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
            $menuItemId = \TCMSLogChange::createUnusedRecordId('cms_menu_item');

            $iconFontClass = $row['icon_font_css_class'];

            if ('' === $iconFontClass) {
                $iconFontClass = $this->getFontIconStyleByImage($row['icon_list']);
            }

            $menuItemData = [
                'id' => $menuItemId,
                'name' => \trim($row['translation']),
                'target' => $row['id'],
                'target_table_name' => 'cms_tbl_conf',
                'icon_font_css_class' => $iconFontClass,
                'position' => $position,
                'cms_menu_category_id' => $newMenuGroupId,
            ];
            foreach ($languageList as $language) {
                if (true === \array_key_exists("translation__$language", $row)) {
                    $menuItemData["name__$language"] = \trim($row["translation__$language"]);
                }
            }

            $this->databaseConnection->insert('cms_menu_item', $menuItemData);
            ++$position;
        }
    }

    private function getFontIconStyleByImage(string $iconFilename): string
    {
        if ('' === $iconFilename || false === isset($this->iconMapping[$iconFilename])) {
            return self::DEFAULT_ICON_CLASS;
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
