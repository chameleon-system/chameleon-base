<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSTableEditorNewsletterSubscriberEndPoint extends TCMSTableEditor
{
    /**
     * {@inheritDoc}
     */
    public function Delete($sId = null)
    {
        if (null !== $sId) {
            // delete user from queue before final delete

            $query = "SELECT * FROM `pkg_newsletter_queue` WHERE `pkg_newsletter_user` = '".MySqlLegacySupport::getInstance()->real_escape_string($sId)."'";
            $oPkgNewsletterQueueList = &TdbPkgNewsletterQueueList::GetList($query);
            while ($oPkgNewsletterQueue = $oPkgNewsletterQueueList->Next()) {
                $iTableID = TTools::GetCMSTableId('pkg_newsletter_queue');

                $oTableEditor = new TCMSTableEditorManager();
                /** @var $oTableEditor TCMSTableEditorManager */
                $oTableEditor->Init($iTableID, $oPkgNewsletterQueue->id);
                $oTableEditor->AllowDeleteByAll(true);
                $oTableEditor->Delete($oPkgNewsletterQueue->id);
            }
        }

        parent::Delete($sId);
    }
}
