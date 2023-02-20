<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ModuleText {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null - Module instance */
private \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null $cmsTplModuleInstance = null,
/** @var null|string - Module instance */
private ?string $cmsTplModuleInstanceId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMedia|null - Optional icon */
private \ChameleonSystem\CoreBundle\Entity\CmsMedia|null $icon = null,
/** @var null|string - Optional icon */
private ?string $iconId = null
, 
    // TCMSFieldVarchar
/** @var string - Headline */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Sub headline */
private string $subheadline = '', 
    // TCMSFieldWYSIWYG
/** @var string - Content */
private string $content = '', 
    // TCMSFieldDownloads
/** @var \ChameleonSystem\CoreBundle\Entity\CmsDocument[] - Download files */
private \Doctrine\Common\Collections\Collection $dataPool = new \Doctrine\Common\Collections\ArrayCollection()  ) {}

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
public function getSubheadline(): string
{
    return $this->subheadline;
}
public function setSubheadline(string $subheadline): self
{
    $this->subheadline = $subheadline;

    return $this;
}


  
    // TCMSFieldLookup
public function getIcon(): \ChameleonSystem\CoreBundle\Entity\CmsMedia|null
{
    return $this->icon;
}
public function setIcon(\ChameleonSystem\CoreBundle\Entity\CmsMedia|null $icon): self
{
    $this->icon = $icon;
    $this->iconId = $icon?->getId();

    return $this;
}
public function getIconId(): ?string
{
    return $this->iconId;
}
public function setIconId(?string $iconId): self
{
    $this->iconId = $iconId;
    // todo - load new id
    //$this->iconId = $?->getId();

    return $this;
}



  
    // TCMSFieldWYSIWYG
public function getContent(): string
{
    return $this->content;
}
public function setContent(string $content): self
{
    $this->content = $content;

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


  
}
