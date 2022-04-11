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
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ChameleonTranslator implements TranslatorInterface, TranslatorBagInterface, LocaleAwareInterface
{
    /**
     * @var TranslatorInterface|TranslatorBagInterface
     */
    private $delegate;
    /**
     * @var RequestInfoServiceInterface
     */
    private $requestInfoService;

    /**
     * @param TranslatorInterface         $delegate
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
     * {@inheritdoc}
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
