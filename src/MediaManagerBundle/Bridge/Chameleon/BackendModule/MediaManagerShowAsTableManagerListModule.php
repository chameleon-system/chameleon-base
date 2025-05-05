<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\MediaManagerBundle\Bridge\Chameleon\BackendModule;

use ChameleonSystem\MediaManagerBundle\Bridge\Chameleon\ListManager\TCMSListManagerMediaManager;

class MediaManagerShowAsTableManagerListModule extends \MTPkgViewRendererAbstractModuleMapper
{
    /**
     * {@inheritDoc}
     */
    public function Accept(
        \IMapperVisitorRestricted $oVisitor,
        $bCachingEnabled,
        \IMapperCacheTriggerRestricted $oCacheTriggerManager
    ) {
        $image = new \TCMSImage();
        $image->Load('1');

        $listManager = new TCMSListManagerMediaManager();
        $listManager->Init($image);
        $oVisitor->SetMappedValue('tableHtml', $listManager->GetList());
    }
}
