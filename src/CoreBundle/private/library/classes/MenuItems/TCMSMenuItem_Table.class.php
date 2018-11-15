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

        $titleAttribute = '';
        if (array_key_exists('notes', $this->data) && !empty($this->data['notes'])) {
            $titleAttribute = ' title="'.TGlobal::OutHTML($this->data['notes']).'"';
        }

        return '<a class="nav-link" href="'.PATH_CMS_CONTROLLER.'?pagedef=tablemanager&amp;id='.urlencode($this->data['id'])."\"{$iconStyle}{$titleAttribute}><span style=\"{$rightIconStyle}\">".TGlobal::OutHTML($this->data['translation']).'</span></a>';
    }
}
