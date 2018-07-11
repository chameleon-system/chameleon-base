<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSFieldExternalVideoCode extends TCMSFieldText
{
    /**
     * renders the html of the field (overwrite this to write your own field type).
     *
     * @return string
     */
    public function GetHTML()
    {
        $html = parent::GetHTML();
        $html .= $this->data;

        return $html;
    }

    /**
     * renders the read only view of the field.
     *
     * @return string
     */
    public function GetReadOnly()
    {
        $html = $this->_GetHiddenField()."\n".$this->data;
        $html .= '<textarea style="margin-top: 10px; width: 500px; height: 200px;">'.TGlobal::OutHTML($this->data)."</textarea>\n";

        return $html;
    }

    /**
     * returns the modifier (none, hidden, readonly) of the field. if the field
     * is restricted, and the modifier is none, then we return readonly instead.
     *
     * @return string
     */
    public function GetDisplayType()
    {
        return 'hidden';
    }
}
