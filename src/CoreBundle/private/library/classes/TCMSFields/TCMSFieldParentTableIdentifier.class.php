<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\AutoclassesBundle\TableConfExport\DoctrineNotTransformableInterface;

class TCMSFieldParentTableIdentifier extends TCMSField implements DoctrineNotTransformableInterface
{
    public function GetHTML()
    {
        $html = $this->_GetHTMLValue();

        return $html;
    }

    public function _GetHTMLValue()
    {
        return $this->data;
    }
}
