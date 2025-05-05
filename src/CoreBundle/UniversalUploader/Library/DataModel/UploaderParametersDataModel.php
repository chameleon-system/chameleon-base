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

use ChameleonSystem\CoreBundle\UniversalUploader\Exception\InvalidParameterValueException;

class UploaderParametersDataModel
{
    public const PARAMETER_VALUE_MODE_MEDIA = 'media';

    public const PARAMETER_VALUE_MODE_DOCUMENT = 'document';

    /**
     * @var bool
     */
    private $proportionExactMatch = false;

    /**
     * @var int|null
     */
    private $maxUploadHeight;

    /**
     * @var int|null
     */
    private $maxUploadWidth;

    /**
     * @var string
     */
    private $mode;

    /**
     * @var string|null
     */
    private $queueCompleteCallback;

    /**
     * @var string|null
     */
    private $recordID;

    /**
     * @var array|null
     */
    private $allowedFileTypes;

    /**
     * @var string
     */
    private $uploadDescription = '';

    /**
     * @var string
     */
    private $uploadName = '';

    /**
     * @var string|null
     */
    private $treeNodeID;

    /**
     * @var bool
     */
    private $singleMode = false;

    /**
     * @var bool
     */
    private $showMetaFields = true;

    /**
     * @var string|null
     */
    private $uploadSuccessCallback;

    /**
     * @return bool
     */
    public function isProportionExactMatch()
    {
        return $this->proportionExactMatch;
    }

    /**
     * @param bool $bProportionExactMatch
     *
     * @return void
     */
    public function setProportionExactMatch($bProportionExactMatch)
    {
        $this->proportionExactMatch = $bProportionExactMatch;
    }

    /**
     * @return int|null
     */
    public function getMaxUploadHeight()
    {
        return $this->maxUploadHeight;
    }

    /**
     * @param int $maxUploadHeight
     *
     * @return void
     *
     * @throws InvalidParameterValueException
     */
    public function setMaxUploadHeight($maxUploadHeight)
    {
        if (false === is_int($maxUploadHeight)) {
            throw new InvalidParameterValueException('Max upload height must be of type integer.');
        }
        if (0 >= $maxUploadHeight) {
            throw new InvalidParameterValueException('Max upload height cannot be 0 or below.');
        }
        $this->maxUploadHeight = $maxUploadHeight;
    }

    /**
     * @return int|null
     */
    public function getMaxUploadWidth()
    {
        return $this->maxUploadWidth;
    }

    /**
     * @param int $maxUploadWidth
     *
     * @return void
     *
     * @throws InvalidParameterValueException
     */
    public function setMaxUploadWidth($maxUploadWidth)
    {
        if (false === is_int($maxUploadWidth)) {
            throw new InvalidParameterValueException('Max upload width must be of type integer.');
        }
        if (0 >= $maxUploadWidth) {
            throw new InvalidParameterValueException('Max upload width cannot be 0 or below.');
        }
        $this->maxUploadWidth = $maxUploadWidth;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param string $mode
     *
     * @return void
     *
     * @throws InvalidParameterValueException
     */
    public function setMode($mode)
    {
        if (false === in_array($mode, [self::PARAMETER_VALUE_MODE_MEDIA, self::PARAMETER_VALUE_MODE_DOCUMENT])) {
            throw new InvalidParameterValueException('Invalid mode, see constants in UploaderParametersDataModel.');
        }
        $this->mode = $mode;
    }

    /**
     * @return string|null
     */
    public function getQueueCompleteCallback()
    {
        return $this->queueCompleteCallback;
    }

    /**
     * @param string $queueCompleteCallback
     *
     * @return void
     *
     * @throws InvalidParameterValueException
     */
    public function setQueueCompleteCallback($queueCompleteCallback)
    {
        if (false === is_string($queueCompleteCallback)) {
            throw new InvalidParameterValueException('Queue complete callback must be a string.');
        }

        $this->queueCompleteCallback = $queueCompleteCallback;
    }

    /**
     * @return string|null
     */
    public function getRecordID()
    {
        return $this->recordID;
    }

    /**
     * @param string|null $recordID
     *
     * @return void
     *
     * @throws InvalidParameterValueException
     */
    public function setRecordID($recordID)
    {
        if (false === is_string($recordID) && null !== $recordID) {
            throw new InvalidParameterValueException('Record ID must be string or null.');
        }
        $this->recordID = $recordID;
    }

    /**
     * @return array|null
     */
    public function getAllowedFileTypes()
    {
        return $this->allowedFileTypes;
    }

    /**
     * @param array|null $allowedFileTypes
     *
     * @return void
     *
     * @throws InvalidParameterValueException
     */
    public function setAllowedFileTypes($allowedFileTypes)
    {
        if (false === is_array($allowedFileTypes) && null !== $allowedFileTypes) {
            throw new InvalidParameterValueException('Allowed file types must be array or null.');
        }
        $this->allowedFileTypes = $allowedFileTypes;
    }

    /**
     * @return string
     */
    public function getUploadDescription()
    {
        return $this->uploadDescription;
    }

    /**
     * @param string $uploadDescription
     *
     * @return void
     *
     * @throws InvalidParameterValueException
     */
    public function setUploadDescription($uploadDescription)
    {
        if (false === is_string($uploadDescription)) {
            throw new InvalidParameterValueException('Upload description must be string.');
        }
        $this->uploadDescription = $uploadDescription;
    }

    /**
     * @return string
     */
    public function getUploadName()
    {
        return $this->uploadName;
    }

    /**
     * @param string $uploadName
     *
     * @return void
     *
     * @throws InvalidParameterValueException
     */
    public function setUploadName($uploadName)
    {
        if (false === is_string($uploadName)) {
            throw new InvalidParameterValueException('Upload name must be string.');
        }
        $this->uploadName = $uploadName;
    }

    /**
     * @return string|null
     */
    public function getTreeNodeID()
    {
        return $this->treeNodeID;
    }

    /**
     * @param string|null $treeNodeID
     *
     * @return void
     *
     * @throws InvalidParameterValueException
     */
    public function setTreeNodeID($treeNodeID)
    {
        if (false === is_string($treeNodeID) && null !== $treeNodeID) {
            throw new InvalidParameterValueException('tree node ID must be string or null.');
        }
        $this->treeNodeID = $treeNodeID;
    }

    /**
     * @return bool
     */
    public function isSingleMode()
    {
        return $this->singleMode;
    }

    /**
     * @param bool $singleMode
     *
     * @return void
     */
    public function setSingleMode($singleMode)
    {
        $this->singleMode = $singleMode;
    }

    /**
     * @return bool
     */
    public function isShowMetaFields()
    {
        return $this->showMetaFields;
    }

    /**
     * @param bool $showMetaFields
     *
     * @return void
     */
    public function setShowMetaFields($showMetaFields)
    {
        $this->showMetaFields = $showMetaFields;
    }

    /**
     * @return string|null
     */
    public function getUploadSuccessCallback()
    {
        return $this->uploadSuccessCallback;
    }

    /**
     * @param string|null $uploadSuccessCallback
     *
     * @return void
     *
     * @throws InvalidParameterValueException
     */
    public function setUploadSuccessCallback($uploadSuccessCallback)
    {
        if (false === is_string($uploadSuccessCallback) && null !== $uploadSuccessCallback) {
            throw new InvalidParameterValueException('Queue complete callback must be string or null.');
        }
        $this->uploadSuccessCallback = $uploadSuccessCallback;
    }

    /**
     * @param string[] $excludeParameters
     *
     * @return array<string, int|string>
     */
    public function getAsArray($excludeParameters = [])
    {
        $parameterArray = [];

        $parameterArray['sAllowedFileTypes'] = implode(',', $this->getAllowedFileTypes());
        $parameterArray['recordID'] = $this->getRecordID();
        $parameterArray['treeNodeID'] = $this->getTreeNodeID();
        $parameterArray['iMaxUploadHeight'] = $this->getMaxUploadHeight();
        $parameterArray['iMaxUploadWidth'] = $this->getMaxUploadWidth();
        $parameterArray['mode'] = $this->getMode();
        $parameterArray['queueCompleteCallback'] = $this->getQueueCompleteCallback();
        $parameterArray['sUploadDescription'] = $this->getUploadDescription();
        $parameterArray['sUploadName'] = $this->getUploadName();
        $parameterArray['callback'] = $this->getQueueCompleteCallback();

        if ($this->isProportionExactMatch()) {
            $parameterArray['bProportionExactMatch'] = '1';
        } else {
            $parameterArray['bProportionExactMatch'] = '0';
        }

        if ($this->isSingleMode()) {
            $parameterArray['singleMode'] = '1';
        } else {
            $parameterArray['singleMode'] = '0';
        }

        if ($this->isShowMetaFields()) {
            $parameterArray['showMetaFields'] = '1';
        } else {
            $parameterArray['showMetaFields'] = '0';
        }

        $parameterArray = array_filter($parameterArray, function ($value) {return null !== $value; }); // remove null-values

        foreach ($excludeParameters as $excludedParameter) {
            if (isset($parameterArray[$excludedParameter])) {
                unset($parameterArray[$excludedParameter]);
            }
        }

        return $parameterArray;
    }

    /**
     * Check the inner validity of all the parameters currently set and throw exception if invalid.
     *
     * @return void
     *
     * @throws InvalidParameterValueException
     */
    public function validate()
    {
        if ($this->isProportionExactMatch() && ($this->getMaxUploadHeight() <= 0 || $this->getMaxUploadWidth() <= 0)) {
            throw new InvalidParameterValueException('if exact proportions should be used, max width and height must be greater than 0.');
        }
    }
}
