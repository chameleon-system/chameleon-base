<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsExportProfiles {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsPortal|null - Editorial department */
private \ChameleonSystem\CoreBundle\Entity\CmsPortal|null $cmsPortal = null,
/** @var null|string - Editorial department */
private ?string $cmsPortalId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTblConf|null - Table */
private \ChameleonSystem\CoreBundle\Entity\CmsTblConf|null $cmsTblConf = null,
/** @var null|string - Table */
private ?string $cmsTblConfId = null
, 
    // TCMSFieldVarchar
/** @var string - Profile name */
private string $name = '', 
    // TCMSFieldOption
/** @var string - Export format */
private string $exportType = 'TABs', 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\CmsExportProfilesFields[] - Fields to be exported */
private \Doctrine\Common\Collections\Collection $cmsExportProfilesFieldsCollection = new \Doctrine\Common\Collections\ArrayCollection()  ) {}

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
    // TCMSFieldVarchar
public function getName(): string
{
    return $this->name;
}
public function setName(string $name): self
{
    $this->name = $name;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsPortal(): \ChameleonSystem\CoreBundle\Entity\CmsPortal|null
{
    return $this->cmsPortal;
}
public function setCmsPortal(\ChameleonSystem\CoreBundle\Entity\CmsPortal|null $cmsPortal): self
{
    $this->cmsPortal = $cmsPortal;
    $this->cmsPortalId = $cmsPortal?->getId();

    return $this;
}
public function getCmsPortalId(): ?string
{
    return $this->cmsPortalId;
}
public function setCmsPortalId(?string $cmsPortalId): self
{
    $this->cmsPortalId = $cmsPortalId;
    // todo - load new id
    //$this->cmsPortalId = $?->getId();

    return $this;
}



  
    // TCMSFieldOption
public function getExportType(): string
{
    return $this->exportType;
}
public function setExportType(string $exportType): self
{
    $this->exportType = $exportType;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsTblConf(): \ChameleonSystem\CoreBundle\Entity\CmsTblConf|null
{
    return $this->cmsTblConf;
}
public function setCmsTblConf(\ChameleonSystem\CoreBundle\Entity\CmsTblConf|null $cmsTblConf): self
{
    $this->cmsTblConf = $cmsTblConf;
    $this->cmsTblConfId = $cmsTblConf?->getId();

    return $this;
}
public function getCmsTblConfId(): ?string
{
    return $this->cmsTblConfId;
}
public function setCmsTblConfId(?string $cmsTblConfId): self
{
    $this->cmsTblConfId = $cmsTblConfId;
    // todo - load new id
    //$this->cmsTblConfId = $?->getId();

    return $this;
}



  
    // TCMSFieldPropertyTable
public function getCmsExportProfilesFieldsCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsExportProfilesFieldsCollection;
}
public function setCmsExportProfilesFieldsCollection(\Doctrine\Common\Collections\Collection $cmsExportProfilesFieldsCollection): self
{
    $this->cmsExportProfilesFieldsCollection = $cmsExportProfilesFieldsCollection;

    return $this;
}


  
}
