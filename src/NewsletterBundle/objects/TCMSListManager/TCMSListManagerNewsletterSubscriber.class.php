<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSListManagerNewsletterSubscriber extends TCMSListManagerFullGroupTable
{
    /**
     * add table-specific buttons to the editor (add them directly to $this->oMenuItems).
     */
    protected function GetCustomMenuItems()
    {
        parent::GetCustomMenuItems();
        $oMenuItem = $this->GetNewsletterUserImportMenuItem();
        $this->oMenuItems->AddItem($oMenuItem);
    }

    protected function GetNewsletterUserImportMenuItem()
    {
        $oMenuItem = new TCMSTableEditorMenuItem();
        $oMenuItem->sItemKey = 'ImportSubscriber';
        $oMenuItem->sDisplayName = TGlobal::Translate('chameleon_system_newsletter.action.import_subscribers');
        $oMenuItem->sIcon = TGlobal::GetStaticURLToWebLib('/images/icons/folder_user.png');
        $oMenuItem->sOnClick = "CreateModalIFrameDialogCloseButton('".PATH_CMS_CONTROLLER."?pagedef=NewsletterSubscriberImport&_pagedefType=Core',0,0,'".TGlobal::Translate('chameleon_system_newsletter.action.import_subscribers')."');";

        return $oMenuItem;
    }
}
