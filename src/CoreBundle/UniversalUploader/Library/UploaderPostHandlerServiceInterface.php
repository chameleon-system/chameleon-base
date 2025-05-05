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

use ChameleonSystem\CoreBundle\UniversalUploader\Exception\UploaderPostDataException;
use ChameleonSystem\CoreBundle\UniversalUploader\Library\DataModel\UploadedFileDataModel;
use Symfony\Component\HttpFoundation\Response;

/**
 * Handles file upload on server side and provides UploadedFileDataModel
 * Don't save the file in Chameleon, this is done by the controller via SaveToMediaLibraryServiceInterface.
 */
interface UploaderPostHandlerServiceInterface
{
    /**
     * handles file upload and accompanying form fields via post.
     *
     * @return UploadedFileDataModel[]
     *
     * @throws UploaderPostDataException
     */
    public function post(UploaderConfigurationInterface $uploaderConfiguration);

    /**
     * Return a response your uploader component can handle (some kind of JSON Response in most cases).
     *
     * @param UploadedFileDataModel[] $uploadedFiles
     *
     * @return Response
     */
    public function getResponse($uploadedFiles);
}
