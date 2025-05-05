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

interface CmsConfigDataAccessInterface
{
    /**
     * Determines the backend theme.
     *
     * Errors here are ignored and null is returned.
     *
     * This methods is necessary as TdbCmsConfig::GetFieldPkgCmsTheme() cannot be used directly as the field might not exist
     * yet during the first execution and rendering of updates.
     */
    public function getBackendTheme(): ?\TdbPkgCmsTheme;
}
