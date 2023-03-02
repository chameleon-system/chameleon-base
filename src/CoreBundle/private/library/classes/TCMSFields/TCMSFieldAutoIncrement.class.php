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
    protected function getDoctrineDataModelXml(string $namespace): string
    {
        return $this->getDoctrineRenderer('mapping/autoincrement.xml.twig', [
            'fieldName' => $this->snakeToCamelCase($this->name),
            'column' => $this->name,
            'comment' => $this->oDefinition->sqlData['translation'],
        ])->render();
    }
}
