<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Util;

use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * PageServiceUtilInterface is a utility containing helper methods only meant to be used by the PageService.
 */
interface PageServiceUtilInterface
{
    /**
     * Returns the URL path for the given page.
     *
     * @return string
     *
     * @throws RouteNotFoundException
     */
    public function getPagePath(\TdbCmsTplPage $page, \TdbCmsLanguage $language);

    /**
     * Adds things like trailing slashes and HTTPS usage to URLs.
     *
     * @param string $url
     * @param bool $forceSecure
     *
     * @return string
     */
    public function postProcessUrl($url, \TdbCmsPortal $portal, \TdbCmsLanguage $language, $forceSecure);
}
