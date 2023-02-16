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
 * separator.
/**/
class TCMSFieldSeparator extends TCMSField
{

    public function getDoctrineDataModelParts(string $namespace): ?DataModelParts
    {
        return null;
    }
    public function __construct()
    {
        $this->completeRow = true;
    }

    public function GetHTML()
    {
        $title = TGlobal::OutHTML(TGlobal::Translate($this->oDefinition->sqlData['field_default_value']));

        return $title;
    }
}
