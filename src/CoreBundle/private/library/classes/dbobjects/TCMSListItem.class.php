<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\ActivePageServiceInterface;

/**
 * module_list item.
 *
 * /**/
class TCMSListItem extends TCMSRecord
{
    /**
     * returns.
     *
     * @return string
     */
    public function GetDetailURL()
    {
        $sDetailURL = $this->getActivePageService()->getLinkToActivePageRelative([
            'article'.$this->sqlData['cms_tpl_module_instance_id'] => $this->id,
        ]);

        return $sDetailURL;
    }

    /**
     * @return ActivePageServiceInterface
     */
    private function getActivePageService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.active_page_service');
    }
}
