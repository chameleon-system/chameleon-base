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
use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ChameleonTranslator implements TranslatorInterface, TranslatorBagInterface
{
    /**
     * @var TranslatorInterface&TranslatorBagInterface
     */
    private $delegate;
    private RequestInfoServiceInterface $requestInfoService;

    /**
     * @param TranslatorInterface&TranslatorBagInterface $delegate
     * @param RequestInfoServiceInterface $requestInfoService
     */
    public function __construct(TranslatorInterface $delegate, RequestInfoServiceInterface $requestInfoService)
    {
        if (!$delegate instanceof TranslatorBagInterface) {
            throw new \LogicException('The translator must implement both TranslatorInterface and TranslatorBagInterface');
        }
        /** @psalm-suppress InvalidPropertyAssignmentValue */
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
    public function trans(string $id, array $parameters = array(), ?string $domain = null, ?string $locale = null): string
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
     * {@inheritdoc}
     */
    public function getLocale(): string
    {
        return $this->delegate->getLocale();
    }

    /**
     * {@inheritdoc}
     */
    public function getCatalogue(?string $locale = null): MessageCatalogueInterface
    {
        return $this->delegate->getCatalogue($locale);
    }

    public function getCatalogues(): array
    {
        return $this->delegate->getCatalogues();
    }

}
