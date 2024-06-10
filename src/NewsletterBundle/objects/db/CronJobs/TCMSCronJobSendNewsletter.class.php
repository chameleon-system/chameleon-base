<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSCronJobSendNewsletter extends TdbCmsCronjobs
{
    /**
     * @return void
     */
    protected function _ExecuteCron()
    {
        $languageService = self::getLanguageService();

        $now = date('Y-m-d H:i:s');
        $newsletterList = TdbPkgNewsletterCampaignList::GetList("SELECT * FROM `pkg_newsletter_campaign` WHERE `queue_date` <= '".MySqlLegacySupport::getInstance()->real_escape_string($now)."' AND `active` = '1'");
        while ($newsletter = $newsletterList->Next()) {
            $portal = $newsletter->GetFieldCmsPortal();
            if (null !== $portal) {
                $languageService->setActiveLanguage($portal->fieldCmsLanguageId);
                $newsletter->SetLanguage($portal->fieldCmsLanguageId);
                $newsletter->PostLoadHook(); // reload with the portal default language
            }
            $newsletter->SendNewsletter();
        }
    }
}
