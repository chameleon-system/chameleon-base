<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CmsRoutingBundle\Bridge\Chameleon;

use ChameleonSystem\CmsRoutingBundle\Event\RoutingConfigChangedEvent;
use ChameleonSystem\CoreBundle\CoreEvents;

class CmsRoutingTableEditor extends \TCMSTableEditor
{
    /**
     * {@inheritDoc}
     */
    protected function PostSaveHook($oFields, $oPostTable)
    {
        parent::PostSaveHook($oFields, $oPostTable);

        $this->getEventDispatcher()->dispatch(
            new RoutingConfigChangedEvent($this->oTable),
            CoreEvents::CHANGE_ROUTING_CONFIG
        );
    }
}
