<?php

namespace ChameleonSystem\DataAccessBundle\Entity\Core;

class CmsExportProfilesFields
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldLookupParentID
        /** @var CmsExportProfiles|null - Belongs to profile */
        private ?CmsExportProfiles $cmsExportProfiles = null,
        // TCMSFieldTablefieldnameExport
        /** @var string - Field from table */
        private string $fieldname = '',
        // TCMSFieldPosition
        /** @var int - Sort order */
        private int $sortOrder = 0,
        // TCMSFieldVarchar
        /** @var string - HTML formatting */
        private string $htmlTemplate = ''
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getCmsident(): ?int
    {
        return $this->cmsident;
    }

    public function setCmsident(int $cmsident): self
    {
        $this->cmsident = $cmsident;

        return $this;
    }

    // TCMSFieldLookupParentID
    public function getCmsExportProfiles(): ?CmsExportProfiles
    {
        return $this->cmsExportProfiles;
    }

    public function setCmsExportProfiles(?CmsExportProfiles $cmsExportProfiles): self
    {
        $this->cmsExportProfiles = $cmsExportProfiles;

        return $this;
    }

    // TCMSFieldTablefieldnameExport
    public function getFieldname(): string
    {
        return $this->fieldname;
    }

    public function setFieldname(string $fieldname): self
    {
        $this->fieldname = $fieldname;

        return $this;
    }

    // TCMSFieldPosition
    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(int $sortOrder): self
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    // TCMSFieldVarchar
    public function getHtmlTemplate(): string
    {
        return $this->htmlTemplate;
    }

    public function setHtmlTemplate(string $htmlTemplate): self
    {
        $this->htmlTemplate = $htmlTemplate;

        return $this;
    }
}
