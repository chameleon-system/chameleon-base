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
 * a Table item.
/**/
class TCMSMenuItem_Table extends TCMSMenuItem
{
    /**
     * {@inheritdoc}
     */
    public function GetLink()
    {
        $iconStyle = '';
        $rightIconStyle = ' padding-left: 20px;';
        if (array_key_exists('icon_list', $this->data) && !empty($this->data['icon_list'])) {
            $iconStyle = ' style="background-image: url('.URL_CMS.'/images/icons/'.$this->data['icon_list'].'); background-repeat: no-repeat; background-position: left center; line-height: 17px;"';
        }

        $url = PATH_CMS_CONTROLLER.'?pagedef=tablemanager&amp;id='.urlencode($this->data['id']);
        $titleAttribute = '';
        if (array_key_exists('notes', $this->data) && !empty($this->data['notes'])) {
            $titleAttribute = ' title="'.TGlobal::OutHTML($this->data['notes']).'"';
        }

        if (array_key_exists('icon_font_awesome', $this->data) && !empty($this->data['icon_font_awesome'])) {
            $icon = $this->data['icon_font_awesome'];
        } else {
            $icon = 'fas fa-sign-out-alt'; //standard icon
        }

        return '<a class="nav-link-fa" href="'.$url.'"'. $titleAttribute . '><i class="'.$icon.'"></i><span>'. TGlobal::OutHTML($this->data['translation']).'</span></a>';
    }
}
