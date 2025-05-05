<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

interface ICmsCoreRedirect
{
    /**
     * Redirects to another url sending the status code. If a relative URL is passed, it will be expanded to the current domain.
     *
     * @param string $url
     * @param int $status
     * @param bool $allowOnlyInternalUrls
     *
     * @return never-returns
     */
    public function redirect($url, $status = 302, $allowOnlyInternalUrls = false);

    /**
     * Returns true if the URL passed is an URL on the active domain.
     *
     * @param string $url
     *
     * @return bool
     */
    public function isInternalURL($url);

    /**
     * Redirects to the current page - replacing the query string by one generated based on the parameters passed.
     *
     * @param array|string|null $queryStringParameters
     *
     * @return never-returns
     */
    public function redirectToActivePage($queryStringParameters = null);
}
