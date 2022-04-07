<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\NewsletterBundle\PostProcessing\PostProcessorInterface;
use Psr\Log\LoggerInterface;

class TCMSNewsletterCampaign extends TCMSNewsletterCampaignAutoParent
{
    const URL_USER_ID_PARAMETER = 'TPkgNewsletterCampaign';

    /**
     * @param TdbPkgNewsletterUser|false|null $oNewsletterUser
     *
     * @return false|null|string
     */
    protected function CreateEmailFromTemplate($oNewsletterUser = null)
    {
        $sFullURL = $this->GetLinkToHTMLNewsletter($oNewsletterUser);

        if (!empty($sFullURL)) {
            $sBuffer = '';
            if ($pFile = fopen($sFullURL, 'r')) {
                while (!feof($pFile)) {
                    $sBuffer .= fread($pFile, 4096);
                }
            } else {
                echo '<br />ERROR: Could not open '.$sFullURL.'<br />';

                return false;
            }

            $sNewsletter = $sBuffer;
            $bDone = false;
            while (!$bDone && ($iPos = stripos($sNewsletter, '<!--#REMOVE-FROM-NEWSLETTER-START#-->'))) {
                $iEndPos = stripos($sNewsletter, '<!--#REMOVE-FROM-NEWSLETTER-END#-->');
                if (false === $iEndPos) {
                    $bDone = true;
                } else {
                    $iEndPos = $iEndPos + 35;
                    $sNewsletter = substr($sNewsletter, 0, $iPos).substr($sNewsletter, $iEndPos);
                }
            }
            // also remove scripts
            $bDone = false;
            while (!$bDone && ($iPos = stripos($sNewsletter, '<script'))) {
                $iEndPos = stripos($sNewsletter, '</script>');
                if (false === $iEndPos) {
                    $bDone = true;
                } else {
                    $iEndPos = $iEndPos + 9;
                    $sNewsletter = substr($sNewsletter, 0, $iPos).substr($sNewsletter, $iEndPos);
                }
            }

            $sNewsletter = $this->ProcessNewsletterHTMLHook($sNewsletter);

            // change char type to ISO-8859-15
            $sNewsletter = str_replace('text/html; charset=UTF-8', 'text/html; charset=ISO-8859-15', $sNewsletter);
            $sNewsletter = str_replace('encoding="UTF-8"', 'encoding="ISO-8859-15"', $sNewsletter);

            // also replace umlaute
            $aReplaceChars = array('ä' => '&auml;', 'ü' => '&uuml;', 'ö' => '&ouml;', 'ß' => '&szlig;', 'Ä' => '&Auml;', 'Ü' => '&Uuml;', 'Ö' => '&Ouml;');
            $sNewsletter = str_replace(array_keys($aReplaceChars), array_values($aReplaceChars), $sNewsletter);

            // change relative links to absolute
            $oPortal = $this->GetFieldCmsPortal();
            if ($oPortal) {
                $sURL = 'http://'.$this->getPortalDomainService()->getPrimaryDomain($oPortal->id)->getInsecureDomainName();
                $sNewsletter = str_replace('href="/', 'href="'.$sURL.'/', $sNewsletter);
                $sNewsletter = str_replace("href='/", "href='".$sURL.'/', $sNewsletter);
            }

            return $sNewsletter;
        }
    }

    /**
     * Process the rendered HTML-Newsletter
     * This is done before converting the newsletter to ISO-8859-15.
     *
     * @param string $sNewsletter
     *
     * @return string
     */
    protected function ProcessNewsletterHTMLHook($sNewsletter)
    {
        // replace float in img style attribute
        $imageFloatReplacementPattern = '/(?<imageTagStart><img)(?<beforeStyle>.*)(?<styleAttr>style=)(?<styleAttrDelimiterStart>\'|")(?<beforeFloat>.*)(?<float>float:\s?(?<floatValue>left|right)\s?(;)?)(?<styleAttrDelimiterEndWithRest>[^\>]*)(?<imageTagEnd>\/>)/';
        $sNewsletter = preg_replace_callback($imageFloatReplacementPattern, array($this, 'replaceFloatInStyleAttributesCallback'), $sNewsletter);

        return $sNewsletter;
    }

    /**
     * return instance based on the id in the url
     * TdbPkgNewsletterCampaign.
     *
     * @return TdbPkgNewsletterCampaign
     */
    public static function &GetInstanceFromURLId()
    {
        static $oInstance = false;
        if (false === $oInstance) {
            $oGlobal = TGlobal::instance();
            $sId = $oGlobal->GetuserData(TdbPkgNewsletterCampaign::URL_USER_ID_PARAMETER);
            $oInstance = TdbPkgNewsletterCampaign::GetNewInstance();
            if (!$oInstance->Load($sId)) {
                $oInstance = null;
            }
        }

        return $oInstance;
    }

    /**
     * send newsletter for current campaign.
     *
     * @return void
     */
    public function SendNewsletter()
    {
        $iTableID = TTools::GetCMSTableId('pkg_newsletter_campaign');
        $oTableEditor = new TCMSTableEditorManager(); /*@var $oTableEditor TCMSTableEditorManager*/
        $oTableEditor->AllowEditByAll(true);
        $oTableEditor->Init($iTableID, $this->id);
        if ('0000-00-00 00:00:00' == $this->fieldSendStartDate) {
            $oTableEditor->SaveField('send_start_date', date('Y-m-d H:i:s'));
        }
        $oNewsletterUserList = $this->GetNewsletterUser();
        $oNewsletterGroupList = $this->GetFieldPkgNewsletterGroupList();
        $sGeneratedNewsletter = false;
        while ($oNewsletterUser = &$oNewsletterUserList->Next()) /*@var $oNewsletterUser TdbPkgNewsletterUser*/ {
            $oNewsletterGroup = $this->GetNewsletterGroupForNewsletterUserBestFit($oNewsletterUser, $oNewsletterGroupList);
            $bFoundGroup = false;
            $aNewsletterGroups = array();
            $bAddAllUnsubscribeLinks = false;
            while ($oNewsletterGroupTmp = $oNewsletterGroupList->Next()) {
                $oNewsletterGroupUserGroupList = $oNewsletterGroupTmp->GetFieldDataExtranetGroupList();
                if ($oNewsletterGroup->fieldIncludeAllNewsletterUsers ||
                   $oNewsletterGroup->fieldIncludeNewsletterUserNotAssignedToAnyGroup ||
                   $oNewsletterGroup->fieldIncludeAllNewsletterUsersWithNoExtranetAccount ||
                   $oNewsletterGroupUserGroupList->Length() > 0) {
                    $bAddAllUnsubscribeLinks = true;
                }
                if ($oNewsletterUser->isInGroup($oNewsletterGroupTmp->id)) {
                    $aNewsletterGroups[] = $oNewsletterGroupTmp->id;
                    if (!$bFoundGroup) {
                        $oNewsletterGroup = $oNewsletterGroupTmp;
                        $bFoundGroup = true;
                    }
                }
            }
            if (0 == count($aNewsletterGroups)) {
                $aNewsletterGroups[] = $oNewsletterGroup->id;
            }
            if (false === $sGeneratedNewsletter || true === $this->fieldGenerateUserDependingNewsletter) {
                if (true === $this->fieldGenerateUserDependingNewsletter) {
                    $sGeneratedNewsletter = $this->CreateEmailFromTemplate($oNewsletterUser);
                } else {
                    $sGeneratedNewsletter = $this->CreateEmailFromTemplate();
                }
            }
            $bSent = $this->SendMail($oNewsletterGroup, $oNewsletterUser, $sGeneratedNewsletter, $aNewsletterGroups, $bAddAllUnsubscribeLinks);
            if (true === $bSent) {
                // mark entry as completed
                $query = "UPDATE pkg_newsletter_queue
                      SET date_sent = '".date('Y-m-d H:i:s')."'
                    WHERE pkg_newsletter_campaign_id = '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."'
                      AND pkg_newsletter_user = '".MySqlLegacySupport::getInstance()->real_escape_string($oNewsletterUser->id)."'
                  ";
                MySqlLegacySupport::getInstance()->query($query);
            }
        }
        $oTableEditor->SaveField('send_end_date', date('Y-m-d H:i:s'));
        $oTableEditor->SaveField('active', '0');
    }

    /**
     * If user is direct member of a list, return first one. Then evaluate other options and extranet groups.
     *
     * @param TdbPkgNewsletterUser      $oNewsletterUser
     * @param TdbPkgNewsletterGroupList $oNewsletterGroupList
     *
     * @return TdbPkgNewsletterGroup
     */
    protected function GetNewsletterGroupForNewsletterUserBestFit(TdbPkgNewsletterUser $oNewsletterUser, TdbPkgNewsletterGroupList &$oNewsletterGroupList)
    {
        $oNewsletterGroup = null;
        $oExtranetUser = $oNewsletterUser->GetFieldDataExtranetUser();

        $oNewsletterGroupList->GoToEnd();
        while ($oNewsletterGroupTmp = $oNewsletterGroupList->Previous()) {
            $bGroupIsMatch = false;
            if ($oNewsletterUser->isInGroup($oNewsletterGroupTmp->id)) {
                //we have a direct match
                $oNewsletterGroup = $oNewsletterGroupTmp;
                break;
            }
            if ($oNewsletterGroupTmp->fieldIncludeAllNewsletterUsers) {
                //list sends to ALL users
                $oNewsletterGroup = $oNewsletterGroupTmp;
                $bGroupIsMatch = true;
            }
            if (!$bGroupIsMatch && $oNewsletterGroupTmp->fieldIncludeNewsletterUserNotAssignedToAnyGroup) {
                $aGroupList = $oNewsletterUser->GetFieldPkgNewsletterGroupIdList();
                if (0 == count($aGroupList)) {
                    //user is not member of a group
                    $oNewsletterGroup = $oNewsletterGroupTmp;
                    $bGroupIsMatch = true;
                }
            }
            if (!$bGroupIsMatch && !$oExtranetUser && $oNewsletterGroupTmp->fieldIncludeAllNewsletterUsersWithNoExtranetAccount) {
                //user has no extranet account
                $oNewsletterGroup = $oNewsletterGroupTmp;
                $bGroupIsMatch = true;
            }
            if (!$bGroupIsMatch) {
                if ($oExtranetUser) {
                    $aNewsletterUserGroups = $oNewsletterGroupTmp->GetFieldDataExtranetGroupIdList();
                    if (count($aNewsletterUserGroups) > 0) {
                        $aUserExtranetGroups = $oExtranetUser->GetFieldDataExtranetGroupIdList();
                        $aIntersect = array_intersect($aNewsletterUserGroups, $aUserExtranetGroups);
                        if (count($aIntersect) > 0) {
                            //user is member of an extranet group the list sends to
                            $oNewsletterGroup = $oNewsletterGroupTmp;
                        }
                    }
                }
            }
        }
        if (null === $oNewsletterGroup) {
            //first group
            $oNewsletterGroupList->GoToStart();
            $oNewsletterGroup = $oNewsletterGroupList->Current();
        }
        $oNewsletterGroupList->GoToStart();

        return $oNewsletterGroup;
    }

    /**
     * send the personalized newsletter to the user. return true on success, or an error message on error.
     *
     * @param TdbPkgNewsletterGroup $oNewsletterGroup
     * @param TdbPkgNewsletterUser  $oNewsletterUser
     * @param string                $sGeneratedNewsletter
     * @param array                 $aNewsletterGroups
     * @param bool                  $bAddAllUnsubscribeLinks
     *
     * @return bool
     */
    protected function SendMail(&$oNewsletterGroup, &$oNewsletterUser, $sGeneratedNewsletter, $aNewsletterGroups = null, $bAddAllUnsubscribeLinks = false)
    {
        $bSend = false;
        if (!empty($sGeneratedNewsletter)) {
            // now send email using email object
            $oMailObject = $this->getMailer();
            $oMailObject->SetSubject($this->fieldSubject);
            $oMailObject->SetFromData($oNewsletterGroup->fieldFromEmail, $oNewsletterGroup->fieldFromName);
            $oMailObject->ClearReplyTos();
            $oMailObject->AddReplyTo($oNewsletterGroup->fieldReplyEmail, $oNewsletterGroup->fieldFromName);
            $oMailObject->AddAddress($oNewsletterUser->fieldEmail, $oNewsletterUser->fieldFirstname.' '.$oNewsletterUser->fieldLastname);
            $oMailObject->SetObjectTemplate('emails', 'Customer', 'newsletter', 'newslettertext');
            $sPlaintext = $this->fieldContentPlain;

            $postPrecessorCollector = $this->getPostProcessorCollector();
            $userDataFactory = $this->getNewsletterUserDataFactory();
            $newsletterGroupId = null === $oNewsletterGroup ? null : $oNewsletterGroup->id;
            $userData = $userDataFactory->createNewsletterUserData($this, $oNewsletterUser, $newsletterGroupId, $aNewsletterGroups, $bAddAllUnsubscribeLinks);
            $sPlaintext = $postPrecessorCollector->process($sPlaintext, $userData);
            $sGeneratedNewsletter = $postPrecessorCollector->process($sGeneratedNewsletter, $userData);

            $sPlaintext = $this->ReplaceVariablesInTextHook($sPlaintext, $oNewsletterUser);
            $sGeneratedNewsletter = $this->ReplaceVariablesInTextHook($sGeneratedNewsletter, $oNewsletterUser);

            $logger = $this->getLogger();
            if (false === $this->isNewsletterAlreadySent($oNewsletterUser)) {
                if (!$oMailObject->Send(array('sBody' => $sGeneratedNewsletter, 'sTextBody' => $sPlaintext))) {
                    $logger->warning(sprintf('Unable to send Newsletter: %s', $oMailObject->ErrorInfo));
                } else {
                    $bSend = true;
                }
            } else {
                $logger->warning(sprintf('Mail to newsletter queue entry with e-mail %s already sent.', $oNewsletterUser->fieldEmail));
            }
        }

        return $bSend;
    }

    /**
     * checks if newsletter was already sent by other running cron job.
     *
     * @param TdbPkgNewsletterUser $oNewsletterUser
     *
     * @return bool
     */
    protected function isNewsletterAlreadySent($oNewsletterUser)
    {
        $bIsNewsletterAlreadySent = true;
        if (isset($oNewsletterUser->sqlData['pkg_newsletter_queue_id']) &&
           '' != $oNewsletterUser->sqlData['pkg_newsletter_queue_id']) {
            $oQueue = TdbPkgNewsletterQueue::GetNewInstance();
            $oQueue->SetEnableObjectCaching(false);
            if ($oQueue->Load($oNewsletterUser->sqlData['pkg_newsletter_queue_id'])) {
                if ('0000-00-00 00:00:00' == $oQueue->fieldDateSent) {
                    $bIsNewsletterAlreadySent = false;
                }
            }
        }

        return $bIsNewsletterAlreadySent;
    }

    /**
     * This is done on both Plain Text and HTML-Version.
     *
     * @param string               $sText
     * @param TdbPkgNewsletterUser $oNewsletterUser
     *
     * @return mixed
     */
    protected function ReplaceVariablesInTextHook($sText, $oNewsletterUser)
    {
        return $sText;
    }

    /**
     * get all newsletter users for campaign
     * - returns only users to which no mail was send yet
     * - and users which have an "optin"
     * 	 (prevents sending newsletters to users which have optout after queue was created)
     * - and users that are not blacklisted (robinson list).
     *
     * @return TdbPkgNewsletterUserList
     */
    protected function GetNewsletterUser()
    {
        $query = "SELECT `pkg_newsletter_user`.*, `pkg_newsletter_queue`.`id` as pkg_newsletter_queue_id FROM `pkg_newsletter_queue`
              INNER JOIN `pkg_newsletter_user` ON `pkg_newsletter_user`.`id` = `pkg_newsletter_queue`.`pkg_newsletter_user`
			   LEFT JOIN `pkg_newsletter_robinson` ON `pkg_newsletter_user`.`email` = `pkg_newsletter_robinson`.`email`
				   WHERE `pkg_newsletter_queue`.`pkg_newsletter_campaign_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."'
					 AND `pkg_newsletter_queue`.`date_sent` = '0000-00-00 00:00:00'
					 AND `pkg_newsletter_user`.`optin` =  '1'
					 AND `pkg_newsletter_robinson`.`email` IS NULL";
        $oNewsletterUserList = TdbPkgNewsletterUserList::GetList($query);

        return $oNewsletterUserList;
    }

    /**
     * @param TdbPkgNewsletterUser|null $oNewsletterUser
     *
     * @return string
     */
    public function GetLinkToHTMLNewsletter($oNewsletterUser = null)
    {
        $sFullURL = '';
        $sLink = $this->GetFieldCmsTreeNodeIdPageURL(false, true);
        if (!empty($sLink)) {
            $sUserID = '';
            if (null !== $oNewsletterUser) {
                $sUserID = $oNewsletterUser->id;
            }

            if ('http://' == strtolower(substr($sLink, 0, 7)) || 'https://' == strtolower(substr($sLink, 0, 8))) {
                $sTransportAndDomain = parse_url($sLink, PHP_URL_SCHEME).'://'.parse_url($sLink, PHP_URL_HOST);
                $sLink = str_replace($sTransportAndDomain, '', $sLink);
            }

            $sURL = 'http://'.$this->getPortalDomainService()->getPrimaryDomain($this->GetFieldCmsPortal()->id)->getInsecureDomainName();
            $sFullURL = $sURL.$sLink;
            if (!is_null($sUserID)) {
                $sFullURL .= '?'.TdbPkgNewsletterUser::URL_USER_ID_PARAMETER.'='.$sUserID.'&'.TdbPkgNewsletterCampaign::URL_USER_ID_PARAMETER.'='.$this->id;
            }
        }

        return $sFullURL;
    }

    /**
     * @param array<string, string> $matches
     * @return string
     */
    public function replaceFloatInStyleAttributesCallback($matches)
    {
        $return = $matches['imageTagStart'];
        $return .= ' align="'.$matches['floatValue'].'" ';
        $return .= $matches['beforeStyle'];
        $return .= $matches['styleAttr'];
        $return .= $matches['styleAttrDelimiterStart'];
        $return .= $matches['styleAttrDelimiterEndWithRest'];
        $return .= $matches['imageTagEnd'];

        return $return;
    }

    /**
     * @return TCMSMail
     */
    private function getMailer()
    {
        return ServiceLocator::get('chameleon_system_core.mailer');
    }

    /**
     * @return PostProcessorInterface
     */
    protected function getPostProcessorCollector()
    {
        return ServiceLocator::get('chameleon_system_newsletter.post_processor_collector');
    }

    /**
     * @return \ChameleonSystem\NewsletterBundle\PostProcessing\Bridge\NewsletterUserDataFactoryInterface
     */
    private function getNewsletterUserDataFactory()
    {
        return ServiceLocator::get('chameleon_system_newsletter.user_data_factory');
    }

    /**
     * @return PortalDomainServiceInterface
     */
    private function getPortalDomainService()
    {
        return ServiceLocator::get('chameleon_system_core.portal_domain_service');
    }

    private function getLogger(): LoggerInterface
    {
        return ServiceLocator::get('logger');
    }
}
