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
     * @var string|null
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
    private $errorMessages = [];

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
     *
     * @return void
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
     *
     * @return void
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
     *
     * @return void
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
     *
     * @return void
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
     * @param int $code
     * @param array $context
     *
     * @return void
     */
    public function addErrorMessage($errorMessage, $code = 0, $context = [])
    {
        $this->hasError = true;
        $this->errorMessages[] = ['code' => $code, 'message' => $errorMessage, 'context' => $context];
    }

    /**
     * @return bool
     */
    public function hasError()
    {
        return $this->hasError;
    }

    /**
     * @return string|null
     */
    public function getSavedRecordId()
    {
        return $this->savedRecordId;
    }

    /**
     * @param string|null $savedRecordId
     *
     * @return void
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
     *
     * @return void
     */
    public function setUploadFinished($uploadFinished)
    {
        $this->uploadFinished = $uploadFinished;
    }
}
