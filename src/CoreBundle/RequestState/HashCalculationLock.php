<?php

namespace ChameleonSystem\CoreBundle\RequestState;

use ChameleonSystem\CoreBundle\RequestState\Interfaces\HashCalculationLockInterface;

class HashCalculationLock implements HashCalculationLockInterface
{
    /**
     * @var bool
     */
    private $isLocked = false;

    /**
     * {@inheritdoc}
     */
    public function lock()
    {
        if ($this->isLocked) {
            return false;
        }
        $this->isLocked = true;

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function release()
    {
        $this->isLocked = false;
    }
}
