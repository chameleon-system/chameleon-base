<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSFieldNewsletterCampaignStatistics extends TCMSFieldText
{
    public function GetHTML()
    {
        $html = $this->GetReadOnly();

        return $html;
    }

    /**
     * renders the read only view of the field.
     *
     * @return string
     */
    public function GetReadOnly()
    {
        $sShowData = $this->data;
        if ('' == $sShowData) {
            $sShowData = TGlobal::Translate('chameleon_system_newsletter.field_campaign_stats.no_queue');
            $html = '<div class="alert alert-info"><i class="fas fa-minus-circle"></i> '.TGlobal::OutHTML($sShowData).'</div>';
        } else {
            $sShowData = TGlobal::Translate('chameleon_system_newsletter.field_campaign_stats.subscriber_count').': '.$sShowData;
            $iAlreadySentCont = $this->getAlreadySentCount();
            $iToSendCount = $this->data - $iAlreadySentCont;
            if (0 == $iToSendCount) {
                $html = '<i class="far fa-check-square"></i> '.TGlobal::OutHTML(TGlobal::Translate('chameleon_system_newsletter.field_campaign_stats.queue_processed')).'<br />';
            } else {
                if (0 == $iAlreadySentCont) {
                    $html = '<i class="far fa-clock"></i> '.TGlobal::OutHTML(TGlobal::Translate('chameleon_system_newsletter.field_campaign_stats.queue_not_started')).'<br />';
                } else {
                    $html = '<i class="fas fa-clock"></i> '.TGlobal::OutHTML(TGlobal::Translate('chameleon_system_newsletter.field_campaign_stats.queue_processing')).'<br />';
                }
            }
            $html .= '<i class="fas fa-link"></i> '.TGlobal::OutHTML($sShowData).'<br>';
            $html .= '<i class="fas fa-plus-circle"></i> '.TGlobal::OutHTML(TGlobal::Translate('chameleon_system_newsletter.field_campaign_stats.already_sent').': '.$this->getAlreadySentCount()).'<br>';
            $html .= '<i class="fas fa-play-circle"></i> '.TGlobal::OutHTML(TGlobal::Translate('chameleon_system_newsletter.field_campaign_stats.still_to_process', array('%count%' => $iToSendCount))).'<br />';
        }

        return $html;
    }

    /**
     * @return int
     */
    protected function getAlreadySentCount()
    {
        $sQuery = "SELECT COUNT(*) AS count
                     FROM  `pkg_newsletter_queue`
                     WHERE `pkg_newsletter_campaign_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->oTableRow->id)."'
                       AND `date_sent` != '0000-00-00 00:00:00'";
        $res = MySqlLegacySupport::getInstance()->query($sQuery);
        $aRow = MySqlLegacySupport::getInstance()->fetch_assoc($res);

        return $aRow['count'];
    }

    /**
     * checks if field is mandatory and if field content is valid
     * overwrite this method to add your field based validation
     * you need to add a message to TCMSMessageManager for handling error messages
     * <code>
     * <?php
     *   $oMessageManager = TCMSMessageManager::GetInstance();
     *   $sConsumerName = TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER;
     *   $oMessageManager->AddMessage($sConsumerName,'TABLEEDITOR_FIELD_IS_MANDATORY');
     * ?>
     * </code>.
     *
     * @return bool - returns false if field is mandatory and field content is empty or data is not valid
     */
    public function DataIsValid()
    {
        return true;
    }

    /**
     * returns true if field data is not empty
     * overwrite this method for mlt and property fields.
     *
     * @return bool
     */
    public function HasContent()
    {
        return true;
    }
}
