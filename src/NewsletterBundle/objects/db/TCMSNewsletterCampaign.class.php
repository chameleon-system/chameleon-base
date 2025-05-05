<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Controller\ChameleonFrontendController;
use ChameleonSystem\CoreBundle\RequestType\RequestTypeInterface;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\NewsletterBundle\PostProcessing\PostProcessorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class TCMSNewsletterCampaign extends TCMSNewsletterCampaignAutoParent
{
    public const URL_USER_ID_PARAMETER = 'TPkgNewsletterCampaign';

    /**
     * @param TdbPkgNewsletterUser|false|null $newsletterUser
     *
     * @return false|string|null
     */
    protected function CreateEmailFromTemplate($newsletterUser = null)
    {
        $fullURL = $this->GetLinkToHTMLNewsletter($newsletterUser);

        if (!empty($fullURL)) {
            $buffer = '';
            if ($filePointer = fopen($fullURL, 'r')) {
                while (!feof($filePointer)) {
                    $buffer .= fread($filePointer, 4096);
                }
            } else {
                echo '<br />ERROR: Could not open '.$fullURL.'<br />';

                return false;
            }

            $newsletter = $buffer;
            $done = false;
            while (!$done && ($pos = stripos($newsletter, '<!--#REMOVE-FROM-NEWSLETTER-START#-->'))) {
                $endPos = stripos($newsletter, '<!--#REMOVE-FROM-NEWSLETTER-END#-->');
                if (false === $endPos) {
                    $done = true;
                } else {
                    $endPos = $endPos + 35;
                    $newsletter = substr($newsletter, 0, $pos).substr($newsletter, $endPos);
                }
            }
            // also remove scripts
            $done = false;
            while (!$done && ($pos = stripos($newsletter, '<script'))) {
                $endPos = stripos($newsletter, '</script>');
                if (false === $endPos) {
                    $done = true;
                } else {
                    $endPos = $endPos + 9;
                    $newsletter = substr($newsletter, 0, $pos).substr($newsletter, $endPos);
                }
            }

            $newsletter = $this->ProcessNewsletterHTMLHook($newsletter);

            // change char type to ISO-8859-15
            $newsletter = str_replace('text/html; charset=UTF-8', 'text/html; charset=ISO-8859-15', $newsletter);
            $newsletter = str_replace('encoding="UTF-8"', 'encoding="ISO-8859-15"', $newsletter);

            // also replace umlaute
            $replaceableChars = ['ä' => '&auml;', 'ü' => '&uuml;', 'ö' => '&ouml;', 'ß' => '&szlig;', 'Ä' => '&Auml;', 'Ü' => '&Uuml;', 'Ö' => '&Ouml;'];
            $newsletter = str_replace(array_keys($replaceableChars), array_values($replaceableChars), $newsletter);

            // change relative links to absolute
            $portal = $this->GetFieldCmsPortal();
            if (null !== $portal) {
                $sURL = 'http://'.$this->getPortalDomainService()->getPrimaryDomain($portal->id)->getInsecureDomainName();
                $newsletter = str_replace('href="/', 'href="'.$sURL.'/', $newsletter);
                $newsletter = str_replace("href='/", "href='".$sURL.'/', $newsletter);
            }

            return $newsletter;
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

        return preg_replace_callback($imageFloatReplacementPattern, [$this, 'replaceFloatInStyleAttributesCallback'], $sNewsletter);
    }

    /**
     * return instance based on the id in the url
     * TdbPkgNewsletterCampaign.
     *
     * @return TdbPkgNewsletterCampaign|null
     */
    public static function GetInstanceFromURLId()
    {
        static $instance = false;
        if (false === $instance) {
            $global = TGlobal::instance();
            $id = $global->GetuserData(TdbPkgNewsletterCampaign::URL_USER_ID_PARAMETER);
            $instance = TdbPkgNewsletterCampaign::GetNewInstance();
            if (!$instance->Load($id)) {
                $instance = null;
            }
        }

        return $instance;
    }

    /**
     * send newsletter for current campaign.
     *
     * @return void
     */
    public function SendNewsletter()
    {
        $tableId = TTools::GetCMSTableId('pkg_newsletter_campaign');
        $tableEditor = new TCMSTableEditorManager(); /* @var $oTableEditor TCMSTableEditorManager */
        $tableEditor->AllowEditByAll(true);
        $tableEditor->Init($tableId, $this->id);
        if ('0000-00-00 00:00:00' == $this->fieldSendStartDate) {
            $tableEditor->SaveField('send_start_date', date('Y-m-d H:i:s'));
        }
        $newsletterUserList = $this->GetNewsletterUser();
        $newsletterGroupList = $this->GetFieldPkgNewsletterGroupList();
        $generatedNewsletter = false;
        while ($newsletterUser = $newsletterUserList->Next()) {
            $newsletterGroup = $this->GetNewsletterGroupForNewsletterUserBestFit($newsletterUser, $newsletterGroupList);
            $foundGroup = false;
            $newsletterGroups = [];
            $addAllUnsubscribeLinks = false;
            while ($newsletterGroupTmp = $newsletterGroupList->Next()) {
                $newsletterGroupUserGroupList = $newsletterGroupTmp->GetFieldDataExtranetGroupList();
                if ($newsletterGroup->fieldIncludeAllNewsletterUsers
                    || $newsletterGroup->fieldIncludeNewsletterUserNotAssignedToAnyGroup
                    || $newsletterGroup->fieldIncludeAllNewsletterUsersWithNoExtranetAccount
                    || $newsletterGroupUserGroupList->Length() > 0) {
                    $addAllUnsubscribeLinks = true;
                }
                if ($newsletterUser->isInGroup($newsletterGroupTmp->id)) {
                    $newsletterGroups[] = $newsletterGroupTmp->id;
                    if (!$foundGroup) {
                        $newsletterGroup = $newsletterGroupTmp;
                        $foundGroup = true;
                    }
                }
            }
            if (0 == count($newsletterGroups)) {
                $newsletterGroups[] = $newsletterGroup->id;
            }
            if (false === $generatedNewsletter || true === $this->fieldGenerateUserDependingNewsletter) {
                $generatedNewsletter = $this->generateNewsletterViaFrontend($newsletterUser) ?? false;
            }

            $sent = $this->SendMail($newsletterGroup, $newsletterUser, $generatedNewsletter, $newsletterGroups, $addAllUnsubscribeLinks);
            if (true === $sent) {
                // mark entry as completed
                $query = "UPDATE pkg_newsletter_queue
                      SET date_sent = '".date('Y-m-d H:i:s')."'
                    WHERE pkg_newsletter_campaign_id = '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."'
                      AND pkg_newsletter_user = '".MySqlLegacySupport::getInstance()->real_escape_string($newsletterUser->id)."'
                  ";
                MySqlLegacySupport::getInstance()->query($query);
            }
        }
        $tableEditor->SaveField('send_end_date', date('Y-m-d H:i:s'));
        $tableEditor->SaveField('active', '0');
    }

    /**
     * If user is direct member of a list, return first one. Then evaluate other options and extranet groups.
     *
     * @return TdbPkgNewsletterGroup|false
     */
    protected function GetNewsletterGroupForNewsletterUserBestFit(TdbPkgNewsletterUser $newsletterUser, TdbPkgNewsletterGroupList $newsletterGroupList)
    {
        $newsletterGroup = null;
        $extranetUser = $newsletterUser->GetFieldDataExtranetUser();

        $newsletterGroupList->GoToEnd();
        while ($newsletterGroupTmp = $newsletterGroupList->Previous()) {
            $groupIsMatch = false;
            if ($newsletterUser->isInGroup($newsletterGroupTmp->id)) {
                // we have a direct match
                $newsletterGroup = $newsletterGroupTmp;
                break;
            }
            if ($newsletterGroupTmp->fieldIncludeAllNewsletterUsers) {
                // list sends to ALL users
                $newsletterGroup = $newsletterGroupTmp;
                $groupIsMatch = true;
            }
            if (!$groupIsMatch && $newsletterGroupTmp->fieldIncludeNewsletterUserNotAssignedToAnyGroup) {
                $aGroupList = $newsletterUser->GetFieldPkgNewsletterGroupIdList();
                if (0 == count($aGroupList)) {
                    // user is not member of a group
                    $newsletterGroup = $newsletterGroupTmp;
                    $groupIsMatch = true;
                }
            }
            if (!$groupIsMatch && !$extranetUser && $newsletterGroupTmp->fieldIncludeAllNewsletterUsersWithNoExtranetAccount) {
                // user has no extranet account
                $newsletterGroup = $newsletterGroupTmp;
                $groupIsMatch = true;
            }
            if (!$groupIsMatch) {
                if ($extranetUser) {
                    $newsletterUserGroups = $newsletterGroupTmp->GetFieldDataExtranetGroupIdList();
                    if (count($newsletterUserGroups) > 0) {
                        $userExtranetGroups = $extranetUser->GetFieldDataExtranetGroupIdList();
                        $intersect = array_intersect($newsletterUserGroups, $userExtranetGroups);
                        if (count($intersect) > 0) {
                            // user is member of an extranet group the list sends to
                            $newsletterGroup = $newsletterGroupTmp;
                        }
                    }
                }
            }
        }
        if (null === $newsletterGroup) {
            // first group
            $newsletterGroupList->GoToStart();
            $newsletterGroup = $newsletterGroupList->Current();
        }
        $newsletterGroupList->GoToStart();

        return $newsletterGroup;
    }

    /**
     * send the personalized newsletter to the user. return true on success, or an error message on error.
     *
     * @param TdbPkgNewsletterGroup $oNewsletterGroup
     * @param TdbPkgNewsletterUser $newsletterUser
     * @param string $generatedNewsletter
     * @param array $aNewsletterGroups
     * @param bool $bAddAllUnsubscribeLinks
     *
     * @return bool
     */
    protected function SendMail($oNewsletterGroup, $newsletterUser, $generatedNewsletter, $aNewsletterGroups = null, $bAddAllUnsubscribeLinks = false)
    {
        $send = false;
        if (!empty($generatedNewsletter)) {
            // now send email using email object
            $mailObject = $this->getMailer();
            $mailObject->SetSubject($this->fieldSubject);
            $mailObject->SetFromData($oNewsletterGroup->fieldFromEmail, $oNewsletterGroup->fieldFromName);
            $mailObject->ClearReplyTos();
            $mailObject->AddReplyTo($oNewsletterGroup->fieldReplyEmail, $oNewsletterGroup->fieldFromName);
            $mailObject->AddAddress($newsletterUser->fieldEmail, $newsletterUser->fieldFirstname.' '.$newsletterUser->fieldLastname);
            $mailObject->SetObjectTemplate('emails', 'Customer', 'newsletter', 'newslettertext');
            $plaintext = $this->fieldContentPlain;

            $postPrecessorCollector = $this->getPostProcessorCollector();
            $userDataFactory = $this->getNewsletterUserDataFactory();
            $newsletterGroupId = null === $oNewsletterGroup ? null : $oNewsletterGroup->id;
            $userData = $userDataFactory->createNewsletterUserData($this, $newsletterUser, $newsletterGroupId, $aNewsletterGroups, $bAddAllUnsubscribeLinks);
            $plaintext = $postPrecessorCollector->process($plaintext, $userData);
            $generatedNewsletter = $postPrecessorCollector->process($generatedNewsletter, $userData);

            $plaintext = $this->ReplaceVariablesInTextHook($plaintext, $newsletterUser);
            $generatedNewsletter = $this->ReplaceVariablesInTextHook($generatedNewsletter, $newsletterUser);

            $logger = $this->getLogger();
            if (false === $this->isNewsletterAlreadySent($newsletterUser)) {
                if (!$mailObject->Send(['sBody' => $generatedNewsletter, 'sTextBody' => $plaintext])) {
                    $logger->warning(sprintf('Unable to send Newsletter: %s', $mailObject->ErrorInfo));
                } else {
                    $send = true;
                }
            } else {
                $logger->warning(sprintf('Mail to newsletter queue entry with e-mail %s already sent.', $newsletterUser->fieldEmail));
            }
        }

        return $send;
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
        $isNewsletterAlreadySent = true;
        if (isset($oNewsletterUser->sqlData['pkg_newsletter_queue_id'])
           && '' != $oNewsletterUser->sqlData['pkg_newsletter_queue_id']) {
            $newsletterQueue = TdbPkgNewsletterQueue::GetNewInstance();
            $newsletterQueue->SetEnableObjectCaching(false);
            if ($newsletterQueue->Load($oNewsletterUser->sqlData['pkg_newsletter_queue_id'])) {
                if ('0000-00-00 00:00:00' == $newsletterQueue->fieldDateSent) {
                    $isNewsletterAlreadySent = false;
                }
            }
        }

        return $isNewsletterAlreadySent;
    }

    /**
     * This is done on both Plain Text and HTML-Version.
     *
     * @param string $sText
     * @param TdbPkgNewsletterUser $oNewsletterUser
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

        return TdbPkgNewsletterUserList::GetList($query);
    }

    /**
     * @param TdbPkgNewsletterUser|null $oNewsletterUser
     *
     * @return string
     */
    public function GetLinkToHTMLNewsletter($oNewsletterUser = null)
    {
        $fullURL = '';
        $link = $this->GetFieldCmsTreeNodeIdPageURL(false, true);
        if (!empty($link)) {
            $userId = '';
            if (null !== $oNewsletterUser) {
                $userId = $oNewsletterUser->id;
            }

            if ('http://' == strtolower(substr($link, 0, 7)) || 'https://' == strtolower(substr($link, 0, 8))) {
                $transportAndDomain = parse_url($link, PHP_URL_SCHEME).'://'.parse_url($link, PHP_URL_HOST);
                $link = str_replace($transportAndDomain, '', $link);
            }

            $url = 'http://'.$this->getPortalDomainService()->getPrimaryDomain($this->GetFieldCmsPortal()->id)->getInsecureDomainName();
            $fullURL = $url.$link;
            if (!is_null($userId)) {
                $fullURL .= '?'.TdbPkgNewsletterUser::URL_USER_ID_PARAMETER.'='.$userId.'&'.TdbPkgNewsletterCampaign::URL_USER_ID_PARAMETER.'='.$this->id;
            }
        }

        return $fullURL;
    }

    /**
     * @param array<string, string> $matches
     *
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

    protected function generateNewsletterViaFrontend(TdbPkgNewsletterUser $oNewsletterUser): ?string
    {
        $page = self::getPageService()->getByTreeId($this->fieldCmsTreeNodeId);
        if (null === $page) {
            return null;
        }

        $portal = TdbCmsPortal::GetNewInstance();
        if (false === $portal->Load($this->fieldCmsPortalId)) {
            return null;
        }
        $this->getPortalDomainService()->setActivePortal($portal);
        $domain = $this->getPortalDomainService()->getPrimaryDomain();
        $hostName = $domain->fieldName;

        $queryParams = true === $this->fieldGenerateUserDependingNewsletter
            ? [TdbPkgNewsletterUser::URL_USER_ID_PARAMETER => $oNewsletterUser->id, TdbPkgNewsletterCampaign::URL_USER_ID_PARAMETER => $this->id]
            : [];

        $request = Request::createFromGlobals();
        $request->query = new ParameterBag($queryParams);
        $request->request = new ParameterBag();
        $request->attributes = new ParameterBag(['pagedef' => $page->id]);
        $request->server->set('HTTP_HOST', $hostName);
        $request->headers->set('host', $hostName);

        $requestStack = $this->getRequestStack();
        $requestStack->push($request);

        $requestInfoService = $this->getRequestInfoService();
        $oldRequestType = $requestInfoService->getChameleonRequestType();
        $requestInfoService->setChameleonRequestType(RequestTypeInterface::REQUEST_TYPE_FRONTEND); // temporarily change state

        // start request
        $chameleonFrontendController = $this->getChameleonFrontendController();
        $response = $chameleonFrontendController();
        $generatedNewsletter = $response->getContent();

        $requestInfoService->setChameleonRequestType($oldRequestType); // restore state
        $requestStack->pop();

        return $generatedNewsletter;
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
     * @return ChameleonSystem\NewsletterBundle\PostProcessing\Bridge\NewsletterUserDataFactoryInterface
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

    protected function getRequestStack(): RequestStack
    {
        return ServiceLocator::get('request_stack');
    }

    protected function getChameleonFrontendController(): ChameleonFrontendController
    {
        return ServiceLocator::get('chameleon_system_core.frontend_controller');
    }

    protected function getRequestInfoService(): RequestInfoServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.request_info_service');
    }
}
