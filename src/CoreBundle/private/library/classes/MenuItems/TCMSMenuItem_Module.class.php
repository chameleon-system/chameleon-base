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
 * A standard CMS Module.
 *
 * @deprecated since 6.3.0 - only used for deprecated classic main menu
 */
class TCMSMenuItem_Module extends TCMSMenuItem
{
    /**
     * {@inheritdoc}
     */
    public function GetLink()
    {
        $pagedefType = $this->data['module_location'];
        $url = PATH_CMS_CONTROLLER.'?pagedef='.urlencode($this->data['module']);
        if ('' !== $this->data['parameter']) {
            $url .= '&'.$this->data['parameter'];
        }
        if ('' !== $pagedefType) {
            $url .= '&_pagedefType='.$pagedefType;
        }

        if (array_key_exists('icon_font_css_class', $this->data) && '' !== $this->data['icon_font_css_class']) {
            $icon = $this->data['icon_font_css_class'];
        } else {
            $icon = 'fas fa-sign-out-alt'; //standard icon
        }

        return '<a class="nav-link-fa" href="'.$url.'"><i class="'.$icon.'"></i><span>'. TGlobal::OutHTML($this->data['name']).'</span></a>';
    }
}
