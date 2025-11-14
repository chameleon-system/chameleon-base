<?php

namespace ChameleonSystem\NewsletterBundle\Bridge\Chameleon\TCMSFields;

use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\ImageCrop\Interfaces\CmsMediaDataAccessInterface;
use Psr\Log\LoggerInterface;

class TCMSFieldsWysiwygWithNewsletterImport_TCMSField extends \TCMSFieldWYSIWYG
{
    private const NEWSLETTER_IMPORTE_MEDIA_TREE_ID = '2bf85916-fc60-6f9c-985d-1ac597cbb767';

    // todo make classes in parent protected
    public function GetHTML()
    {
        $viewRenderer = new \ViewRenderer();

        if (false === $this->getNewsletterWithZipImportEnabled()) {
            return $viewRenderer->Render('TCMSFieldWYSIWYG/EditorWithNewsletterImport/editor.html.twig', null, false);
        }

        $viewRenderer->AddSourceObject('sEditorName', 'fieldcontent_'.$this->sTableName.'_'.$this->name);
        $viewRenderer->AddSourceObject('sFieldName', $this->name);
        $viewRenderer->AddSourceObject('extraPluginsConfiguration', $this->getExtraPluginsConfiguration());
        $viewRenderer->AddSourceObject('aEditorSettings', $this->getEditorSettings());
        $sUserCssUrl = $this->getEditorCSSUrl();
        if ('' !== $sUserCssUrl) {
            $cssStyles = [];
            try {
                $cssStyles = $this->getJSStylesSet($sUserCssUrl);
            } catch (\Exception $e) {
                $viewRenderer->AddSourceObject('couldNotLoadCustomCss', true);
                $viewRenderer->AddSourceObject('customCssUrl', $sUserCssUrl);
            }

            $viewRenderer->AddSourceObject('cssStyles', $cssStyles);
        }
        $viewRenderer->AddSourceObject('data', $this->data);
        $viewRenderer->AddSourceObject('editorHeight', (int) str_replace('px', '', $this->getEditorHeight()));

        $viewRenderer->AddSourceObject('isCalledInModal', $this->isCalledInModal());
        $viewRenderer->AddSourceObject('ajaxUrl', $this->GenerateAjaxURL(['_fnc' => 'importZipFile', '_fieldName' => $this->name]));

        return $viewRenderer->Render('TCMSFieldWYSIWYG/EditorWithNewsletterImport/editor.html.twig', null, false);
    }

    public function importZipFile(): void
    {
        $currentRequest = $this->getCurrentRequest();
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

        $this->importHtmlFromZipToNewsletterCampaign($targetDir);
        $this->importTxtFromZipToNewsletterCampaign($targetDir);
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
            $this->getLogger()->error('Could not save html for node with name: '.$dom->nodeName);

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
            $this->getLogger()->warning('image '.$filename.' has not been found in the database');

            return null;
        }

        $mediaImage = $this->getMediaDataAccess()->getCmsMedia(
            $image->id,
            $this->getBackendSession()->getCurrentEditLanguageId()
        );

        if (null === $mediaImage) {
            $this->getLogger()->warning('image '.$filename.' has not been found in the media database');

            return null;
        }

        return $mediaImage->getImageUrl();
    }

    private function importHtmlFromZipToNewsletterCampaign(string $targetDir): void
    {
        $htmlFileDirectory = $targetDir.'/extracted';

        if (!is_dir($htmlFileDirectory)) {
            $this->getLogger()->error('Folder not found: '.$htmlFileDirectory);

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

        $campaignId = $this->recordId;
        $dbConnection = $this->getDatabaseConnection();

        $newsletterUpdateQuery = 'UPDATE `pkg_newsletter_campaign` SET `newsletter_with_import` = :newsletterHtml WHERE `id` = :campaignId';
        $dbConnection->executeQuery($newsletterUpdateQuery, ['campaignId' => $campaignId, 'newsletterHtml' => $newsletterHtml]);
    }

    private function importTxtFromZipToNewsletterCampaign(string $targetDir): void
    {
        $txtFileDirectory = $targetDir.'/extracted';

        if (!is_dir($txtFileDirectory)) {
            $this->getLogger()->error('Folder not found: '.$txtFileDirectory);

            return;
        }

        $firstTxtlFile = $this->findFirstFileOfSpecificType($txtFileDirectory, 'txt');
        $txt = file_get_contents($firstTxtlFile);
        if (false === $txt) {
            return;
        }

        $campaignId = $this->recordId;
        $dbConnection = $this->getDatabaseConnection();

        $newsletterUpdateQuery = 'UPDATE `pkg_newsletter_campaign` SET `content_plain__en` = :newsletterTxt, `content_plain` = :newsletterTxt WHERE `id` = :campaignId';
        $dbConnection->executeQuery($newsletterUpdateQuery, ['campaignId' => $campaignId, 'newsletterTxt' => $txt]);
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

    private function uploadImagesToCms(string $targetDir): void
    {
        $imageDirectory = $targetDir.'/extracted/images';

        if (!is_dir($imageDirectory)) {
            $this->getLogger()->error('Image folder not found: '.$imageDirectory);

            return;
        }

        $imageFiles = glob($imageDirectory.'/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);

        if (empty($imageFiles)) {
            $this->getLogger()->error('No images found in temp image folder: '.$imageDirectory);

            return;
        }

        $oMediaTableConf = new \TCMSTableConf();
        $oMediaTableConf->LoadFromField('name', 'cms_media');

        foreach ($imageFiles as $imagePath) {
            $aImageFileData = ['name' => basename($imagePath), 'type' => getimagesize($imagePath)['mime'] ?? null, 'size' => filesize($imagePath), 'tmp_name' => $imagePath, 'error' => 0];

            $image = \TdbCmsMedia::GetNewInstance();

            // todo more checks?
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

    private function moveZipToTempDirectory($zip, string $targetDir): string
    {
        if (!is_dir($targetDir) && !mkdir($targetDir, 0777, true) && !is_dir($targetDir)) {
            return 'Could not create target directory: '.$targetDir;
        }

        $zipPath = $targetDir.'/'.$zip->getClientOriginalName();
        $zip->move($targetDir, $zip->getClientOriginalName());

        return $zipPath;
    }

    private function sanitizeZipName(string $filename): string
    {
        $name = preg_replace('/\.zip$/i', '', $filename);
        $name = preg_replace('/[^a-zA-Z0-9]+/', '_', $name);
        $name = strtolower(trim($name, '_'));

        return $name;
    }

    protected function DefineInterface(): void
    {
        parent::DefineInterface();
        $this->methodCallAllowed[] = 'importZipFile';
    }

    private function removeFileExtension(string $filename): string
    {
        return preg_replace('/\.[a-zA-Z0-9]+$/', '', $filename);
    }

    private function getLogger(): LoggerInterface
    {
        return ServiceLocator::get('logger');
    }

    private function getMediaDataAccess(): CmsMediaDataAccessInterface
    {
        return ServiceLocator::get('chameleon_system_image_crop.cms_media_data_access');
    }

    protected function getNewsletterWithZipImportEnabled(): bool
    {
        return (bool) ServiceLocator::getParameter('chameleon_system_newsletter.import_via_zip.enabled');
    }
}
