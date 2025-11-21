<?php

namespace ChameleonSystem\NewsletterBundle\Service;

use ChameleonSystem\CmsBackendBundle\BackendSession\BackendSessionInterface;
use ChameleonSystem\ImageCrop\Interfaces\CmsMediaDataAccessInterface;
use ChameleonSystem\NewsletterBundle\Interfaces\NewsletterImportServiceInterface;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class NewsletterImportService implements NewsletterImportServiceInterface
{
    private const string NEWSLETTER_IMPORTE_MEDIA_TREE_ID = '2bf85916-fc60-6f9c-985d-1ac597cbb767';

    public function __construct
    (
        private readonly RequestStack $requestStack,
        private readonly LoggerInterface $logger,
        private readonly Connection $connection,
        private readonly CmsMediaDataAccessInterface $cmsMediaDataAccess,
        private readonly BackendSessionInterface $backendSession
    )
    {
    }

    public function importZipFile(string $recordId): void
    {
        $currentRequest = $this->requestStack->getCurrentRequest();

        if (null === $currentRequest) {
            return;
        }

        $zip = $currentRequest->files->get('zipFile');

        if (!$zip || !$zip->isValid()) {
            return;
        }

        $zipName = $this->sanitizeZipName($zip->getClientOriginalName());

        $baseTmpDir = PATH_CMS_CUSTOMER_DATA.'newsletter';
        $targetDir = $baseTmpDir.'/newsletter_import_'.$zipName.'_'.date('d_m_Y');

        $zipPath = $this->moveZipToTempDirectory($zip, $targetDir);

        $this->extractZipFileInDirectory($zipPath, $targetDir);

        $this->uploadImagesToCms($targetDir);

        $this->importHtmlFromZipToNewsletterCampaign($targetDir, $recordId);
        $this->importTxtFromZipToNewsletterCampaign($targetDir, $recordId);
    }

    private function sanitizeZipName(string $filename): string
    {
        $name = preg_replace('/\.zip$/i', '', $filename);
        $name = preg_replace('/[^a-zA-Z0-9]+/', '_', $name);
        $name = strtolower(trim($name, '_'));

        return $name;
    }

    private function moveZipToTempDirectory($zip, string $targetDir): string
    {
        if (!is_dir($targetDir) && !mkdir($targetDir, 0777, true) && !is_dir($targetDir)) {
            return 'Could not create target directory: '.$targetDir;
        }

        $zipPath = $targetDir.'/'.$zip->getClientOriginalName();
        $zip->move($targetDir, $zip->getClientOriginalName());

        return $zipPath;
    }

    private function extractZipFileInDirectory(string $zipPath, string $targetDir)
    {
        $zipArchive = new \ZipArchive();
        if (true !== $zipArchive->open($zipPath)) {
            return 'Error opening ZIP file.';
        }

        $extractTo = $targetDir.'/extracted';
        if (!is_dir($extractTo)) {
            if (!mkdir($extractTo, 0777, true) && !is_dir($extractTo)) {
                return 'Could not create extraction directory: '.$extractTo;
            }
        }

        $zipArchive->extractTo($extractTo);
        $zipArchive->close();
    }

    private function uploadImagesToCms(string $targetDir): void
    {
        $imageDirectory = $targetDir.'/extracted/images';

        if (!is_dir($imageDirectory)) {
            $this->logger->error('Image folder not found: '.$imageDirectory);

            return;
        }

        $imageFiles = glob($imageDirectory.'/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);

        if (empty($imageFiles)) {
            $this->logger->error('No images found in temp image folder: '.$imageDirectory);

            return;
        }

        $oMediaTableConf = new \TCMSTableConf();
        $oMediaTableConf->LoadFromField('name', 'cms_media');

        foreach ($imageFiles as $imagePath) {
            $aImageFileData = ['name' => basename($imagePath), 'type' => getimagesize($imagePath)['mime'] ?? null, 'size' => filesize($imagePath), 'tmp_name' => $imagePath, 'error' => 0];

            $image = \TdbCmsMedia::GetNewInstance();

            $field = ['custom_filename' => $this->removeFileExtension(basename($imagePath))];

            if (true === $image->LoadFromFields($field)) {
                // image already exists, so we do not need to import it again
                continue;
            }

            $oMediaManagerEditor = new \TCMSTableEditorMedia();
            $oMediaManagerEditor->AllowEditByAll(true);
            $oMediaManagerEditor->Init($oMediaTableConf->id);
            $oMediaManagerEditor->SetUploadData($aImageFileData, true);
            $aDocument['cms_media_tree_id'] = self::NEWSLETTER_IMPORTE_MEDIA_TREE_ID;
            $oMediaManagerEditor->Save($aDocument);
        }
    }

    private function importHtmlFromZipToNewsletterCampaign(string $targetDir, string $recordId): void
    {
        $htmlFileDirectory = $targetDir.'/extracted';

        if (!is_dir($htmlFileDirectory)) {
            $this->logger->error('Folder not found: '.$htmlFileDirectory);

            return;
        }

        $firstHtmlFile = $this->findFirstFileOfSpecificType($htmlFileDirectory);
        $html = file_get_contents($firstHtmlFile);
        if (false === $html) {
            return;
        }

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML($html);

        $newsletterHtml = $this->overwriteImageSrcsInHtml($dom);

        $campaignId = $recordId;

        $newsletterUpdateQuery = 'UPDATE `pkg_newsletter_campaign` SET `newsletter_with_import` = :newsletterHtml WHERE `id` = :campaignId';
        $this->connection->executeQuery($newsletterUpdateQuery, ['campaignId' => $campaignId, 'newsletterHtml' => $newsletterHtml]);
    }

    private function importTxtFromZipToNewsletterCampaign(string $targetDir, string $recordId): void
    {
        $txtFileDirectory = $targetDir.'/extracted';

        if (!is_dir($txtFileDirectory)) {
            $this->logger->error('Folder not found: '.$txtFileDirectory);

            return;
        }

        $firstTxtlFile = $this->findFirstFileOfSpecificType($txtFileDirectory, 'txt');
        $txt = file_get_contents($firstTxtlFile);
        if (false === $txt) {
            return;
        }

        $campaignId = $recordId;

        $newsletterUpdateQuery = 'UPDATE `pkg_newsletter_campaign` SET `content_plain__en` = :newsletterTxt, `content_plain` = :newsletterTxt WHERE `id` = :campaignId';
        $this->connection->executeQuery($newsletterUpdateQuery, ['campaignId' => $campaignId, 'newsletterTxt' => $txt]);
    }

    private function findFirstFileOfSpecificType(string $directory, string $type = 'html'): ?string
    {
        $dirHandle = opendir($directory);
        if (false === $dirHandle) {
            return null;
        }

        while (false !== ($entry = readdir($dirHandle))) {
            if ('.' === $entry || '..' === $entry) {
                continue;
            }

            $path = $directory.'/'.$entry;
            if (!is_file($path)) {
                continue;
            }

            $ext = strtolower(pathinfo($entry, PATHINFO_EXTENSION));
            if ($type === $ext) {
                closedir($dirHandle);

                return $path;
            }
        }

        closedir($dirHandle);

        return null;
    }

    private function removeFileExtension(string $filename): string
    {
        return preg_replace('/\.[a-zA-Z0-9]+$/', '', $filename);
    }

    private function overwriteImageSrcsInHtml(\DOMDocument $dom): string
    {
        $images = $dom->getElementsByTagName('img');
        foreach ($images as $img) {
            $oldSrc = $img->getAttribute('src');
            $newSrc = $this->resolveMediaUrlFromRelativeImagePath($oldSrc);

            if (null !== $newSrc) {
                $img->setAttribute('src', $newSrc);
            }
        }

        $links = $dom->getElementsByTagName('link');
        foreach ($links as $link) {
            $oldHref = $link->getAttribute('href');
            $newHref = $this->resolveMediaUrlFromRelativeImagePath($oldHref);

            if (null !== $newHref) {
                $link->setAttribute('href', $newHref);
            }
        }

        $newHtml = $dom->saveHTML();

        if (false === $newHtml) {
            $this->logger->error('Could not save html for node with name: '.$dom->nodeName);

            return '';
        }

        return $newHtml;
    }

    private function resolveMediaUrlFromRelativeImagePath(string $path): ?string
    {
        if ('' === $path) {
            return null;
        }

        // only handle the relative newsletter paths
        if (0 !== strncmp($path, 'images/', 7)) {
            return null;
        }

        $filename = basename($path);

        $image = \TdbCmsMedia::GetNewInstance();

        $field = [
            'custom_filename' => $this->removeFileExtension($filename),
        ];

        if (false === $image->LoadFromFields($field)) {
            $this->logger->warning('image '.$filename.' has not been found in the database');

            return null;
        }

        $mediaImage = $this->cmsMediaDataAccess->getCmsMedia(
            $image->id,
            $this->backendSession->getCurrentEditLanguageId()
        );

        if (null === $mediaImage) {
            $this->logger->warning('image '.$filename.' has not been found in the media database');

            return null;
        }

        return $mediaImage->getImageUrl();
    }
}