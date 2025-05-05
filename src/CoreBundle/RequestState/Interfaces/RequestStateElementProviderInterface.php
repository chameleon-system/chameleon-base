<?php

namespace ChameleonSystem\CoreBundle\RequestState\Interfaces;

use Symfony\Component\HttpFoundation\Request;

/**
 * Provides a set of key/values based on the request which define the state of the page and are not part of the
 * request URI.
 *
 * Examples: Current language (if not part of the URL), active currency, user is logged in, active user groups, etc)
 */
interface RequestStateElementProviderInterface
{
    /**
     * Returns an associative array of state relevant request based data.
     *
     * @return array<string, mixed>
     */
    public function getStateElements(Request $request);

    /**
     * Returns a list of events that trigger a reset of the current state.
     *
     * @return string[]
     */
    public static function getResetStateEvents();
}
