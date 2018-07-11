<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgCmsSessionWakeUpService
{
    /**
     * wake up session data.
     *
     * @param array $sessionData
     */
    public function wakeUpSessionData($sessionData)
    {
        reset($sessionData);
        foreach (array_keys($sessionData) as $key) {
            if (true === is_array($sessionData[$key])) {
                $this->wakeUpSessionData($sessionData[$key]);
            } else {
                $this->wakeUpSessionObject($sessionData[$key]);
            }
        }
        reset($sessionData);
    }

    public function wakeUpSessionObject($object)
    {
        if ($object instanceof IPkgCmsSessionPostWakeupListener) {
            $object->sessionWakeupHook();
        }
    }
}
