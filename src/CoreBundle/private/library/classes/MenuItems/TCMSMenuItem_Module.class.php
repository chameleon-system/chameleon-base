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
 * a standard CMS Module.
/**/
class TCMSMenuItem_Module extends TCMSMenuItem
{
    /**
     * {@inheritdoc}
     */
    public function GetLink()
    {
        $pagedefType = $this->data['module_location'];
        $url = PATH_CMS_CONTROLLER.'?pagedef='.urlencode($this->data['module']);
        if (!empty($this->data['parameter'])) {
            $url .= '&'.$this->data['parameter'];
        }
        if (!empty($pagedefType)) {
            $url .= '&_pagedefType='.$pagedefType;
        }
        if ('1' == $this->data['show_as_popup']) {
            $url = "javascript:CreateModalIFrameDialogCloseButton('".$url."',".TGlobal::OutHTML($this->data['width']).','.TGlobal::OutHTML($this->data['height']).');';
        }

        if (array_key_exists('icon_font_awesome', $this->data) && !empty($this->data['icon_font_awesome'])) {
            $icon = $this->data['icon_font_awesome'];
        } else {
            $icon = 'fas fa-sign-out-alt'; //standard icon
        }

        return '<a class="nav-link-fa" href="'.$url.'"><i class="'.$icon.'"></i><span>'. TGlobal::OutHTML($this->data['name']).'</span></a>';
    }
}
