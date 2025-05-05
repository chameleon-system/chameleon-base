<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\CoreEvents;
use ChameleonSystem\CoreBundle\Event\ChangeShopOrderStepEvent;

class TCMSTableEditorShopOrderStep extends TCMSTableEditor
{
    /**
     * {@inheritdoc}
     */
    protected function PostInsertHook($oFields)
    {
        parent::PostInsertHook($oFields);

        $changedStep = new TdbShopOrderStep($this->sId);
        $event = new ChangeShopOrderStepEvent([$changedStep]);
        $this->getEventDispatcher()->dispatch($event, CoreEvents::ADD_SHOP_ORDER_STEP);
    }

    /**
     * {@inheritdoc}
     */
    protected function PostSaveHook($oFields, $oPostTable)
    {
        parent::PostSaveHook($oFields, $oPostTable);

        $changedStep = new TdbShopOrderStep($this->sId);
        $event = new ChangeShopOrderStepEvent([$changedStep]);
        $this->getEventDispatcher()->dispatch($event, CoreEvents::UPDATE_SHOP_ORDER_STEP);
    }

    /**
     * {@inheritdoc}
     */
    public function Delete($sId = null)
    {
        parent::Delete($sId);

        $changedStep = new TdbShopOrderStep($this->sId);
        $event = new ChangeShopOrderStepEvent([$changedStep]);
        $this->getEventDispatcher()->dispatch($event, CoreEvents::DELETE_SHOP_ORDER_STEP);
    }
}
