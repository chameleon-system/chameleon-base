<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\MediaManagerBundle\Bridge\Chameleon\UsageFinder;

use ChameleonSystem\CoreBundle\Util\UrlUtil;
use ChameleonSystem\MediaManager\DataModel\MediaItemDataModel;
use ChameleonSystem\MediaManager\DataModel\MediaItemUsageDataModel;
use ChameleonSystem\MediaManager\Exception\UsageFinderException;
use ChameleonSystem\MediaManager\Interfaces\MediaItemUsageFinderInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;

abstract class AbstractImageFieldsUsageFinder implements MediaItemUsageFinderInterface
{
    /**
     * @var Connection
     */
    protected $databaseConnection;

    /**
     * @var UrlUtil
     */
    protected $urlUtil;

    public function __construct(Connection $databaseConnection, UrlUtil $urlUtil)
    {
        $this->databaseConnection = $databaseConnection;
        $this->urlUtil = $urlUtil;
    }

    /**
     * {@inheritdoc}
     */
    public function findUsages(MediaItemDataModel $mediaItem)
    {
        $usages = [];

        try {
            $fields = $this->getFields();
        } catch (DBALException $e) {
            throw new UsageFinderException(
                sprintf(
                    'Error getting fields when trying to find usages of media item with ID %s: %s',
                    $mediaItem->getId(),
                    $e->getMessage()
                ), 0, $e
            );
        }
        foreach ($fields as $row) {
            if (false === $this->canHandleFieldWithFieldTypeConstantName($row['fieldTypeConstantName'])) {
                continue;
            }

            $records = $this->getRecordsForField($row['fieldTypeConstantName'], $row, $mediaItem);
            if (0 === count($records)) {
                continue;
            }

            $table = \TdbCmsTblConf::GetNewInstance();
            if (false === $table->Load($row['tableId'])) {
                throw new UsageFinderException(sprintf('Table with ID %s could not be found.', $row['tableId']));
            }
            $field = \TdbCmsFieldConf::GetNewInstance();
            if (false === $field->Load($row['fieldId'])) {
                throw new UsageFinderException(sprintf('Field config with ID %s could not be found.', $row['fieldId']));
            }
            foreach ($records as $record) {
                $tableObject = $this->getTableObject($row['tableName'], $record['id']);
                if (null === $tableObject) {
                    throw new UsageFinderException(
                        sprintf('Record with ID %s in table %s could not be found.', $record['id'], $row['tableName'])
                    );
                }

                $usage = $this->createUsageDataModel($mediaItem, $tableObject, $table, $field);
                $usages[] = $usage;
            }
        }

        return $usages;
    }

    /**
     * return an array of fields like this:
     * array(
     *  array('tableId' => '', 'tableName' => '', 'fieldId' => '', 'fieldName' => '', 'fieldTypeConstantName' => '')
     * ).
     *
     * @return array
     *
     * @throws DBALException
     */
    protected function getFields()
    {
        $fieldTypes = $this->getFieldTypeConstantNamesToHandle();

        $query = '
            SELECT `cms_tbl_conf`.`id` AS tableId, `cms_tbl_conf`.`name` AS tableName, `cms_field_conf`.`id` AS fieldId,  `cms_field_conf`.`name` AS fieldName, `cms_field_type`.`constname` AS fieldTypeConstantName
              FROM `cms_tbl_conf`
               INNER JOIN `cms_field_conf` ON `cms_tbl_conf`.`id` = `cms_field_conf`.`cms_tbl_conf_id`
               INNER JOIN `cms_field_type` ON `cms_field_conf`.`cms_field_type_id` = `cms_field_type`.`id`
              WHERE `cms_field_type`.`constname` IN (:fieldTypes)
        ';

        $stm = $this->databaseConnection->executeQuery(
            $query,
            ['fieldTypes' => $fieldTypes],
            ['fieldTypes' => Connection::PARAM_STR_ARRAY]
        );

        return $stm->fetchAllAssociative();
    }

    /**
     * Return an array of supported field constant names as defined in table `cms_field_type`, field `constname`.
     *
     * @return string[]
     */
    abstract protected function getFieldTypeConstantNamesToHandle();

    /**
     * @param string $fieldTypeConstantName - constant name as defined in table `cms_field_type`, field `constname`
     *
     * @return bool
     */
    private function canHandleFieldWithFieldTypeConstantName($fieldTypeConstantName)
    {
        return in_array($fieldTypeConstantName, $this->getFieldTypeConstantNamesToHandle(), true);
    }

    /**
     * Returns records containing usages as rows from the database.
     *
     * @param string $fieldTypeConstantName - constant name as defined in table `cms_field_type`, field `constname`
     * @param array $fieldRow - an array containing information about the field in the form [['tableId' => '', 'tableName' => '', 'fieldId' => '', 'fieldName' => '', 'fieldTypeConstantName' => '']]
     *
     * @return array - rows from the database
     *
     * @throws UsageFinderException
     */
    abstract protected function getRecordsForField($fieldTypeConstantName, $fieldRow, MediaItemDataModel $mediaItem);

    /**
     * @param string $tableName
     * @param string $id
     *
     * @return \TCMSRecord|null
     */
    private function getTableObject($tableName, $id)
    {
        $autoClassName = \TCMSTableToClass::GetClassName('Tdb', $tableName);
        $tableObject = call_user_func([$autoClassName, 'GetNewInstance']);
        if (false === $tableObject->Load($id)) {
            return null;
        }

        return $tableObject;
    }

    /**
     * @return MediaItemUsageDataModel
     */
    private function createUsageDataModel(
        MediaItemDataModel $mediaItem,
        \TCMSRecord $tableObject,
        \TdbCmsTblConf $table,
        \TdbCmsFieldConf $field
    ) {
        $usage = new MediaItemUsageDataModel($mediaItem->getId(), $table->fieldName, $tableObject->id);
        $usage->setTargetFieldName($field->fieldName);
        $usage->setTargetFieldDescriptiveName($field->GetName());
        $usage->setTargetRecordName($tableObject->GetName());
        $usage->setTargetTableDescriptiveName($table->GetName());

        $urlParams = [
            'pagedef' => 'tableeditor',
            'tableid' => $table->id,
            'id' => $tableObject->id,
        ];
        $usage->setUrl(
            URL_CMS_CONTROLLER.$this->urlUtil->getArrayAsUrl($urlParams, '?', '&')
        );
        $cropFieldName = \TCMSTableToClass::PREFIX_PROPERTY.\TCMSTableToClass::ConvertToClassString(
            $field->fieldName
        ).'ImageCropId';
        if (property_exists($tableObject, $cropFieldName)) {
            $usage->setCropId($tableObject->$cropFieldName);
        }

        return $usage;
    }
}
