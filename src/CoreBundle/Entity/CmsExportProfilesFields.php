<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsExportProfiles;

class CmsExportProfilesFields {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookupParentID
/** @var CmsExportProfiles|null - Belongs to profile */
private ?CmsExportProfiles $cmsExportProfiles = null
, 
    // TCMSFieldVarchar
/** @var string - HTML formatting */
private string $htmlTemplate = ''  ) {}

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
