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
 * date field.
/**/
class TCMSFieldSmallIconList extends TCMSFieldIconList
{
    protected function _GetModulePagedef()
    {
        return 'CMSsmallIconlist';
    }

    protected function _GetIconPath()
    {
        return '/images/icons/';
    }
}
