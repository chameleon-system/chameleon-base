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
 * the session manager will call sessionWakeupHook on all classes stored within the session that implement this interface
 * Interface IPkgCmsSessionPostWakeupListener.
 */
interface IPkgCmsSessionPostWakeupListener
{
    /**
     * method is called on objects recovered from session after the session has been started.
     * you can use this method to perform post session wakeup logic - but do not use this to delay property serialization of an object (see #25251 to see why this is a bad idea).
     */
    public function sessionWakeupHook();
}
