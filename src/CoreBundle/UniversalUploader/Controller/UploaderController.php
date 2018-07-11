<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\UniversalUploader\Controller;

use ChameleonSystem\CoreBundle\UniversalUploader\Bridge\Chameleon\SaveToMediaLibraryServiceInterface;
use ChameleonSystem\CoreBundle\UniversalUploader\Bridge\Chameleon\UploaderConfiguration;
use ChameleonSystem\CoreBundle\UniversalUploader\Library\DataModel\UploadedFileDataModel;
use ChameleonSystem\CoreBundle\UniversalUploader\Library\UploaderPostHandlerServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use TdbCmsConfig;

class UploaderController
{
    /**
     * @var UploaderPostHandlerServiceInterface
     */
    private $postHandler;

    /**
     * @var SaveToMediaLibraryServiceInterface
     */
    private $saveToMediaLibraryService;

    /**
     * @param UploaderPostHandlerServiceInterface $postHandlerService
     * @param SaveToMediaLibraryServiceInterface  $saveToMediaLibraryService
     */
    public function __construct(UploaderPostHandlerServiceInterface $postHandlerService, SaveToMediaLibraryServiceInterface $saveToMediaLibraryService)
    {
        $this->postHandler = $postHandlerService;
        $this->saveToMediaLibraryService = $saveToMediaLibraryService;
    }

    /**
     * Call the service to handle posted data/files, save the resulting UploadedFileDataModel in media library and get
     * the response from the service.
     *
     * @return Response
     */
    public function __invoke()
    {
        $configuration = $this->getUploaderConfiguration();

        $uploadedFiles = $this->postHandler->post($configuration);

        foreach ($uploadedFiles as $key => $uploadedFile) {
            /**
             * @var UploadedFileDataModel $uploadedFile
             */
            if (false === $uploadedFile->isUploadFinished()) {
                continue;
            }
            $uploadedFiles[$key] = $this->saveToMediaLibraryService->saveUploadedFile($uploadedFile);
        }

        return $this->postHandler->getResponse($uploadedFiles);
    }

    /**
     * @return UploaderConfiguration
     */
    private function getUploaderConfiguration()
    {
        return new UploaderConfiguration(TdbCmsConfig::GetInstance());
    }
}
