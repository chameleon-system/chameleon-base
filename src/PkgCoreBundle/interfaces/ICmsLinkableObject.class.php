<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

interface ICmsLinkableObject
{
    /**
     * @param bool                $bAbsolute           set to true to include the domain in the link
     * @param null|string         $sAnchor
     * @param array               $aOptionalParameters
     * @param TdbCmsPortal|null   $portal
     * @param TdbCmsLanguage|null $language
     *
     * @return string
     */
    public function getLink($bAbsolute = false, $sAnchor = null, $aOptionalParameters = array(), \TdbCmsPortal $portal = null, \TdbCmsLanguage $language = null);
}
