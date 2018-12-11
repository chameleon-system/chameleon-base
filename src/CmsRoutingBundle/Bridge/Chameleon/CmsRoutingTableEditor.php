<?php

namespace ChameleonSystem\CmsRoutingBundle\Bridge\Chameleon;

use ChameleonSystem\CmsRoutingBundle\Event\RoutingConfigChangedEvent;
use ChameleonSystem\CoreBundle\CoreEvents;
use TCMSTableEditor;

class CmsRoutingTableEditor extends TCMSTableEditor
{
    protected function PostSaveHook(&$oFields, &$oPostTable)
    {
        parent::PostSaveHook($oFields, $oPostTable);

        $this->getEventDispatcher()->dispatch(
            CoreEvents::CHANGE_ACTIVATE_ROUTING_CONFIG,
            new RoutingConfigChangedEvent($this->getNameColumnValue())
        );
    }
}
