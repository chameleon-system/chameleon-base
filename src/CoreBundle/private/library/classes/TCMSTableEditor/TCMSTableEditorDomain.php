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
    private const URL_SUFFIX_REGEX = '/^[a-z0-9]+(?:[-_][a-z0-9]+)*$/';

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
    protected function DataIsValid($postData, $oFields = null)
    {
        $isValid = parent::DataIsValid($postData, $oFields);

        if (false === $this->isUrlSuffixConfigurationValid($postData)) {
            $isValid = false;
        }

        return $isValid;
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

    private function isUrlSuffixConfigurationValid(array $postData): bool
    {
        $isValid = true;
        $urlSuffix = (string) ($postData['url_suffix'] ?? '');
        $portalIdentifier = $this->getPortalIdentifier($postData);

        if ('' !== $urlSuffix && 1 !== preg_match(self::URL_SUFFIX_REGEX, $urlSuffix)) {
            return $isValid;
        }

        if ('' !== $urlSuffix && '' === (string) ($postData['cms_language_id'] ?? '')) {
            $this->getFlashMessageService()->addMessage(
                TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER,
                'TABLEEDITOR_DOMAIN_URL_SUFFIX_REQUIRES_LANGUAGE'
            );
            $isValid = false;
        }

        if ('' !== $urlSuffix && null !== $portalIdentifier && mb_strtolower($portalIdentifier) === $urlSuffix) {
            $this->getFlashMessageService()->addMessage(
                TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER,
                'TABLEEDITOR_DOMAIN_URL_SUFFIX_PORTAL_IDENTIFIER_CONFLICT'
            );
            $isValid = false;
        }

        if (false === $this->isUrlSuffixUniqueWithinPortal($postData)) {
            $this->getFlashMessageService()->addMessage(
                TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER,
                'TABLEEDITOR_DOMAIN_URL_SUFFIX_NOT_UNIQUE'
            );
            $isValid = false;
        }

        return $isValid;
    }

    private function isUrlSuffixUniqueWithinPortal(array $postData): bool
    {
        $portalId = $this->getPortalId($postData);
        if (null === $portalId) {
            return true;
        }

        $urlSuffix = trim((string) ($postData['url_suffix'] ?? ''));
        $hosts = $this->getConfiguredHosts($postData);
        if ('' === $urlSuffix || 0 === count($hosts)) {
            return true;
        }

        $queryParts = [];
        $parameters = [
            'portalId' => $portalId,
            'urlSuffix' => mb_strtolower($urlSuffix),
        ];

        if (null !== $this->sId) {
            $queryParts[] = '`id` != :recordId';
            $parameters['recordId'] = $this->sId;
        }

        $hostConditions = [];
        foreach ($hosts as $index => $host) {
            $parameterName = 'host'.$index;
            $hostConditions[] = sprintf('LOWER(TRIM(COALESCE(`name`, \'\'))) = :%1$s OR LOWER(TRIM(COALESCE(`sslname`, \'\'))) = :%1$s', $parameterName);
            $parameters[$parameterName] = $host;
        }

        $queryParts[] = '`cms_portal_id` = :portalId';
        $queryParts[] = 'LOWER(TRIM(COALESCE(`url_suffix`, \'\'))) = :urlSuffix';
        $queryParts[] = '('.implode(' OR ', $hostConditions).')';

        $query = 'SELECT `id`
                    FROM `cms_portal_domains`
                   WHERE '.implode(' AND ', $queryParts).'
                   LIMIT 1';

        return false === $this->getDatabaseConnection()->fetchOne($query, $parameters);
    }

    /**
     * @return list<string>
     */
    private function getConfiguredHosts(array $postData): array
    {
        $hosts = [];
        foreach (['name', 'sslname'] as $fieldName) {
            $value = trim((string) ($postData[$fieldName] ?? $this->oTable?->sqlData[$fieldName] ?? ''));
            if ('' !== $value) {
                $normalizedHost = mb_strtolower($value);
                $hosts[$normalizedHost] = $normalizedHost;
            }
        }

        return array_values($hosts);
    }

    private function getPortalId(array $postData): ?string
    {
        $portalId = (string) ($postData['cms_portal_id'] ?? $this->oTable?->sqlData['cms_portal_id'] ?? $this->sRestriction ?? '');

        if ('' === $portalId) {
            return null;
        }

        return $portalId;
    }

    private function getPortalIdentifier(array $postData): ?string
    {
        $portalId = $this->getPortalId($postData);
        if (null === $portalId) {
            return null;
        }

        $portalIdentifier = $this->getDatabaseConnection()->fetchOne(
            'SELECT `identifier` FROM `cms_portal` WHERE `id` = :portalId',
            ['portalId' => $portalId]
        );

        if (false === $portalIdentifier) {
            return null;
        }

        return (string) $portalIdentifier;
    }
}
