<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ModuleList {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null - Belongs to module */
private \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null $cmsTplModuleInstance = null,
/** @var null|string - Belongs to module */
private ?string $cmsTplModuleInstanceId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ModuleListCat|null - Category */
private \ChameleonSystem\CoreBundle\Entity\ModuleListCat|null $moduleListCat = null,
/** @var null|string - Category */
private ?string $moduleListCatId = null
, 
    // TCMSFieldVarchar
/** @var string - Title */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Sub headline */
private string $subHeadline = '', 
    // TCMSFieldDateToday
/** @var \DateTime|null - Date */
private \DateTime|null $dateToday = null, 
    // TCMSFieldWYSIWYG
/** @var string - Introduction */
private string $teaserText = '', 
    // TCMSFieldWYSIWYG
/** @var string - Description */
private string $description = '', 
    // TCMSFieldDownloads
/** @var \ChameleonSystem\CoreBundle\Entity\CmsDocument[] - Document pool */
private \Doctrine\Common\Collections\Collection $dataPool = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldPosition
/** @var int - Position */
private int $position = 0  ) {}

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
public function getCmsTplModuleInstance(): \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null
{
    return $this->cmsTplModuleInstance;
}
public function setCmsTplModuleInstance(\ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null $cmsTplModuleInstance): self
{
    $this->cmsTplModuleInstance = $cmsTplModuleInstance;
    $this->cmsTplModuleInstanceId = $cmsTplModuleInstance?->getId();

    return $this;
}
public function getCmsTplModuleInstanceId(): ?string
{
    return $this->cmsTplModuleInstanceId;
}
public function setCmsTplModuleInstanceId(?string $cmsTplModuleInstanceId): self
{
    $this->cmsTplModuleInstanceId = $cmsTplModuleInstanceId;
    // todo - load new id
    //$this->cmsTplModuleInstanceId = $?->getId();

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


  
    // TCMSFieldVarchar
public function getSubHeadline(): string
{
    return $this->subHeadline;
}
public function setSubHeadline(string $subHeadline): self
{
    $this->subHeadline = $subHeadline;

    return $this;
}


  
    // TCMSFieldDateToday
public function getDateToday(): \DateTime|null
{
    return $this->dateToday;
}
public function setDateToday(\DateTime|null $dateToday): self
{
    $this->dateToday = $dateToday;

    return $this;
}


  
    // TCMSFieldLookup
public function getModuleListCat(): \ChameleonSystem\CoreBundle\Entity\ModuleListCat|null
{
    return $this->moduleListCat;
}
public function setModuleListCat(\ChameleonSystem\CoreBundle\Entity\ModuleListCat|null $moduleListCat): self
{
    $this->moduleListCat = $moduleListCat;
    $this->moduleListCatId = $moduleListCat?->getId();

    return $this;
}
public function getModuleListCatId(): ?string
{
    return $this->moduleListCatId;
}
public function setModuleListCatId(?string $moduleListCatId): self
{
    $this->moduleListCatId = $moduleListCatId;
    // todo - load new id
    //$this->moduleListCatId = $?->getId();

    return $this;
}



  
    // TCMSFieldWYSIWYG
public function getTeaserText(): string
{
    return $this->teaserText;
}
public function setTeaserText(string $teaserText): self
{
    $this->teaserText = $teaserText;

    return $this;
}


  
    // TCMSFieldWYSIWYG
public function getDescription(): string
{
    return $this->description;
}
public function setDescription(string $description): self
{
    $this->description = $description;

    return $this;
}


  
    // TCMSFieldDownloads
public function getDataPool(): \Doctrine\Common\Collections\Collection
{
    return $this->dataPool;
}
public function setDataPool(\Doctrine\Common\Collections\Collection $dataPool): self
{
    $this->dataPool = $dataPool;

    return $this;
}


  
    // TCMSFieldPosition
public function getPosition(): int
{
    return $this->position;
}
public function setPosition(int $position): self
{
    $this->position = $position;

    return $this;
}


  
}
