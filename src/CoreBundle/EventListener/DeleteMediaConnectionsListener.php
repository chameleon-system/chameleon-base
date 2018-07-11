<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\EventListener;

use ChameleonSystem\CoreBundle\Event\DeleteMediaEvent;
use TCMSTableEditorMedia;

class DeleteMediaConnectionsListener
{
    /**
     * @param DeleteMediaEvent $event
     */
    public function onDeleteMedia(DeleteMediaEvent $event)
    {
        $id = $event->getDeletedMediaId();
        $tableEditor = new TCMSTableEditorMedia();
        $aConnections = $tableEditor->FetchConnections($id);
        foreach ($aConnections as $oConnection) {
            $oConnection->Delete($id);
        }
    }
}
