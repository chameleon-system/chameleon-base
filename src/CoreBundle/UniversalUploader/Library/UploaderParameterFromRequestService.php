<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\UniversalUploader\Library;

use ChameleonSystem\CoreBundle\UniversalUploader\Library\DataModel\UploaderParametersDataModel;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;

class UploaderParameterFromRequestService implements UploaderParameterServiceInterface
{
    /**
     * @var InputFilterUtilInterface
     */
    private $inputFilterUtil;

    public function __construct(InputFilterUtilInterface $inputFilterUtil)
    {
        $this->inputFilterUtil = $inputFilterUtil;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        $parameters = new UploaderParametersDataModel();

        $allowedFileTypesString = $this->inputFilterUtil->getFilteredInput('sAllowedFileTypes');
        if (null !== $allowedFileTypesString) {
            $allowedFileTypes = explode(',', $allowedFileTypesString);
            if (\count($allowedFileTypes) > 0) {
                $parameters->setAllowedFileTypes($allowedFileTypes);
            }
        }

        $proportionsExactMatch = $this->inputFilterUtil->getFilteredInput('bProportionExactMatch');
        if (null !== $proportionsExactMatch) {
            if ($proportionsExactMatch) {
                $parameters->setProportionExactMatch(true);
            } else {
                $parameters->setProportionExactMatch(false);
            }
        }

        $maxUploadHeight = $this->inputFilterUtil->getFilteredInput('iMaxUploadHeight');
        if (null !== $maxUploadHeight) {
            $parameters->setMaxUploadHeight((int) $maxUploadHeight);
        }

        $maxUploadWidth = $this->inputFilterUtil->getFilteredInput('iMaxUploadWidth');
        if (null !== $maxUploadWidth) {
            $parameters->setMaxUploadWidth((int) $maxUploadWidth);
        }

        $mode = $this->inputFilterUtil->getFilteredInput('mode');
        if (null !== $mode) {
            $parameters->setMode($mode);
        }

        $queueCompleteCallback = $this->inputFilterUtil->getFilteredInput('queueCompleteCallback');
        if (null !== $queueCompleteCallback) {
            $parameters->setQueueCompleteCallback('parent.'.$queueCompleteCallback);
        }

        $recordID = $this->inputFilterUtil->getFilteredInput('recordID');
        if (null !== $recordID) {
            $parameters->setRecordID($recordID);
        }

        $uploadDescription = $this->inputFilterUtil->getFilteredInput('sUploadDescription');
        if (null !== $uploadDescription) {
            $parameters->setUploadDescription($uploadDescription);
        }

        $uploadName = $this->inputFilterUtil->getFilteredInput('sUploadName');
        if (null !== $uploadName) {
            $parameters->setUploadName($uploadName);
        }

        $treeNodeID = $this->inputFilterUtil->getFilteredInput('treeNodeID');
        if (null !== $treeNodeID) {
            $parameters->setTreeNodeID($treeNodeID);
        }

        $callback = $this->inputFilterUtil->getFilteredInput('callback');
        $parentIFrame = $this->inputFilterUtil->getFilteredInput('parentIFrame');
        $parentIsInModal = $this->inputFilterUtil->getFilteredInput('parentIsInModal', '');

        if (null !== $callback) {
            if (null !== $parentIFrame && '' !== $parentIFrame && '' === $parentIsInModal) {
                $parameters->setUploadSuccessCallback("$(parent.document.getElementById('".$parentIFrame."')).prop('contentWindow').".$callback);
            } else {
                $parameters->setUploadSuccessCallback('parent.'.$callback);
            }
        }

        $singleMode = $this->inputFilterUtil->getFilteredInput('singleMode');
        if (null !== $singleMode) {
            if ($singleMode) {
                $parameters->setSingleMode(true);
            } else {
                $parameters->setSingleMode(false);
            }
        }

        $showMetaFields = $this->inputFilterUtil->getFilteredInput('showMetaFields');
        if (null !== $showMetaFields) {
            if ($showMetaFields) {
                $parameters->setShowMetaFields(true);
            } else {
                $parameters->setShowMetaFields(false);
            }
        }

        return $parameters;
    }
}
