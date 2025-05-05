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
 * std varchar text field (max 255 chars).
 * /**/
class TCMSFieldTreeURLName extends TCMSFieldVarchar
{
    public function GetHTML()
    {
        $html = parent::GetHTML();

        return $html;
    }

    protected function GetTreeNodeLink()
    {
        $url = $this->oTableRow->GetLink();

        return $url;
    }

    /**
     * renders the read only view of the field.
     *
     * @return string
     */
    public function GetReadOnly()
    {
        $html = $this->_GetHiddenField();
        $html .= '<div class="form-content-simple">'.TGlobal::OutHTML($this->data).'</div>';

        $url = $this->GetTreeNodeLink();
        if (!empty($url)) {
            $html .= '<div class="pt-1"><a href="'.TGlobal::OutHTML($url).'" target="_blank"><i class="fas fa-external-link-alt pr-2"></i>'.ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.field_tree_url_name.open').'</a></div>';
        }

        return $html;
    }
}
