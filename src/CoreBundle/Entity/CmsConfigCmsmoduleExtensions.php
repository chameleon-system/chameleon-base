<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsConfigCmsmoduleExtensions {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsConfig|null - Belongs to cms config */
private \ChameleonSystem\CoreBundle\Entity\CmsConfig|null $cmsConfig = null,
/** @var null|string - Belongs to cms config */
private ?string $cmsConfigId = null
, 
    // TCMSFieldVarchar
/** @var string - Overwritten by */
private string $newclass = '', 
    // TCMSFieldVarchar
/** @var string - Module to overwrite */
private string $name = '', 
    // TCMSFieldOption
/** @var string - Type */
private string $type = 'Customer'  ) {}

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
public function getNewclass(): string
{
    return $this->newclass;
}
public function setNewclass(string $newclass): self
{
    $this->newclass = $newclass;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsConfig(): \ChameleonSystem\CoreBundle\Entity\CmsConfig|null
{
    return $this->cmsConfig;
}
public function setCmsConfig(\ChameleonSystem\CoreBundle\Entity\CmsConfig|null $cmsConfig): self
{
    $this->cmsConfig = $cmsConfig;
    $this->cmsConfigId = $cmsConfig?->getId();

    return $this;
}
public function getCmsConfigId(): ?string
{
    return $this->cmsConfigId;
}
public function setCmsConfigId(?string $cmsConfigId): self
{
    $this->cmsConfigId = $cmsConfigId;
    // todo - load new id
    //$this->cmsConfigId = $?->getId();

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


  
    // TCMSFieldOption
public function getType(): string
{
    return $this->type;
}
public function setType(string $type): self
{
    $this->type = $type;

    return $this;
}


  
}
