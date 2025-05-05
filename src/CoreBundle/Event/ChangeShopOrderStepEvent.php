<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ChangeShopOrderStepEvent extends Event
{
    /**
     * @var \TdbShopOrderStep[]
     */
    private $changedShopOrderSteps;

    /**
     * @param \TdbShopOrderStep[] $changedShopOrderSteps
     */
    public function __construct(array $changedShopOrderSteps)
    {
        $this->changedShopOrderSteps = $changedShopOrderSteps;
    }

    /**
     * @return \TdbShopOrderStep[]
     */
    public function getChangedShopOrderSteps()
    {
        return $this->changedShopOrderSteps;
    }
}
