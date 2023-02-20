<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgMultiModuleSet {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Name of the set */
private string $name = '', 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\PkgMultiModuleSetItem[] - Set consists of these modules */
private \Doctrine\Common\Collections\Collection $pkgMultiModuleSetItemCollection = new \Doctrine\Common\Collections\ArrayCollection()  ) {}

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
public function getPkgMultiModuleSetItemCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->pkgMultiModuleSetItemCollection;
}
public function setPkgMultiModuleSetItemCollection(\Doctrine\Common\Collections\Collection $pkgMultiModuleSetItemCollection): self
{
    $this->pkgMultiModuleSetItemCollection = $pkgMultiModuleSetItemCollection;

    return $this;
}


  
}
