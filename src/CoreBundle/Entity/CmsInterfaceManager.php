<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsInterfaceManager {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - System name */
private string $systemname = '', 
    // TCMSFieldVarchar
/** @var string - Used class */
private string $class = '', 
    // TCMSFieldOption
/** @var string - Class type */
private string $classType = 'Core', 
    // TCMSFieldVarchar
/** @var string - Class subtype */
private string $classSubtype = '', 
    // TCMSFieldText
/** @var string - Description */
private string $description = '', 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\CmsInterfaceManagerParameter[] - Parameter */
private \Doctrine\Common\Collections\Collection $cmsInterfaceManagerParameterCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldBoolean
/** @var bool - Restrict to user groups */
private bool $restrictToUserGroups = false, 
    // TCMSFieldLookupMultiselectCheckboxes
/** @var \ChameleonSystem\CoreBundle\Entity\CmsUsergroup[] - Available for the following groups */
private \Doctrine\Common\Collections\Collection $cmsUsergroupMlt = new \Doctrine\Common\Collections\ArrayCollection()  ) {}

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
public function getSystemname(): string
{
    return $this->systemname;
}
public function setSystemname(string $systemname): self
{
    $this->systemname = $systemname;

    return $this;
}


  
    // TCMSFieldVarchar
public function getClass(): string
{
    return $this->class;
}
public function setClass(string $class): self
{
    $this->class = $class;

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


  
    // TCMSFieldPropertyTable
public function getCmsInterfaceManagerParameterCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsInterfaceManagerParameterCollection;
}
public function setCmsInterfaceManagerParameterCollection(\Doctrine\Common\Collections\Collection $cmsInterfaceManagerParameterCollection): self
{
    $this->cmsInterfaceManagerParameterCollection = $cmsInterfaceManagerParameterCollection;

    return $this;
}


  
    // TCMSFieldBoolean
public function isRestrictToUserGroups(): bool
{
    return $this->restrictToUserGroups;
}
public function setRestrictToUserGroups(bool $restrictToUserGroups): self
{
    $this->restrictToUserGroups = $restrictToUserGroups;

    return $this;
}


  
    // TCMSFieldLookupMultiselectCheckboxes
public function getCmsUsergroupMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsUsergroupMlt;
}
public function setCmsUsergroupMlt(\Doctrine\Common\Collections\Collection $cmsUsergroupMlt): self
{
    $this->cmsUsergroupMlt = $cmsUsergroupMlt;

    return $this;
}


  
}
