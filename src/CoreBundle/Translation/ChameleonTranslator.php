<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Translation;

use ChameleonSystem\CoreBundle\i18n\TranslationConstants;
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Component\Translation\TranslatorInterface as LegacyTranslatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @psalm-suppress UndefinedInterfaceMethod, InvalidPropertyAssignmentValue
 * @FIXME This translator uses methods that are exclusive to `Symfony\Component\Translation\TranslatorInterface` (e.g. `transChoice`) but uses `Symfony\Contracts\Translation\TranslatorInterface` for `$delegate`
 */
class ChameleonTranslator implements LegacyTranslatorInterface, TranslatorInterface, TranslatorBagInterface
{
    /**
     * @var TranslatorInterface&TranslatorBagInterface
     */
    private $delegate;
    /**
     * @var RequestInfoServiceInterface
     */
    private $requestInfoService;

    /**
     * @param TranslatorInterface&TranslatorBagInterface $delegate
     * @param RequestInfoServiceInterface $requestInfoService
     */
    public function __construct(TranslatorInterface $delegate, RequestInfoServiceInterface $requestInfoService)
    {
        if (!$delegate instanceof TranslatorBagInterface) {
            throw new \LogicException('The translator must implement both TranslatorInterface and TranslatorBagInterface');
        }
        $this->delegate = $delegate;
        $this->requestInfoService = $requestInfoService;
    }

    /**
     * @param string $id
     * @param array $parameters
     * @param string|null $domain
     * @param string|null $locale
     * @return string
     */
    public function trans($id, array $parameters = array(), $domain = null, $locale = null)
    {
        if (null === $domain) {
            static $isBackendMode = null;
            if (null === $isBackendMode) {
                $isBackendMode = $this->requestInfoService->isBackendMode();
            }
            if ($isBackendMode) {
                $domain = TranslationConstants::DOMAIN_BACKEND;
            }
        }

        return $this->delegate->trans($id, $parameters, $domain, $locale);
    }

    /**
     * @param string $id
     * @param int $number
     * @param array $parameters
     * @param string $domain
     * @param string $locale
     * @return string
     */
    public function transChoice($id, $number, array $parameters = array(), $domain = null, $locale = null)
    {
        return $this->delegate->transChoice($id, $number, $parameters, $domain, $locale);
    }

    /**
     * @param string $locale
     * @return void
     */
    public function setLocale($locale)
    {
        $this->delegate->setLocale($locale);
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale()
    {
        return $this->delegate->getLocale();
    }

    /**
     * {@inheritdoc}
     */
    public function getCatalogue($locale = null)
    {
        return $this->delegate->getCatalogue($locale);
    }
}
