<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\MediaManagerBundle\Bridge\Chameleon\BackendModule\Mapper;

use ChameleonSystem\CoreBundle\i18n\TranslationConstants;
use Symfony\Contracts\Translation\TranslatorInterface;

class MediaManagerPageSizeMapper extends \AbstractViewMapper
{
    /**
     * @var array
     */
    private $pageSizes;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(array $pageSizes, TranslatorInterface $translator)
    {
        $this->pageSizes = $pageSizes;
        $this->translator = $translator;
    }

    /**
     * {@inheritDoc}
     */
    public function GetRequirements(\IMapperRequirementsRestricted $oRequirements): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function Accept(
        \IMapperVisitorRestricted $oVisitor,
        $bCachingEnabled,
        \IMapperCacheTriggerRestricted $oCacheTriggerManager
    ): void {
        $oVisitor->SetMappedValue('pageSizes', $this->getPageSizes());
    }

    /**
     * @return array
     */
    private function getPageSizes()
    {
        $pageSizes = $this->pageSizes;
        $pageSizes = array_combine($pageSizes, $pageSizes);

        $keyShowAll = array_search(-1, $pageSizes, true);
        if (false === $keyShowAll) {
            return $pageSizes;
        }
        try {
            $translation = $this->translator->trans(
                'chameleon_system_media_manager.paging.page_size_show_all',
                [],
                TranslationConstants::DOMAIN_BACKEND
            );
            $pageSizes[$translation] = -1;
        } catch (\InvalidArgumentException $e) {
            $pageSizes['Show all'] = -1;
        }
        unset($pageSizes[$keyShowAll]);

        return $pageSizes;
    }
}
