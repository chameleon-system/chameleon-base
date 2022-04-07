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

use ChameleonSystem\CoreBundle\Event\BackendLoginEvent;
use ChameleonSystem\SanityCheck\Handler\CheckHandlerInterface;
use ChameleonSystem\SanityCheck\Outcome\CheckOutcome;
use ChameleonSystem\SanityCheck\Output\CheckOutputInterface;

class SanityCheckOnAdminLoginListener
{
    /**
     * @var CheckHandlerInterface
     */
    private $checkHandler;
    /**
     * @var CheckOutputInterface
     */
    private $checkOutput;
    /**
     * @var array
     */
    private $checkList;

    public function __construct(CheckHandlerInterface $checkHandler, CheckOutputInterface $checkOutput, array $checkList)
    {
        $this->checkHandler = $checkHandler;
        $this->checkOutput = $checkOutput;
        $this->checkList = $checkList;
    }

    /**
     * @return void
     */
    public function onLogin(BackendLoginEvent $event)
    {
        $user = $event->getUser();

        $accessManagerUser = new \TAccessManagerUser();
        $accessManagerUser->InitFromObject($user);
        if ($accessManagerUser->IsAdmin()) {
            $checkOutcomeList = $this->checkHandler->checkSome($this->checkList);
            foreach ($checkOutcomeList as $checkOutcome) {
                if ($checkOutcome->getLevel() > CheckOutcome::OK) {
                    $this->checkOutput->gather($checkOutcome);
                }
            }
            $this->checkOutput->commit();
        }
    }
}
