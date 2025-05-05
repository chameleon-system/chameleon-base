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
    public $sType;
    /**
     * @var string|null
     */
    public $sRelativePath;
    /**
     * @var string|null
     */
    public $sSnippetName;
    /**
     * @var string|null
     */
    public $sFullDummyDataPath;
    /**
     * @var string|null
     */
    public $sDummyDataFileName;
    /**
     * @var TPkgViewRendererSnippetDummyData|null
     */
    public $oDummyData;

    /**
     * @return array
     */
    public function getDummyData()
    {
        $aDummyData = [];
        if (null !== $this->oDummyData) {
            $aDummyData = $this->oDummyData->getDummyData();
        }

        return $aDummyData;
    }
}
