<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\MediaManager\DataModel;

class MediaItemUsageDataModel
{
    /**
     * @var string
     */
    private $mediaItemId;

    /**
     * @var string
     */
    private $targetTableName;

    /**
     * @var string
     */
    private $targetRecordId;

    /**
     * @var string
     */
    private $targetFieldName;

    /**
     * @var string|null
     */
    private $targetTableDescriptiveName;

    /**
     * @var string|null
     */
    private $targetFieldDescriptiveName;

    /**
     * @var string|null
     */
    private $targetRecordName;

    /**
     * @var string|null
     */
    private $url;

    /**
     * @var string|null
     */
    private $cropId;

    /**
     * @param string $mediaItemId
     * @param string $targetTableName
     * @param string $targetRecordId
     */
    public function __construct($mediaItemId, $targetTableName, $targetRecordId)
    {
        $this->mediaItemId = $mediaItemId;
        $this->targetTableName = $targetTableName;
        $this->targetRecordId = $targetRecordId;
    }

    /**
     * @return string
     */
    public function getMediaItemId()
    {
        return $this->mediaItemId;
    }

    /**
     * @return string
     */
    public function getTargetTableName()
    {
        return $this->targetTableName;
    }

    /**
     * @return string
     */
    public function getTargetRecordId()
    {
        return $this->targetRecordId;
    }

    /**
     * @return string
     */
    public function getTargetFieldName()
    {
        return $this->targetFieldName;
    }

    /**
     * @param string $targetFieldName
     */
    public function setTargetFieldName($targetFieldName)
    {
        $this->targetFieldName = $targetFieldName;
    }

    /**
     * @return string|null
     */
    public function getTargetTableDescriptiveName()
    {
        return $this->targetTableDescriptiveName;
    }

    /**
     * @param string|null $targetTableDescriptiveName
     */
    public function setTargetTableDescriptiveName($targetTableDescriptiveName)
    {
        $this->targetTableDescriptiveName = $targetTableDescriptiveName;
    }

    /**
     * @return string|null
     */
    public function getTargetFieldDescriptiveName()
    {
        return $this->targetFieldDescriptiveName;
    }

    /**
     * @param string|null $targetFieldDescriptiveName
     */
    public function setTargetFieldDescriptiveName($targetFieldDescriptiveName)
    {
        $this->targetFieldDescriptiveName = $targetFieldDescriptiveName;
    }

    /**
     * @return string|null
     */
    public function getTargetRecordName()
    {
        return $this->targetRecordName;
    }

    /**
     * @param string|null $targetRecordName
     */
    public function setTargetRecordName($targetRecordName)
    {
        $this->targetRecordName = $targetRecordName;
    }

    /**
     * @return string|null
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string|null $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return string|null
     */
    public function getCropId()
    {
        return $this->cropId;
    }

    /**
     * @param string|null $cropId
     */
    public function setCropId($cropId)
    {
        $this->cropId = $cropId;
    }
}
