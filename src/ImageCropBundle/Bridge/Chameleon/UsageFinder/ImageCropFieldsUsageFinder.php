<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ImageCropBundle\Bridge\Chameleon\UsageFinder;

use ChameleonSystem\MediaManager\DataModel\MediaItemDataModel;
use ChameleonSystem\MediaManagerBundle\Bridge\Chameleon\UsageFinder\AbstractImageFieldsUsageFinder;

class ImageCropFieldsUsageFinder extends AbstractImageFieldsUsageFinder
{
    /**
     * {@inheritdoc}
     */
    protected function getFieldTypeConstantNamesToHandle()
    {
        return ['CMSFIELD_EXTENDEDTABLELIST_MEDIA_CROP'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getRecordsForField($fieldTypeConstantName, $fieldRow, MediaItemDataModel $mediaItem)
    {
        $records = [];
        switch ($fieldTypeConstantName) {
            case 'CMSFIELD_EXTENDEDTABLELIST_MEDIA_CROP':
                $query = sprintf(
                    'SELECT * FROM %s
                            WHERE %s = :mediaItemId',
                    $this->databaseConnection->quoteIdentifier($fieldRow['tableName']),
                    $this->databaseConnection->quoteIdentifier($fieldRow['fieldName'])
                );
                $records = $this->databaseConnection->fetchAllAssociative(
                    $query,
                    [
                        'mediaItemId' => $mediaItem->getId(),
                    ]
                );
                break;
            default:
                break;
        }

        return $records;
    }
}
