<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsInterfaceManagerParameter {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsInterfaceManager|null - Belongs to interface */
private \ChameleonSystem\CoreBundle\Entity\CmsInterfaceManager|null $cmsInterfaceManager = null,
/** @var null|string - Belongs to interface */
private ?string $cmsInterfaceManagerId = null
, 
    // TCMSFieldText
/** @var string - Description */
private string $description = '', 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Value */
private string $value = ''  ) {}

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
public function getCmsInterfaceManager(): \ChameleonSystem\CoreBundle\Entity\CmsInterfaceManager|null
{
    return $this->cmsInterfaceManager;
}
public function setCmsInterfaceManager(\ChameleonSystem\CoreBundle\Entity\CmsInterfaceManager|null $cmsInterfaceManager): self
{
    $this->cmsInterfaceManager = $cmsInterfaceManager;
    $this->cmsInterfaceManagerId = $cmsInterfaceManager?->getId();

    return $this;
}
public function getCmsInterfaceManagerId(): ?string
{
    return $this->cmsInterfaceManagerId;
}
public function setCmsInterfaceManagerId(?string $cmsInterfaceManagerId): self
{
    $this->cmsInterfaceManagerId = $cmsInterfaceManagerId;
    // todo - load new id
    //$this->cmsInterfaceManagerId = $?->getId();

    return $this;
}



  
    // TCMSFieldText
public function getDescription(): string
{
    return $this->description;
}
public function setDescription(string $description): self
{
    $this->description = $description;

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
public function getValue(): string
{
    return $this->value;
}
public function setValue(string $value): self
{
    $this->value = $value;

    return $this;
}


  
}
