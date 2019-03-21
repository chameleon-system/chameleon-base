<h1>Build #1551797432</h1>
<h2>Date: 2019-03-06</h2>
<div class="changelog">
    - ref #335 migrate unhandled main menu items to sidebar
</div>
<?php

use Doctrine\Common\Collections\Expr\Comparison;

/*
 * Remove some items from the main menu as they are outdated
 */
$data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'en')
    ->setFields([
        'cms_content_box_id' => '',
    ])
    ->setWhereExpressions([
        new Comparison('name', Comparison::IN, [
            'cms_dark_site_content',
            'shop_suggest_article_log',
            'shop_variant_type',
        ]),
    ])
;
TCMSLogChange::update(__LINE__, $data);

/**
 * @var ChameleonSystem\CoreBundle\Bridge\Chameleon\Migration\Service\MainMenuMigrator $mainMenuMigrator
 */
$mainMenuMigrator = \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.service.main_menu_migrator');
$mainMenuMigrator->migrateUnhandledContentBoxes();
$mainMenuMigrator->migrateUnhandledTableMenuItems();
