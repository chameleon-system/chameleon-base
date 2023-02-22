<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\AutoclassesBundle\TableConfExport\DataModelParts;

/**
 * auto incrementing field.
/**/
class TCMSFieldAutoIncrement extends TCMSFieldNumber
{
    // todo - not used in the default project - need to map to a generated value. not sure if this is even possible - i think there can be only one autoincrement field per table

    public function getDoctrineDataModelParts(string $namespace): DataModelParts
    {
        throw new Exception('TCMSFieldAutoIncrement not implemented yet');
    }

    public function getDoctrineDataModelXml(string $namespace): string
    {
        throw new Exception('TCMSFieldAutoIncrement not implemented yet');
    }
}
