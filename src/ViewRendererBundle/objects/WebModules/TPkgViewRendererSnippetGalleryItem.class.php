<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgViewRendererSnippetGalleryItem
{
    public $sType = null;
    public $sRelativePath = null;
    public $sSnippetName = null;
    public $sFullDummyDataPath = null;
    public $sDummyDataFileName = null;
    /**
     * @var null|TPkgViewRendererSnippetDummyData
     */
    public $oDummyData = null;

    /**
     * @return array
     */
    public function getDummyData()
    {
        $aDummyData = array();
        if (null !== $this->oDummyData) {
            $aDummyData = $this->oDummyData->getDummyData();
        }

        return $aDummyData;
    }
}
