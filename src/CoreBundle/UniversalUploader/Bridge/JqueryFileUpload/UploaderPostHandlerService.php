<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\UniversalUploader\Bridge\JqueryFileUpload;

use ChameleonSystem\CoreBundle\i18n\TranslationConstants;
use ChameleonSystem\CoreBundle\UniversalUploader\Bridge\JqueryFileUpload\DataModel\File;
use ChameleonSystem\CoreBundle\UniversalUploader\Exception\UploaderPostDataException;
use ChameleonSystem\CoreBundle\UniversalUploader\Library\DataModel\UploadedFileDataModel;
use ChameleonSystem\CoreBundle\UniversalUploader\Library\UploaderConfigurationInterface;
use ChameleonSystem\CoreBundle\UniversalUploader\Library\UploaderPostHandlerServiceInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

class UploaderPostHandlerService implements UploaderPostHandlerServiceInterface
{
    /**
     * @var RequestStack|null
     */
    private $requestStack;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(RequestStack $requestStack, TranslatorInterface $translator)
    {
        $this->requestStack = $requestStack;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function post(UploaderConfigurationInterface $uploaderConfiguration)
    {
        $uploadedFiles = [];
        $postFiles = $this->getUploadedFile('files');
        if ($postFiles) {
            foreach ($postFiles as $uploadedFile) {
                $uploadedFiles[] = $this->handleFileUpload($uploadedFile, $uploaderConfiguration);
            }
        }

        return $uploadedFiles;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse($uploadedFiles)
    {
        $filesResponse = [];
        foreach ($uploadedFiles as $uploadedFileModel) {
            $file = new File();
            $file->name = $uploadedFileModel->getOriginalClientFileName();
            $file->size = $uploadedFileModel->getFileSize();
            $file->type = $uploadedFileModel->getMimeType();
            $file->recordId = $uploadedFileModel->getSavedRecordId();
            $errorString = '';

            if ($uploadedFileModel->hasError()) {
                $errors = $uploadedFileModel->getErrorMessages();
                foreach ($errors as $error) {
                    $errorString .= $this->translator->trans($error['message'], $error['context'], TranslationConstants::DOMAIN_BACKEND);
                }
                $file->error = $errorString;
            }

            $filesResponse[] = $file;
        }

        return new JsonResponse(['files' => $filesResponse]);
    }

    /**
     * Parse the Content-Range header, which has the following form:
     * Content-Range: bytes 0-524287/2000000.
     *
     * @return array|null
     */
    private function getContentRange()
    {
        $request = $this->requestStack->getCurrentRequest();
        $contentRangeHeader = $request->server->get('HTTP_CONTENT_RANGE', null);
        if (1 !== preg_match('/(\d+)-(\d+)\/(\d+)/', $contentRangeHeader, $contentRange)) {
            return null;
        }
        $mappedContentRange = [];
        $mappedContentRange['start'] = (int) $contentRange[1];
        $mappedContentRange['end'] = (int) $contentRange[2];
        $mappedContentRange['total'] = (int) $contentRange[3];

        return $mappedContentRange;
    }

    /**
     * Take a file upload, copy to tmp directory and provide a UploadedFileDataModel for further processing.
     *
     * @return UploadedFileDataModel
     */
    private function handleFileUpload(UploadedFile $uploadedFile, UploaderConfigurationInterface $uploaderConfiguration)
    {
        $uploadedFileModel = new UploadedFileDataModel();
        $uploadedFileModel->setOriginalClientFileName($this->getClientOriginalName($uploadedFile));
        $uploadedFileModel->setMimeType($uploadedFile->getClientMimeType());

        if (UPLOAD_ERR_OK !== $uploadedFile->getError()) { // we cannot use isValid() because it checks for is_uploaded_file
            $uploadedFileModel->addErrorMessage($uploadedFile->getErrorMessage(), $uploadedFile->getError());

            return $uploadedFileModel;
        }

        $tmpDirPath = $uploaderConfiguration->getUploadTmpDir();
        $tmpFileName = $this->getUniqueFileNameForUpload($uploadedFile);
        $tmpFilePath = $tmpDirPath.$tmpFileName;
        $contentRange = $this->getContentRange();
        $appendToExistingFile = $this->isChunkFile($contentRange, $tmpFilePath);
        if (is_uploaded_file($uploadedFile->getPathname())) {
            // multipart/formdata uploads (POST method uploads)
            if (true === $appendToExistingFile) {
                file_put_contents(
                    $tmpFilePath,
                    fopen($uploadedFile->getPathname(), 'r'),
                    FILE_APPEND
                );
            } else {
                $uploadedFile->move($tmpDirPath, $tmpFilePath);
            }
        } else {
            // Non-multipart uploads (PUT method support)
            file_put_contents(
                $tmpFilePath,
                fopen('php://input', 'r'),
                $appendToExistingFile ? FILE_APPEND : 0
            );
        }

        $uploadedFileModel->setTmpFilePath($tmpFilePath);
        $uploadedFileModel->setFileSize($this->getFileSize($tmpFilePath, $appendToExistingFile));
        $uploadedFileModel->setUploadFinished($this->isUploadFinished($contentRange));

        return $uploadedFileModel;
    }

    /**
     * @param string $tmpFilePath
     *
     * @return bool
     */
    private function isChunkFile(?array $contentRange, $tmpFilePath)
    {
        if (null === $contentRange) {
            return false;
        }
        if (($contentRange['start'] > 0 && true === is_file($tmpFilePath))
            || 0 === $contentRange['start'] && false === $this->isUploadFinished($contentRange)
        ) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    private function isUploadFinished(?array $contentRange = null)
    {
        if (null === $contentRange) {
            return true;
        }
        if ($contentRange['end'] + 1 === $contentRange['total']) {
            return true;
        }

        return false;
    }

    /**
     * @param string $filePath
     * @param bool $clearStatCache - stat cache needs to be cleared after we write into the file, so file size gets updated
     *
     * @return int
     */
    private function getFileSize($filePath, $clearStatCache = false)
    {
        if (true === $clearStatCache) {
            if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
                clearstatcache(true, $filePath);
            } else {
                clearstatcache();
            }
        }

        return filesize($filePath);
    }

    /**
     * Returns the filename, the file had on the client filesystem.
     *
     * @return mixed|string|null
     */
    private function getClientOriginalName(UploadedFile $uploadedFile)
    {
        $fileName = $uploadedFile->getClientOriginalName();

        $fileName = trim(basename(stripslashes($fileName)), ".\x00..\x20");
        // use a timestamp for empty file names:
        if ('' === $fileName) {
            $fileName = str_replace('.', '-', (string) microtime(true));
        }

        return $fileName;
    }

    /**
     * @return string
     */
    private function getUniqueFileNameForUpload(UploadedFile $uploadedFile)
    {
        return 'cmsupld'.md5($this->getClientOriginalName($uploadedFile));
    }

    /**
     * @param string $fieldName
     *
     * @return array|null
     *
     * @throws UploaderPostDataException
     */
    private function getUploadedFile($fieldName)
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            throw new UploaderPostDataException('No request set.');
        }

        return $request->files->get($fieldName, null);
    }
}
