<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Service;

use ChameleonSystem\CoreBundle\CoreEvents;
use ChameleonSystem\CoreBundle\DataAccess\DataAccessCmsLanguageInterface;
use ChameleonSystem\CoreBundle\Event\LocaleChangedEvent;
use ChameleonSystem\CoreBundle\Service\Initializer\LanguageServiceInitializerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use TCMSUser;
use TdbCmsConfig;
use TdbCmsLanguage;

/**
 * Class LanguageService.
 */
class LanguageService implements LanguageServiceInterface
{
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var TdbCmsLanguage
     */
    private $activeLanguage;
    /**
     * Holds the fallback language in case we don't have a request (Console, ...). We hereby add a state to the service,
     * but the service isn't really stateless anyway because it uses the request to store our active language.
     *
     * @var TdbCmsLanguage|null
     */
    private $fallbackLanguage;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var LanguageServiceInitializerInterface
     */
    private $languageServiceInitializer;
    /**
     * @var bool
     */
    private $isInitializing = false;
    /**
     * @var DataAccessCmsLanguageInterface
     */
    private $dataAccessCmsLanguage;

    /**
     * @var TdbCmsLanguage|null
     */
    private $cmsBaseLanguage;

    /**
     * @param RequestStack                        $requestStack
     * @param EventDispatcherInterface            $eventDispatcher
     * @param LanguageServiceInitializerInterface $languageServiceInitializer
     * @param DataAccessCmsLanguageInterface      $dataAccessCmsLanguage
     */
    public function __construct(RequestStack $requestStack, EventDispatcherInterface $eventDispatcher, LanguageServiceInitializerInterface $languageServiceInitializer, DataAccessCmsLanguageInterface $dataAccessCmsLanguage)
    {
        $this->requestStack = $requestStack;
        $this->eventDispatcher = $eventDispatcher;
        $this->languageServiceInitializer = $languageServiceInitializer;
        $this->dataAccessCmsLanguage = $dataAccessCmsLanguage;
    }

    /**
     * {@inheritdoc}
     */
    public function getCmsBaseLanguageId()
    {
        return TdbCmsConfig::GetInstance()->fieldTranslationBaseLanguageId;
    }

    /**
     * {@inheritdoc}
     */
    public function getCmsBaseLanguage()
    {
        if (null !== $this->cmsBaseLanguage) {
            return $this->cmsBaseLanguage;
        }

        $this->cmsBaseLanguage = $this->dataAccessCmsLanguage->getLanguage($this->getCmsBaseLanguageId(), $this->getActiveLanguageId());

        return $this->cmsBaseLanguage;
    }

    /**
     * {@inheritdoc}
     */
    public function getLanguageIsoCode($languageId = null)
    {
        if (null === $languageId) {
            $languageId = $this->getActiveLanguageId();
        }
        if (null === $languageId) {
            return null;
        }
        /**
         * Do not load the language object itself to avoid possible circular loading attempts.
         */
        $rawData = $this->dataAccessCmsLanguage->getLanguageRaw($languageId);
        if (null === $rawData) {
            return null;
        }

        return $rawData['iso_6391'];
    }

    /**
     * {@inheritdoc}
     */
    public function getLanguageFromIsoCode($isoCode, $targetLanguageId = null)
    {
        if (null === $targetLanguageId) {
            $targetLanguageId = $this->getActiveLanguageId();
        }

        return $this->dataAccessCmsLanguage->getLanguageFromIsoCode($isoCode, $targetLanguageId);
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveLanguage()
    {
        if (null === $this->activeLanguage) {
            $this->initialize();
        }
        if (null === $this->activeLanguage) {
            if (null === $this->fallbackLanguage) {
                $this->initializeFallbackLanguage();
            }

            return $this->fallbackLanguage;
        }

        return $this->activeLanguage;
    }

    /**
     * @return void
     */
    private function initialize()
    {
        if ($this->isInitializing) {
            return;
        }
        $this->isInitializing = true;
        $this->languageServiceInitializer->initialize($this);
        $this->isInitializing = false;
    }

    /**
     * @return void
     */
    private function initializeFallbackLanguage()
    {
        if ($this->isInitializing) {
            return;
        }
        $this->isInitializing = true;
        $this->languageServiceInitializer->initializeFallbackLanguage($this);
        $this->isInitializing = false;
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveLanguageId()
    {
        $activeLanguageId = null;
        $language = $this->getActiveLanguage();
        if (null !== $language) {
            $activeLanguageId = $language->id;
        }

        return $activeLanguageId;
    }

    /**
     * {@inheritdoc}
     */
    public function getLanguage($id, $targetLanguageId = null)
    {
        if (null === $targetLanguageId) {
            $targetLanguageId = $this->getActiveLanguageId();
        }

        return $this->dataAccessCmsLanguage->getLanguage($id, $targetLanguageId);
    }

    /**
     * {@inheritdoc}
     */
    public function setActiveLanguage($languageId)
    {
        if (null === $languageId) {
            return;
        }
        /** @var TdbCmsLanguage $originalLanguage */
        $originalLanguage = $this->activeLanguage;
        $newLanguage = $this->dataAccessCmsLanguage->getLanguage($languageId, $languageId);
        $newLanguage->SetLanguage($languageId);
        $this->activeLanguage = $newLanguage;
        /**
         * Reload language after setting it as active language so that translated fields are displayed in the correct language.
         */
        $this->activeLanguage->LoadFromRow($this->activeLanguage->sqlData);
        $request = $this->requestStack->getCurrentRequest();
        if (null !== $request) {
            $request->attributes->set('_locale', $newLanguage->fieldIso6391);
        } else {
            $this->fallbackLanguage = $newLanguage;
        }

        if (null === $originalLanguage || $originalLanguage->id !== $this->getActiveLanguageId()) {
            $localeChanged = new LocaleChangedEvent($newLanguage->fieldIso6391, (null !== $originalLanguage) ? $originalLanguage->fieldIso6391 : null);
            $this->eventDispatcher->dispatch($localeChanged, CoreEvents::LOCALE_CHANGED);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveLocale()
    {
        $activeLanguage = $this->getActiveLanguage();
        if (null !== $activeLanguage) {
            return $activeLanguage->fieldIso6391;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveEditLanguage()
    {
        $oUser = TCMSUser::GetActiveUser();
        if (null === $oUser) {
            $currentEditLanguage = $this->getCmsBaseLanguage();
        } else {
            $currentEditLanguage = $oUser->GetCurrentEditLanguageObject();
        }

        return $currentEditLanguage;
    }

    /**
     * {@inheritdoc}
     */
    public function setFallbackLanguage(TdbCmsLanguage $fallbackLanguage)
    {
        $this->fallbackLanguage = $fallbackLanguage;
    }
}
