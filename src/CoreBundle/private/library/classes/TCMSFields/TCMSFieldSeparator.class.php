<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * separator.
 * /**/
class TCMSFieldSeparator extends TCMSField
{
    public function __construct()
    {
        $this->completeRow = true;
    }

    public function GetHTML()
    {
        return TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans($this->oDefinition->sqlData['field_default_value']));
    }
}
