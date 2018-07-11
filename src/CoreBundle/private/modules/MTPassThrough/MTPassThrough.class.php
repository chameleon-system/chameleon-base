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
 * loads a file from workflow temp directory and outputs it based on content-type.
 *
/**/

/**
 * @deprecated since 6.2.0 - workflow is not supported anymore
 */
class MTPassThrough extends TCMSModelBase
{
    /**
     * {@inheritdoc}
     */
    public function &Execute()
    {
        parent::Execute();

        return $this->data;
    }

    /**
     * load image file from temp directory.
     *
     * @param string $sFilePath
     */
    protected function GetMediaFile($sFilePath)
    {
    }

    /**
     * load document file from temp directory.
     *
     * @param string $sFilePath
     */
    protected function GetDocumentFile($sFilePath)
    {
    }

    /**
     * returns TdbCmsFiletype based on filename and extension.
     *
     * @param string $sFilePath
     *
     * @return TdbCmsFiletype
     */
    protected function GetFileTypeObject($sFilePath)
    {
        return TdbCmsFiletype::GetNewInstance();
    }

    /**
     * passes file to browser.
     *
     * @param string $sFilePath
     * @param string $sContentType
     */
    protected function OutputFile($sFilePath, $sContentType)
    {
    }
}
