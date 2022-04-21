<?php

namespace ChameleonSystem\CoreBundle\RequestState\Interfaces;

/**
 * Calculating the request state hash may require access to Tdb Objects which in turn require the request state hash.
 * The lock prevents the request state hash calculation from entering an endless loop.
 */
interface HashCalculationLockInterface
{
    /**
     * Acquire lock - return false if already locked.
     *
     * @return bool
     */
    public function lock();

    /**
     * @return void
     */
    public function release();
}
