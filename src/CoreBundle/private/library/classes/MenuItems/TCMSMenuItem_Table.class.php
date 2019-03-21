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
 * A table item.
 *
 * @deprecated since 6.3.0 - only used for deprecated classic main menu
 */
class TCMSMenuItem_Table extends TCMSMenuItem
{
    /**
     * {@inheritdoc}
     */
    public function GetLink()
    {
        $url = PATH_CMS_CONTROLLER.'?pagedef=tablemanager&amp;id='.urlencode($this->data['id']);
        $titleAttribute = '';
        if (array_key_exists('notes', $this->data) && '' !== $this->data['notes']) {
            $titleAttribute = ' title="'.TGlobal::OutHTML($this->data['notes']).'"';
        }

        if (array_key_exists('icon_font_css_class', $this->data) && '' !== $this->data['icon_font_css_class']) {
            $icon = $this->data['icon_font_css_class'];
        } else {
            $icon = 'fas fa-sign-out-alt'; //standard icon
        }

        return '<a class="nav-link-fa" href="'.$url.'"'.$titleAttribute.'><i class="'.$icon.'"></i><span>'.TGlobal::OutHTML($this->data['translation']).'</span></a>';
    }
}
