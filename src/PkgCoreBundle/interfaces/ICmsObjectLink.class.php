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
interface ICmsObjectLink
{
    /**
     * fetch the object record for given table name and id and return the link.
     *
     * @param string      $sTableName
     * @param string      $sId
     * @param bool        $bAbsolute  set to true to include the domain in the link
     * @param string|null $sAnchor
     *
     * @return string
     */
    public function getLink($sTableName, $sId, $bAbsolute = false, $sAnchor = null);
}
