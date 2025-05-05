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
use Doctrine\DBAL\Connection;

class ModuleIconMigrator
{
    /**
     * @var array
     */
    private $iconMapping = [];

    /**
     * @var Connection
     */
    private $databaseConnection;

    public function __construct(
        Connection $databaseConnection
    ) {
        $this->databaseConnection = $databaseConnection;
        $this->iconMapping = IconMapping::ICON_MAPPING;
    }

    /**
     * @return void
     */
    public function migrateModuleIcon(string $module, array $additionalIconMapping = [])
    {
        $query = 'SELECT * FROM `cms_tpl_module` WHERE `classname` = :module';
        $row = $this->databaseConnection->fetchAssociative($query, ['module' => $module]);

        if (false === $row) {
            \TCMSLogChange::addInfoMessage(\sprintf('Template Module "%s" could not be found.', $module), \TCMSLogChange::INFO_MESSAGE_LEVEL_ERROR);

            return;
        }

        $this->migrateModuleIconByRecord($row, $additionalIconMapping);
    }

    /**
     * @return void
     */
    public function migrateUnhandledModules(array $additionalIconMapping = [])
    {
        $moduleRecordList = $this->databaseConnection->fetchAllAssociative("SELECT * FROM `cms_tpl_module` WHERE `icon_font_css_class` = '' AND `icon_list` != ''");

        foreach ($moduleRecordList as $moduleRecord) {
            $this->migrateModuleIconByRecord($moduleRecord, $additionalIconMapping);
        }
    }

    /**
     * @return void
     */
    private function migrateModuleIconByRecord(array $moduleRecord, array $additionalIconMapping = [])
    {
        $iconFontClass = $this->getFontIconStyleByImage($moduleRecord['icon_list'], $additionalIconMapping);

        $query = 'UPDATE `cms_tpl_module`
          SET `icon_font_css_class` = :iconFontClass
          WHERE `id` = :id';

        $this->databaseConnection->executeQuery($query, ['id' => $moduleRecord['id'], 'iconFontClass' => $iconFontClass]);
    }

    private function getFontIconStyleByImage(string $iconFilename, array $additionalIconMapping = []): string
    {
        $iconMapping = \array_merge($additionalIconMapping, $this->iconMapping);

        if ('' === $iconFilename || false === isset($iconMapping[$iconFilename])) {
            \TCMSLogChange::addInfoMessage(\sprintf('No icon mapping found for %s.', $iconFilename), \TCMSLogChange::INFO_MESSAGE_LEVEL_ERROR);

            return '';
        }

        return $iconMapping[$iconFilename];
    }
}
