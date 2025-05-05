<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Service;

/**
 * ActivePageServiceInterface defines a service that encapsulates getting and setting of the currently active page.
 */
interface ActivePageServiceInterface
{
    /**
     * @param bool $reload
     *
     * @return \TCMSActivePage|null
     */
    public function getActivePage($reload = false);

    /**
     * @param string $activePageId
     * @param string $referrerPageId
     *
     * @return void
     */
    public function setActivePage($activePageId, $referrerPageId);

    /**
     * Returns a link to the currently active page. Note that this link might be absolute if it requires HTTPS access.
     *
     * @param array $additionalParameters see $parameters attribute in PageServiceInterface::getLinkToActivePageRelative()
     * @param array $excludeParameters parameters that should be excluded from the URL generation
     *
     * @return string
     */
    public function getLinkToActivePageRelative(array $additionalParameters = [], array $excludeParameters = [], ?\TdbCmsLanguage $language = null);

    /**
     * Returns the link to the currently active page. Note that this link might be absolute if it requires HTTPS access.
     *
     * @param array $additionalParameters see $parameters attribute in PageServiceInterface::getLinkToActivePageAbsolute()
     * @param array $excludeParameters parameters that should be excluded from the URL generation
     * @param bool|false $forceSecure
     *
     * @return string
     */
    public function getLinkToActivePageAbsolute(array $additionalParameters = [], array $excludeParameters = [], ?\TdbCmsLanguage $language = null, $forceSecure = false);
}
