<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsExportProfilesFields {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsExportProfiles|null - Belongs to profile */
private \ChameleonSystem\CoreBundle\Entity\CmsExportProfiles|null $cmsExportProfiles = null,
/** @var null|string - Belongs to profile */
private ?string $cmsExportProfilesId = null
, 
    // TCMSFieldTablefieldnameExport
/** @var string - Field from table */
private string $fieldname = '', 
    // TCMSFieldPosition
/** @var int - Sort order */
private int $sortOrder = 0, 
    // TCMSFieldVarchar
/** @var string - HTML formatting */
private string $htmlTemplate = ''  ) {}

  public function getId(): ?string
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
    // TCMSFieldLookup
public function getCmsExportProfiles(): \ChameleonSystem\CoreBundle\Entity\CmsExportProfiles|null
{
    return $this->cmsExportProfiles;
}
public function setCmsExportProfiles(\ChameleonSystem\CoreBundle\Entity\CmsExportProfiles|null $cmsExportProfiles): self
{
    $this->cmsExportProfiles = $cmsExportProfiles;
    $this->cmsExportProfilesId = $cmsExportProfiles?->getId();

    return $this;
}
public function getCmsExportProfilesId(): ?string
{
    return $this->cmsExportProfilesId;
}
public function setCmsExportProfilesId(?string $cmsExportProfilesId): self
{
    $this->cmsExportProfilesId = $cmsExportProfilesId;
    // todo - load new id
    //$this->cmsExportProfilesId = $?->getId();

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
