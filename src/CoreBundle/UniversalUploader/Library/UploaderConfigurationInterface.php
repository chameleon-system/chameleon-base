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

/**
 * Configuration for universal uploader.
 *
 * Interface ConfigurationInterface
 */
interface UploaderConfigurationInterface
{
    /**
     * Get the max upload size in kilobytes.
     *
     * @param UploaderParametersDataModel $uploaderParameter
     *
     * @return int
     */
    public function getMaxUploadSize($uploaderParameter);

    /**
     * Get chunk size in kilobytes.
     *
     * @return int
     */
    public function getChunkSize();

    /**
     * @param UploaderParametersDataModel $uploaderParameter
     *
     * @return array
     */
    public function getAllowedFileTypes($uploaderParameter);

    /**
     * @return string
     */
    public function getUploadTmpDir();
}
