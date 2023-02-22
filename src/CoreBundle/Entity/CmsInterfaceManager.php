<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsInterfaceManagerParameter;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class CmsInterfaceManager {
  public function __construct(
    private string $id,
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
    // TCMSFieldVarchar
/** @var string - Class subtype */
private string $classSubtype = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, CmsInterfaceManagerParameter> - Parameter */
private Collection $cmsInterfaceManagerParameterCollection = new ArrayCollection()
  ) {}

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


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, CmsInterfaceManagerParameter>
*/
public function getCmsInterfaceManagerParameterCollection(): Collection
{
    return $this->cmsInterfaceManagerParameterCollection;
}

public function addCmsInterfaceManagerParameterCollection(CmsInterfaceManagerParameter $cmsInterfaceManagerParameter): self
{
    if (!$this->cmsInterfaceManagerParameterCollection->contains($cmsInterfaceManagerParameter)) {
        $this->cmsInterfaceManagerParameterCollection->add($cmsInterfaceManagerParameter);
        $cmsInterfaceManagerParameter->setCmsInterfaceManager($this);
    }

    return $this;
}

public function removeCmsInterfaceManagerParameterCollection(CmsInterfaceManagerParameter $cmsInterfaceManagerParameter): self
{
    if ($this->cmsInterfaceManagerParameterCollection->removeElement($cmsInterfaceManagerParameter)) {
        // set the owning side to null (unless already changed)
        if ($cmsInterfaceManagerParameter->getCmsInterfaceManager() === $this) {
            $cmsInterfaceManagerParameter->setCmsInterfaceManager(null);
        }
    }

    return $this;
}


  
}
