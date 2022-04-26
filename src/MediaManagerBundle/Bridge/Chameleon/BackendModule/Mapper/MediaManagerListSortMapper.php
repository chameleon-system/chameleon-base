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

use AbstractViewMapper;
use ChameleonSystem\MediaManager\SortColumnCollection;
use IMapperCacheTriggerRestricted;
use IMapperRequirementsRestricted;
use IMapperVisitorRestricted;

class MediaManagerListSortMapper extends AbstractViewMapper
{
    /**
     * @var SortColumnCollection
     */
    private $sortColumnCollection;

    /**
     * @param SortColumnCollection $sortColumnCollection
     */
    public function __construct(SortColumnCollection $sortColumnCollection)
    {
        $this->sortColumnCollection = $sortColumnCollection;
    }

    /**
     * {@inheritDoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function Accept(
        IMapperVisitorRestricted $oVisitor,
        $bCachingEnabled,
        IMapperCacheTriggerRestricted $oCacheTriggerManager
    ) {
        $oVisitor->SetMappedValue('sortColumns', $this->sortColumnCollection->getSortColumns());
    }
}
