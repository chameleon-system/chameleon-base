<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Request;

/**
 * manages the session handing.
 *
 * @deprecated - use session in request object instead
/**/
class TCMSSessionHandler
{
    public static $bSessionOpen = false;
    public static $sSessionId = null;

    public static function Start($force = false, $sSessionId = null)
    {
    }

    /**
     * resets the session to an empty array but preserves all variables from
     * $aProtectedVariables and moves them to the new session.
     *
     * @param array $aProtectedVariables
     */
    public static function CleanUpSession($aProtectedVariables = array())
    {
        $request = self::getRequest();
        /** @var TPKgCmsSession $session */
        $session = $request->getSession();

        $all = $session->all();
        $new = array();
        foreach ($aProtectedVariables as $key) {
            if (isset($all[$key])) {
                $new[$key] = $all[$key];
            }
        }
        $_SESSION = array();
        $session->replace($new);
    }

    /**
     * force empty session - the session is COMPLETELY DESTROYED!
     */
    public static function ClearSession()
    {
        $request = self::getRequest();
        /** @var TPKgCmsSession $session */
        $session = $request->getSession();
        $session->clear();
    }

    /**
     * regenerate session id - see http://anvilstudios.co.za/blog/php/session-cookies-faulty-in-ie8/ on why we need it.
     *
     * @deprecated since 6.2.0 - no longer used due to browser incompatibility problems.
     */
    public static function RegenerateId()
    {
        // disabled because of problems with IE (doubled cookie)
    }

    /**
     * @return Request
     */
    private static function getRequest()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('request_stack')->getCurrentRequest();
    }
}
