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

use ChameleonSystem\MediaManager\Exception\DataAccessException;
use ChameleonSystem\MediaManager\Interfaces\MediaItemDataAccessInterface;

class MediaManagerSearchAutoCompleteMapper extends \AbstractViewMapper
{
    /**
     * @var MediaItemDataAccessInterface
     */
    private $mediaItemDataAccess;

    public function __construct(MediaItemDataAccessInterface $mediaItemDataAccess)
    {
        $this->mediaItemDataAccess = $mediaItemDataAccess;
    }

    /**
     * {@inheritDoc}
     */
    public function GetRequirements(\IMapperRequirementsRestricted $oRequirements): void
    {
        $oRequirements->NeedsSourceObject('searchTerm', 'string');
        $oRequirements->NeedsSourceObject('language', \TdbCmsLanguage::class);
    }

    /**
     * {@inheritDoc}
     */
    public function Accept(
        \IMapperVisitorRestricted $oVisitor,
        $bCachingEnabled,
        \IMapperCacheTriggerRestricted $oCacheTriggerManager
    ): void {
        /**
         * @var string $searchTerm
         * @var \TdbCmsLanguage $language
         */
        $searchTerm = $oVisitor->GetSourceObject('searchTerm');
        $language = $oVisitor->GetSourceObject('language');
        try {
            $rows = $this->mediaItemDataAccess->getTermsToAutoSuggestForSearchTerm($searchTerm, $language->id);
        } catch (DataAccessException $e) {
            $oVisitor->SetMappedValue('hasError', true);

            return;
        }
        $oVisitor->SetMappedValue('autoCompleteTags', $rows);
    }
}
