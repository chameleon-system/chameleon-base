<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsSmartUrlHandler {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - PHP class */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Path to smart URL handler class */
private string $classSubtype = '', 
    // TCMSFieldOption
/** @var string - PHP class type */
private string $classType = 'Customer', 
    // TCMSFieldBoolean
/** @var bool - Active */
private bool $active = true, 
    // TCMSFieldLookupMultiselectCheckboxes
/** @var \ChameleonSystem\CoreBundle\Entity\CmsPortal[] - Portal selection */
private \Doctrine\Common\Collections\Collection $cmsPortalMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
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
public function getClassSubtype(): string
{
    return $this->classSubtype;
}
public function setClassSubtype(string $classSubtype): self
{
    $this->classSubtype = $classSubtype;

    return $this;
}


  
    // TCMSFieldOption
public function getClassType(): string
{
    return $this->classType;
}
public function setClassType(string $classType): self
{
    $this->classType = $classType;

    return $this;
}


  
    // TCMSFieldBoolean
public function isActive(): bool
{
    return $this->active;
}
public function setActive(bool $active): self
{
    $this->active = $active;

    return $this;
}


  
    // TCMSFieldLookupMultiselectCheckboxes
public function getCmsPortalMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsPortalMlt;
}
public function setCmsPortalMlt(\Doctrine\Common\Collections\Collection $cmsPortalMlt): self
{
    $this->cmsPortalMlt = $cmsPortalMlt;

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
