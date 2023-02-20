<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsTplModuleInstance {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsPortal|null - was created in portal */
private \ChameleonSystem\CoreBundle\Entity\CmsPortal|null $cmsPortal = null,
/** @var null|string - was created in portal */
private ?string $cmsPortalId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTplModule|null - Module ID */
private \ChameleonSystem\CoreBundle\Entity\CmsTplModule|null $cmsTplModule = null,
/** @var null|string - Module ID */
private ?string $cmsTplModuleId = null
, 
    // TCMSFieldVarchar
/** @var string - Instance name */
private string $name = '', 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTplPageCmsMasterPagedefSpot[] - CMS pages dynamic spots */
private \Doctrine\Common\Collections\Collection $cmsTplPageCmsMasterPagedefSpotCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldVarchar
/** @var string - File name of the module template */
private string $template = ''  ) {}

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


  
    // TCMSFieldPropertyTable
public function getCmsTplPageCmsMasterPagedefSpotCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsTplPageCmsMasterPagedefSpotCollection;
}
public function setCmsTplPageCmsMasterPagedefSpotCollection(\Doctrine\Common\Collections\Collection $cmsTplPageCmsMasterPagedefSpotCollection): self
{
    $this->cmsTplPageCmsMasterPagedefSpotCollection = $cmsTplPageCmsMasterPagedefSpotCollection;

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



  
    // TCMSFieldVarchar
public function getTemplate(): string
{
    return $this->template;
}
public function setTemplate(string $template): self
{
    $this->template = $template;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsTplModule(): \ChameleonSystem\CoreBundle\Entity\CmsTplModule|null
{
    return $this->cmsTplModule;
}
public function setCmsTplModule(\ChameleonSystem\CoreBundle\Entity\CmsTplModule|null $cmsTplModule): self
{
    $this->cmsTplModule = $cmsTplModule;
    $this->cmsTplModuleId = $cmsTplModule?->getId();

    return $this;
}
public function getCmsTplModuleId(): ?string
{
    return $this->cmsTplModuleId;
}
public function setCmsTplModuleId(?string $cmsTplModuleId): self
{
    $this->cmsTplModuleId = $cmsTplModuleId;
    // todo - load new id
    //$this->cmsTplModuleId = $?->getId();

    return $this;
}



  
}
