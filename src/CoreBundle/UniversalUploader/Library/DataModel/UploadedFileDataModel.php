<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\UniversalUploader\Library\DataModel;

class UploadedFileDataModel
{
    /**
     * @var null|string
     */
    private $savedRecordId;

    /**
     * real path to temporary file.
     *
     * @var string
     */
    private $tmpFilePath;

    /**
     * file name as on client filesystem.
     *
     * @var string
     */
    private $originalClientFileName;

    /**
     * file size in bytes.
     *
     * @var int
     */
    private $fileSize;

    /**
     * @var string
     */
    private $mimeType;

    /**
     * @var array
     */
    private $errorMessages = array();

    /**
     * @var bool
     */
    private $hasError = false;

    /**
     * @var bool
     */
    private $uploadFinished = false;

    /**
     * @return string
     */
    public function getTmpFilePath()
    {
        return $this->tmpFilePath;
    }

    /**
     * @param string $tmpFilePath
     */
    public function setTmpFilePath($tmpFilePath)
    {
        $this->tmpFilePath = $tmpFilePath;
    }

    /**
     * @return string
     */
    public function getOriginalClientFileName()
    {
        return $this->originalClientFileName;
    }

    /**
     * @param string $originalClientFileName
     */
    public function setOriginalClientFileName($originalClientFileName)
    {
        $this->originalClientFileName = $originalClientFileName;
    }

    /**
     * @return int
     */
    public function getFileSize()
    {
        return $this->fileSize;
    }

    /**
     * @param int $fileSize
     */
    public function setFileSize($fileSize)
    {
        $this->fileSize = $fileSize;
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @param string $mimeType
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
    }

    /**
     * @return array
     */
    public function getErrorMessages()
    {
        return $this->errorMessages;
    }

    /**
     * @param string $errorMessage
     * @param int    $code
     * @param array  $context
     */
    public function addErrorMessage($errorMessage, $code = 0, $context = array())
    {
        $this->hasError = true;
        $this->errorMessages[] = array('code' => $code, 'message' => $errorMessage, 'context' => $context);
    }

    /**
     * @return bool
     */
    public function hasError()
    {
        return $this->hasError;
    }

    /**
     * @return null|string
     */
    public function getSavedRecordId()
    {
        return $this->savedRecordId;
    }

    /**
     * @param null|string $savedRecordId
     */
    public function setSavedRecordId($savedRecordId)
    {
        $this->savedRecordId = $savedRecordId;
    }

    /**
     * @return bool
     */
    public function isUploadFinished()
    {
        return $this->uploadFinished;
    }

    /**
     * @param bool $uploadFinished
     */
    public function setUploadFinished($uploadFinished)
    {
        $this->uploadFinished = $uploadFinished;
    }
}
