<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\EventListener\AddBackendToasterMessageListener;
use ChameleonSystem\CoreBundle\i18n\TranslationConstants;
use ChameleonSystem\CoreBundle\Service\ActivePageServiceInterface;
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * the class manages messages. any module can place messages into it. they are kept there until they are consumed
 * note that the system will store message codes and translate them using the connected database table. messages
 * in the database may hold placeholders for variables:
 * variables need to be marked as follows: [{varname:type}].
 *  - type must be one of: string, date, or number.
 *  - if type is a number, then the number of decimal places to show may be added as follows:
 *  - number:N (N = number of decimal places).
 *
 * since messages may be in different languages, the system will try to fetch the language from tcmsactivepage
 *
 * the object maintains its contents via session until the data is consumed
 *
 * NOTE: InjectMessageIntoString is called on the COMPLETE RENDERED PAGE. so you can add messages to your page
 * without having to worry about caching. just use the format described in the method InjectMessageIntoString
 *
 * /**/
class TCMSMessageManager
{
    public const SESSION_KEY_NAME = 'core/cmsmessagemanager';
    public const GLOBAL_CONSUMER_NAME = '_all';
    public const AUTO_CREATED_MARKER = '[TODO] ';

    /**
     * array of all messages.
     *
     * @var array
     */
    protected $aMessages = [];

    /**
     * @param bool $bReload
     *
     * @return TCMSMessageManager
     *
     * @deprecated use the service chameleon_system_core.flash_messages instead
     */
    public static function GetInstance($bReload = false)
    {
        $request = ServiceLocator::get('request_stack')->getCurrentRequest();
        if ((null === $request) || (null !== $request->getSession() && false === $request->getSession()->isStarted())) {
            return null;
        }
        if (false === $request->getSession()->isStarted()) {
            return null;
        }
        if ($bReload) {
            if (true === $request->getSession()->has(self::SESSION_KEY_NAME)) {
                $request->getSession()->remove(self::SESSION_KEY_NAME);
            }
        }

        if (false === $request->getSession()->has(self::SESSION_KEY_NAME)) {
            $request->getSession()->set(self::SESSION_KEY_NAME, new self());
        }

        return $request->getSession()->get(self::SESSION_KEY_NAME);
    }

    /**
     * add a message code to the queue.
     *
     * @param string $sConsumerName
     * @param string $sMessageCode
     * @param array $aMessageCodeParameters
     */
    public function AddMessage($sConsumerName, $sMessageCode, $aMessageCodeParameters = [])
    {
        if (!array_key_exists($sConsumerName, $this->aMessages)) {
            $this->aMessages[$sConsumerName] = new TIterator();
        }
        if (TGlobal::IsCMSMode()) {
            $this->AddBackendMessage($sConsumerName, $sMessageCode, $aMessageCodeParameters);
        } else {
            $iPortalId = $this->getPortalDomainService()->getActivePortal()->id;

            /** @var $oMessage TdbCmsMessageManagerMessage */
            $oMessage = TdbCmsMessageManagerMessage::GetNewInstance();
            $oMessage->SetLanguage($this->getLanguageService()->getActiveLanguageId());

            // try to load the message. if this fails, we create the message....
            if (!$oMessage->LoadFromFields(['name' => $sMessageCode, 'cms_portal_id' => $iPortalId])) {
                // write message to table
                // Note: we use the TCMSRecordWritable instead of the TCMSTableEditorManager, since this may happen in the front and backend... so we need to make sure anyone
                // can write here...
                /** @var $oTmpTable TCMSRecordWritable */
                $oTmpTable = TdbCmsMessageManagerMessage::GetNewInstance();
                $oTmpTable->AllowEditByAll(true);

                $sDescription = $this->GetDefaultDescription($sConsumerName, $aMessageCodeParameters);
                $sErrorMessage = $this->GetDefaultMessage($sMessageCode);

                $aPostData = ['cms_portal_id' => $iPortalId, 'name' => $sMessageCode, 'description' => $sDescription, 'message' => $sErrorMessage];
                if (null !== $oMessage->id) {
                    $aPostData['id'] = $oMessage->id;
                }

                $oTmpTable->LoadFromRow($aPostData);
                $oTmpTable->Save();
                $oMessage->Load($oTmpTable->id);
            }
            $oMessage->SetMessageParameters($aMessageCodeParameters);

            // add message
            $this->aMessages[$sConsumerName]->AddItem($oMessage);
        }
    }

    /**
     * get description text for new created messages in the database.
     *
     * @param string $sConsumerName
     * @param array $aMessageCodeParameters
     *
     * @return string
     */
    protected function GetDefaultDescription($sConsumerName, $aMessageCodeParameters)
    {
        $oGlobal = TGlobal::instance();
        $activePageService = $this->getActivePageService();
        if (null === $activePageService->getActivePage()) {
            $sCallingPage = '';
        } else {
            $sCallingPage = $activePageService->getLinkToActivePageRelative();
        }

        $sDescription = \ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans(
            self::AUTO_CREATED_MARKER.'chameleon_system_core.cms_message_manager.auto_entry_description',
            [
                '%consumerName%' => $sConsumerName,
                '%createdDate%' => date('Y-m-d H:i:s'),
                '%parameter%' => print_r($aMessageCodeParameters, true),
            ]
        );
        if (!empty($sCallingPage)) {
            $sDescription .= "\nWebPage: {$sCallingPage}";
        } else {
            $sDescription .= "\nCMSPage: ".$oGlobal->GetUserData('pagedef');
        }

        return $sDescription;
    }

    /**
     * get message text for new created messages in the database.
     *
     * @param string $sMessageCode
     *
     * @return string
     */
    protected function GetDefaultMessage($sMessageCode)
    {
        return $this->getTranslator()->trans('chameleon_system_core.cms_message_manager.invalid_code', ['%messageCode%' => $sMessageCode], TranslationConstants::DOMAIN_BACKEND);
    }

    /**
     * add a message code to the queue of the backend message manager.
     *
     * @param string $sConsumerName
     * @param string $sMessageCode
     * @param array $aMessageCodeParameters
     */
    protected function AddBackendMessage($sConsumerName, $sMessageCode, $aMessageCodeParameters = [])
    {
        /** @var $oMessage TdbCmsMessageManagerBackendMessage */
        $oMessage = TdbCmsMessageManagerBackendMessage::GetNewInstance();

        // try to load the message. if this fails, we create the message....
        if (!$oMessage->LoadFromFields(['name' => $sMessageCode, 'cms_config_id' => 1])) {
            // write message to table
            // Note: we use the TCMSRecordWritable instead of the TCMSTableEditorManager, since this may happen in the front and backend... so we need to make sure anyone
            // can write here...
            /** @var $oTmpTable TCMSRecordWritable */
            $oTmpTable = new TCMSRecordWritable();
            $oTmpTable->table = $oMessage->table;
            $oTmpTable->AllowEditByAll(true);

            $oGlobal = TGlobal::instance();
            $description = \ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans(
                self::AUTO_CREATED_MARKER.'chameleon_system_core.cms_message_manager.auto_entry_description',
                [
                    '%consumerName%' => $sConsumerName,
                    '%createdDate%' => date('Y-m-d H:i:s'),
                    '%parameter%' => print_r($aMessageCodeParameters, true),
                ]
            );
            $description .= "\nCMSPage: ".$oGlobal->GetUserData('pagedef');

            $sErrorMessage = \ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_message_manager.invalid_code').'['.$sMessageCode.']';

            $aPostData = ['cms_config_id' => 1, 'name' => $sMessageCode, 'description' => $description, 'message' => $sErrorMessage];

            $oTmpTable->LoadFromRow($aPostData);
            $oTmpTable->Save();
            $oMessage->Load($oTmpTable->id);
        }
        $oMessage->SetMessageParameters($aMessageCodeParameters);

        // add message
        $this->aMessages[$sConsumerName]->AddItem($oMessage);
    }

    /**
     * returns all messages for the consumer. Note that global messages will be added to
     * the end of the list.
     *
     * @param bool $bRemove
     *
     * @return TIterator
     */
    public function ConsumeMessages(string $sConsumerName = '', $bRemove = true, bool $includeGlobal = true)
    {
        $oMessages = null;

        if (empty($sConsumerName)) {
            foreach ($this->aMessages as $consumer => $messages) {
                if (null === $oMessages) {
                    $oMessages = $messages;
                } else {
                    while (false !== ($oMessage = $messages->Next())) {
                        $oMessages->AddItem($oMessage);
                    }
                }

                if (true === $bRemove) {
                    unset($this->aMessages[$consumer]);
                }
            }
        } else {
            if (array_key_exists($sConsumerName, $this->aMessages)) {
                $oMessages = $this->aMessages[$sConsumerName];
                if (true === $bRemove) {
                    unset($this->aMessages[$sConsumerName]);
                }
            }
        }

        // add global parameters
        if (true === $includeGlobal && array_key_exists(self::GLOBAL_CONSUMER_NAME, $this->aMessages)) {
            if (null === $oMessages) {
                $oMessages = $this->aMessages[self::GLOBAL_CONSUMER_NAME];
            } else {
                while (false !== ($oGlobalMessage = $this->aMessages[self::GLOBAL_CONSUMER_NAME]->Next())) {
                    $oMessages->AddItem($oGlobalMessage);
                }
            }
            if (true === $bRemove) {
                unset($this->aMessages[self::GLOBAL_CONSUMER_NAME]);
            }
        }

        return $oMessages;
    }

    /**
     * render messages for consumer.
     *
     * @param string $sConsumerName
     * @param string $sViewName
     * @param string $sViewType
     * @param array $aCallTimeVars
     * @param bool $bRemove
     *
     * @return string
     */
    public function RenderMessages($sConsumerName, $sViewName = null, $sViewType = null, $aCallTimeVars = [], $bRemove = true)
    {
        $sMsg = '';
        $oMessages = $this->ConsumeMessages($sConsumerName, $bRemove);
        if (!is_null($oMessages)) {
            $oMessages->GoToStart();
            /** @var TdbCmsMessageManagerMessage $oMessage */
            while ($oMessage = $oMessages->Next()) {
                $sMsg .= $oMessage->Render($sViewName, $sViewType, $aCallTimeVars);
            }
        }

        return $sMsg;
    }

    /**
     * clear messages for a consumer. if no consumer is given, all messages will be cleared.
     *
     * @param string $sConsumerName
     */
    public function ClearMessages($sConsumerName = null)
    {
        if (is_null($sConsumerName)) {
            $this->aMessages = [];
        } elseif (array_key_exists($sConsumerName, $this->aMessages)) {
            unset($this->aMessages[$sConsumerName]);
        }
    }

    /**
     * return true if there are messages for the consumer (also returns true if there
     * are global messages.
     *
     * @param string $sConsumerName
     *
     * @return bool
     */
    public function ConsumerHasMessages($sConsumerName, bool $includeGlobal = true)
    {
        $bHasMessages = false;
        if ($this->ConsumerMessageCount($sConsumerName, $includeGlobal) > 0) {
            $bHasMessages = true;
        }

        return $bHasMessages;
    }

    /**
     * return the number of messages assigned to the consumer (global message will be included in the count by default).
     *
     * @param string $sConsumerName
     *
     * @return int
     */
    public function ConsumerMessageCount($sConsumerName, bool $includeGlobal = true)
    {
        $iMessageCount = 0;
        if (true === $includeGlobal && array_key_exists(self::GLOBAL_CONSUMER_NAME, $this->aMessages)) {
            $iMessageCount = $this->aMessages[self::GLOBAL_CONSUMER_NAME]->Length();
        }
        if (array_key_exists($sConsumerName, $this->aMessages)) {
            $iMessageCount += $this->aMessages[$sConsumerName]->Length();
        }

        return $iMessageCount;
    }

    /**
     * return the total number of messages in the system.
     *
     * @return int
     */
    public function TotalMessageCount()
    {
        $iMessageCount = 0;
        reset($this->aMessages);
        foreach (array_keys($this->aMessages) as $sConsumer) {
            $iMessageCount = $iMessageCount + $this->aMessages[$sConsumer]->Length();
        }
        reset($this->aMessages);

        return $iMessageCount;
    }

    /**
     * inject all messages for all consumers into the string passed - and return
     * the string. The method will search for variables of the form:
     * [{CMSMSG-CONSUMER-NAME:viewName:classtype}]
     * where viewName and classtype are optional (classtype = Core, Custom-Core, Customer).
     *
     * @example [{CMSMSG-myconsumer}]
     *
     * @para string $sText
     *
     * @return string
     */
    public function InjectMessageIntoString($sText)
    {
        // find out which items will be replaced
        if (false === stripos($sText, '[{CMSMSG-')) {
            return $sText;
        }
        if (0 === count($this->aMessages)) {
            $matchString = '/\[\{CMSMSG-(.*?)\}\]/si';

            return preg_replace($matchString, '', $sText);
        }
        $matchString = '/\[\{CMSMSG-(.*?)(:(.*?))?(:(Core|Custom-Core|Customer))?\}\]/si';

        return preg_replace_callback($matchString, [$this, 'InjectMessageIntoStringReplaceCallback'], $sText);
    }

    /**
     * replace message vars callback (called by InjectMessageIntoString).
     */
    protected function InjectMessageIntoStringReplaceCallback($aMatches)
    {
        $sConsumer = $aMatches[1];
        $sView = null;
        $sType = null;
        if (array_key_exists(3, $aMatches)) {
            $sView = $aMatches[3];
        }
        if (array_key_exists(5, $aMatches)) {
            $sType = $aMatches[5];
        }

        return $this->RenderMessages($sConsumer, $sView, $sType);
    }

    /**
     * return a list (array) of all consumers that have one or more messages.
     *
     * @return array
     */
    public function GetConsumerListWithMessages()
    {
        return array_keys($this->aMessages);
    }

    /**
     * get all css classes for consumer messages, separated by spaces or optional divider.
     *
     * @param string $sConsumerName
     * @param string $sDivider
     *
     * @return string
     */
    public function GetClassesForConsumer($sConsumerName, $sDivider = ' ')
    {
        $aFieldClasses = [];
        if ($this->ConsumerHasMessages($sConsumerName)) {
            $oMessages = $this->ConsumeMessages($sConsumerName, false);
            while ($oMessage = $oMessages->Next()) {
                $oMessageType = TdbCmsMessageManagerMessageType::GetNewInstance();
                if (!$oMessageType->Load($oMessage->fieldCmsMessageManagerMessageTypeId)) {
                    $oMessageType = null;
                }
                if (!is_null($oMessageType) && !empty($oMessageType->fieldClass)) {
                    if (!in_array($oMessageType->fieldClass, $aFieldClasses)) {
                        array_push($aFieldClasses, $oMessageType->fieldClass);
                    }
                }
            }
            $oMessages->GoToStart();
        }

        return implode($sDivider, $aFieldClasses);
    }

    /**
     * @param string $id message ID to be translated
     * @param string $type one of the message types defined in cms.v2.js:toasterMessage()
     * @param array $parameters for the message translation
     * @param string $domain for the message translation
     */
    public function addBackendToasterMessage($id, $type = 'ERROR', array $parameters = [], $domain = TranslationConstants::DOMAIN_BACKEND)
    {
        $translator = $this->getTranslator();
        $message = $translator->trans($id, $parameters, $domain);
        $listener = new AddBackendToasterMessageListener($message, $type);
        $dispatcher = $this->getEventDispatcher();
        $dispatcher->addListener(KernelEvents::RESPONSE, [$listener, 'addMessage']);
    }

    /**
     * @return ActivePageServiceInterface
     */
    private function getActivePageService()
    {
        return ServiceLocator::get('chameleon_system_core.active_page_service');
    }

    private function getLanguageService(): LanguageServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.language_service');
    }

    private function getPortalDomainService(): PortalDomainServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.portal_domain_service');
    }

    /**
     * @return TranslatorInterface
     */
    private function getTranslator()
    {
        return ServiceLocator::get('translator');
    }

    /**
     * @return EventDispatcherInterface
     */
    private function getEventDispatcher()
    {
        return ServiceLocator::get('event_dispatcher');
    }
}
