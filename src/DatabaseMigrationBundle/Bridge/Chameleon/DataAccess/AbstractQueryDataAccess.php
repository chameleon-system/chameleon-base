<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\DatabaseMigrationBundle\Bridge\Chameleon\DataAccess;

use Doctrine\DBAL\Connection;

class AbstractQueryDataAccess implements AbstractQueryDataAccessInterface
{
    /**
     * @var Connection
     */
    private $databaseConnection;

    public function __construct(Connection $databaseConnection)
    {
        $this->databaseConnection = $databaseConnection;
    }

    /**
     * {@inheritdoc}
     *
     * @return string|null
     */
    public function getBaseLanguageIso()
    {
        return \TdbCmsConfig::GetInstance()->GetFieldTranslationBaseLanguage()->fieldIso6391;
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslatedFieldsForTable($tableName)
    {
        $query = "SELECT `cms_field_conf`.`name` FROM `cms_field_conf`
          JOIN `cms_tbl_conf`
          ON `cms_field_conf`.`cms_tbl_conf_id` = `cms_tbl_conf`.`id`
          WHERE `cms_tbl_conf`.`name` = :tableName
          AND `cms_field_conf`.`is_translatable` = '1'";
        $result = $this->databaseConnection->fetchAllAssociative($query, [
            'tableName' => $tableName,
        ]);

        return array_map(function (array $array) {
            return $array['name'];
        }, $result);
    }
}
