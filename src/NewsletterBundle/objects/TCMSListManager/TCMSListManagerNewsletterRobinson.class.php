<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSListManagerNewsletterRobinson extends TCMSListManagerFullGroupTable
{
    /**
     * add table-specific buttons to the editor (add them directly to $this->oMenuItems).
     *
     * @return void
     */
    protected function GetCustomMenuItems()
    {
        parent::GetCustomMenuItems();
        $oMenuItem = $this->GetImportMenuItem();
        $this->oMenuItems->AddItem($oMenuItem);
    }

    /**
     * @return TCMSTableEditorMenuItem
     */
    protected function GetImportMenuItem()
    {
        $oMenuItem = new TCMSTableEditorMenuItem();
        $oMenuItem->sItemKey = 'ImportSubscriber';
        $oMenuItem->sDisplayName = TGlobal::Translate('chameleon_system_newsletter.action.import_blacklist');
        $oMenuItem->sIcon = 'fas fa-user-minus';
        $oMenuItem->sOnClick = "CreateModalIFrameDialogCloseButton('".PATH_CMS_CONTROLLER."?pagedef=NewsletterRobinsonImport&_pagedefType=Core',0,0,'".TGlobal::OutJS(TGlobal::Translate('chameleon_system_newsletter.action.import_blacklist'))."');";

        return $oMenuItem;
    }
}
