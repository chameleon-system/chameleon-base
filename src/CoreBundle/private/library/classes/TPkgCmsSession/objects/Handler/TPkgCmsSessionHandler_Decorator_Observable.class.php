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
 * @deprecated since 6.2.0 - no longer used.
 */
class TPkgCmsSessionHandler_Decorator_Observable extends \Symfony\Component\HttpFoundation\Session\Storage\Proxy\SessionHandlerProxy implements \IPkgCmsEventObservable
{
    private $observer = array();

    /**
     * Close the session.
     *
     * @see http://php.net/manual/en/sessionhandlerinterafce.close.php
     *
     * @return bool <p>
     *              The return value (usually TRUE on success, FALSE on failure).
     *              Note this value is returned internally to PHP for processing.
     *              </p>
     */
    public function close()
    {
        $event = new TPkgCmsEvent();
        $event
            ->SetSubject($this)
            ->SetContext('pkgCmsSession')
            ->SetName(__METHOD__)
            ->SetData(null);
        $this->notifyObservers($event);

        return parent::close();
    }

    /**
     * Destroy a session.
     *
     * @see http://php.net/manual/en/sessionhandlerinterafce.destroy.php
     *
     * @param int $session_id the session ID being destroyed
     *
     * @return bool <p>
     *              The return value (usually TRUE on success, FALSE on failure).
     *              Note this value is returned internally to PHP for processing.
     *              </p>
     */
    public function destroy($session_id)
    {
        $event = new TPkgCmsEvent();
        $event
            ->SetSubject($this)
            ->SetContext('pkgCmsSession')
            ->SetName(__METHOD__)
            ->SetData(array('session_id' => $session_id));
        $this->notifyObservers($event);

        return parent::destroy($session_id);
    }

    /**
     * Cleanup old sessions.
     *
     * @see http://php.net/manual/en/sessionhandlerinterafce.gc.php
     *
     * @param int $maxlifetime <p>
     *                         Sessions that have not updated for
     *                         the last maxlifetime seconds will be removed.
     *                         </p>
     *
     * @return bool <p>
     *              The return value (usually TRUE on success, FALSE on failure).
     *              Note this value is returned internally to PHP for processing.
     *              </p>
     */
    public function gc($maxlifetime)
    {
        $event = new TPkgCmsEvent();
        $event
            ->SetSubject($this)
            ->SetContext('pkgCmsSession')
            ->SetName(__METHOD__)
            ->SetData(array('maxlifetime' => $maxlifetime));
        $this->notifyObservers($event);

        return parent::gc($maxlifetime);
    }

    /**
     * Initialize session.
     *
     * @see http://php.net/manual/en/sessionhandlerinterafce.open.php
     *
     * @param string $save_path  the path where to store/retrieve the session
     * @param string $session_id the session id
     *
     * @return bool <p>
     *              The return value (usually TRUE on success, FALSE on failure).
     *              Note this value is returned internally to PHP for processing.
     *              </p>
     */
    public function open($save_path, $session_id)
    {
        $event = new TPkgCmsEvent();
        $event
            ->SetSubject($this)
            ->SetContext('pkgCmsSession')
            ->SetName(__METHOD__)
            ->SetData(array('save_path' => $save_path, 'session_id' => $session_id));
        $this->notifyObservers($event);

        return parent::open($save_path, $session_id);
    }

    /**
     * Read session data.
     *
     * @see http://php.net/manual/en/sessionhandlerinterafce.read.php
     *
     * @param string $session_id the session id to read data for
     *
     * @return string <p>
     *                Returns an encoded string of the read data.
     *                If nothing was read, it must return an empty string.
     *                Note this value is returned internally to PHP for processing.
     *                </p>
     */
    public function read($session_id)
    {
        $event = new TPkgCmsEvent();
        $event
            ->SetSubject($this)
            ->SetContext('pkgCmsSession')
            ->SetName(__METHOD__)
            ->SetData(array('session_id' => $session_id));
        $this->notifyObservers($event);

        return parent::read($session_id);
    }

    /**
     * Write session data.
     *
     * @see http://php.net/manual/en/sessionhandlerinterafce.write.php
     *
     * @param string $session_id   the session id
     * @param string $session_data <p>
     *                             The encoded session data. This data is the
     *                             result of the PHP internally encoding
     *                             the $_SESSION superglobal to a serialized
     *                             string and passing it as this parameter.
     *                             Please note sessions use an alternative serialization method.
     *                             </p>
     *
     * @return bool <p>
     *              The return value (usually TRUE on success, FALSE on failure).
     *              Note this value is returned internally to PHP for processing.
     *              </p>
     */
    public function write($session_id, $session_data)
    {
        $event = new TPkgCmsEvent();
        $event
            ->SetSubject($this)
            ->SetContext('pkgCmsSession')
            ->SetName(__METHOD__)
            ->SetData(array('session_id' => $session_id, 'session_data' => $session_data));
        $this->notifyObservers($event);

        return parent::write($session_id, $session_data);
    }

    protected function notifyObservers(IPkgCmsEvent $event)
    {
        reset($this->observer);
        foreach (array_keys($this->observer) as $obIndex) {
            $this->observer[$obIndex]->PkgCmsEventNotify($event);
        }
        reset($this->observer);
    }

    public function registerObserver(IPkgCmsEventObserver $observer)
    {
        $this->observer[] = $observer;
    }

    public function unregisterObserver(IPkgCmsEventObserver $observer)
    {
        reset($this->observer);
        foreach (array_keys($this->observer) as $obIndex) {
            if ($this->observer[$obIndex] === $observer) {
                unset($this->observer[$obIndex]);
                break;
            }
        }
        reset($this->observer);
    }
}
