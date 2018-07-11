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

use ChameleonSystem\CoreBundle\UniversalUploader\Exception\InvalidParameterValueException;
use ChameleonSystem\CoreBundle\UniversalUploader\Library\DataModel\UploaderParametersDataModel;

/**
 * The uploader can be controlled with certain parameters, read them and map them to the data model.
 *
 * Interface UploaderParameterInterface
 */
interface UploaderParameterServiceInterface
{
    /**
     * Get the mapped data model.
     *
     * @return UploaderParametersDataModel
     *
     * @throws InvalidParameterValueException
     */
    public function getParameters();
}
