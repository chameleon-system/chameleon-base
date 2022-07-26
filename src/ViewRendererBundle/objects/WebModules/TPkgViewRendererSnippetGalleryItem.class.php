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
    /**
     * @var string|null
     */
    public $sType = null;
    /**
     * @var string|null
     */
    public $sRelativePath = null;
    /**
     * @var string|null
     */
    public $sSnippetName = null;
    /**
     * @var string|null
     */
    public $sFullDummyDataPath = null;
    /**
     * @var string|null
     */
    public $sDummyDataFileName = null;
    /**
     * @var TPkgViewRendererSnippetDummyData|null
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
