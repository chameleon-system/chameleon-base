<?php

namespace ChameleonSystem\MarkdownCmsBundle\Bridge\Chameleon\Entity;

class DocumentFileDownload extends \ChameleonSystemMarkdownCmsBundleBridgeChameleonEntityDocumentFileDownloadAutoParent
{
    public function GetFileNameWithExtension()
    {
        $fileName = $this->fieldFilename;

        $fileType = $this->GetFileType();
        if (null !== $fileType) {
            $fileName .= '.'.$fileType->fieldFileExtension;
        }

        return $fileName;
    }

    public function getLink($bAbsolute = false, $sAnchor = null, $aOptionalParameters = [], ?\TdbCmsPortal $portal = null, ?\TdbCmsLanguage $language = null)
    {
        return $this->GetPlainDownloadLink(false, false, !$bAbsolute);
    }
}
