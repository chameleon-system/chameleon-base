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

        $iconStyle = '';
        $rightIconStyle = 'display: block; background-image: url('.URL_CMS.'/images/icon_enter.gif); background-repeat: no-repeat; background-position: right center; padding-right: 16px;';
        if (array_key_exists('icon_list', $this->data) && !empty($this->data['icon_list'])) {
            $iconStyle = ' style="background-image: url('.URL_CMS.'/images/icons/'.$this->data['icon_list'].'); background-repeat: no-repeat; background-position: left center; line-height: 17px;"';
            $rightIconStyle .= ' padding-left: 20px;';
        }

        return '<a class="nav-link" href="'.$url.'" title="'.$this->data['name']."\"{$iconStyle}><span style=\"{$rightIconStyle}\">".$this->data['name'].'</span></a>';
    }
}
