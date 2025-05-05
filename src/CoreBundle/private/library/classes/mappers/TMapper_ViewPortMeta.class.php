<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TMapper_ViewPortMeta extends AbstractViewMapper
{
    /**
     * @var TCMSViewPortManager
     */
    private $viewPortManager;

    public function __construct(?TCMSViewPortManager $viewPortManager = null)
    {
        if (null === $viewPortManager) {
            $this->viewPortManager = ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.view_port_manager');
        } else {
            $this->viewPortManager = $viewPortManager;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(
        IMapperVisitorRestricted $oVisitor,
        $bCachingEnabled,
        IMapperCacheTriggerRestricted $oCacheTriggerManager
    ) {
        $oVisitor->SetMappedValue('sViewPortContent', $this->viewPortManager->getViewPortContent());
    }
}
