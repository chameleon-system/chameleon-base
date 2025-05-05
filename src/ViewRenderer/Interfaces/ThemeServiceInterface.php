<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ViewRenderer\Interfaces;

interface ThemeServiceInterface
{
    /**
     * Gets the current theme.
     * This correctly considers the portal or backend but can also be overwritten by setting the theme beforehand - which is then always returned.
     */
    public function getTheme(?\TdbCmsPortal $portal): ?\TdbPkgCmsTheme;
}
