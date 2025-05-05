<?php

namespace ChameleonSystem\CoreBundle\RequestState\Interfaces;

use Symfony\Component\HttpFoundation\Request;

/**
 * Provides a hash for the request state not expressed via the request url.
 */
interface RequestStateHashProviderInterface
{
    /**
     * Returns a hash identifying the current state independent of the URL.
     * Will return null if there is no request or if getHash is called from within getHash.
     *
     * Will use the request passed, or the active request if null is passed.
     *
     * @return string|null
     */
    public function getHash(?Request $request = null);
}
