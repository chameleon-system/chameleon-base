<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSTag extends TAdbCmsTags
{
    /**
     * return link to a list of articles of this tag.
     *
     * @return string
     */
    public function GetTagArticlesLink()
    {
        $sURLName = $this->sqlData['urlname'];
        if (empty($sURLName)) {
            $sURLName = $this->sqlData['name'];
        }
        $sSearchLink = self::getPageService()->getLinkToPortalHomePageRelative().'/tag/'.urlencode($sURLName); // error?!?

        $sSearchLink = str_replace('//', '/', $sSearchLink);

        return $sSearchLink;
    }
}
