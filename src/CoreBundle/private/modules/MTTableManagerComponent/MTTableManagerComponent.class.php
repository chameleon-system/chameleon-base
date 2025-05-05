<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * the tablemanager that can be used within other components (like a popup). it will
 * not add any info to the history stack.
 * /**/
class MTTableManagerComponent extends MTTableManager
{
    /**
     * does nothing.
     */
    public function AddURLHistory()
    {
    }

    protected function isInFrame(): bool
    {
        return true;
    }
}
