<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\CoreEvents;
use ChameleonSystem\CoreBundle\Event\ChangeDomainEvent;
use ChameleonSystem\CoreBundle\Exception\InvalidPortalDomainException;
use ChameleonSystem\CoreBundle\Interfaces\FlashMessageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use Psr\Log\LoggerInterface;

class TCMSTableEditorDomain extends TCMSTableEditor
{
    /**
     * {@inheritdoc}
     */
    protected function _OverwriteDefaults($oFields)
    {
        parent::_OverwriteDefaults($oFields);

        /**
         * @var TCMSFieldUniqueMarker $nameField
         */
        $nameField = $oFields->FindItemWithProperty('name', 'is_master_domain');
        if (false === $nameField) {
            return;
        }
        if (false === $this->isThereAPrimaryDomainForThePortalAndLanguage('')) {
            $nameField->data = '1';
        } else {
            $nameField->data = '0';
        }
    }

    /**
     * @param string $languageId
     *
     * @return bool
     */
    private function isThereAPrimaryDomainForThePortalAndLanguage($languageId)
    {
        $portalId = $this->sRestriction;
        try {
            return $this->getPortalDomainService()->hasPrimaryDomain($portalId, $languageId);
        } catch (InvalidPortalDomainException $e) {
            $this->getLogger()->error('Error while trying to determine if a primary domain is set: '.$e->getMessage());

            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function PrepareDataForSave($postData)
    {
        $postData = parent::PrepareDataForSave($postData);
        if (false === isset($postData['is_master_domain']) || '1' === $postData['is_master_domain']) {
            return $postData;
        }

        /**
         * @var TdbCmsPortalDomains|null $preChangeData
         */
        $preChangeData = $this->oTablePreChangeData;
        if (true === $preChangeData->fieldIsMasterDomain && $preChangeData->fieldCmsLanguageId === $postData['cms_language_id']) {
            $postData['is_master_domain'] = '1';
            $this->getFlashMessageService()->addMessage(TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER, 'TABLEEDITOR_DOMAIN_UNSET_PRIMARY_NOT_POSSIBLE');
        } elseif (false === $this->isThereAPrimaryDomainForThePortalAndLanguage($postData['cms_language_id'])) {
            $postData['is_master_domain'] = '1';
        }

        return $postData;
    }

    /**
     * {@inheritdoc}
     */
    protected function PostInsertHook($oFields)
    {
        parent::PostInsertHook($oFields);

        $changedDomain = new TdbCmsPortalDomains($this->sId);
        $event = new ChangeDomainEvent([$changedDomain]);
        $this->getEventDispatcher()->dispatch($event, CoreEvents::ADD_DOMAIN);
    }

    /**
     * {@inheritdoc}
     */
    protected function PostSaveHook($oFields, $oPostTable)
    {
        parent::PostSaveHook($oFields, $oPostTable);

        $changedDomain = new TdbCmsPortalDomains($this->sId);
        $event = new ChangeDomainEvent([$changedDomain]);
        $this->getEventDispatcher()->dispatch($event, CoreEvents::UPDATE_DOMAIN);
    }

    /**
     * {@inheritdoc}
     */
    public function Delete($sId = null)
    {
        parent::Delete($sId);

        $changedDomain = new TdbCmsPortalDomains($this->sId);
        $event = new ChangeDomainEvent([$changedDomain]);
        $this->getEventDispatcher()->dispatch($event, CoreEvents::DELETE_DOMAIN);
    }

    /**
     * @return FlashMessageServiceInterface
     */
    private function getFlashMessageService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.flash_messages');
    }

    /**
     * @return LoggerInterface
     */
    private function getLogger()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('logger');
    }

    /**
     * @return PortalDomainServiceInterface
     */
    private function getPortalDomainService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.portal_domain_service');
    }
}
