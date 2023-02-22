<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsMenuItem;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class CmsMenuCategory {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - System name */
private string $systemName = '', 
    // TCMSFieldVarchar
/** @var string -  */
private string $iconFontCssClass = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, CmsMenuItem> - Menu items */
private Collection $cmsMenuItemCollection = new ArrayCollection()
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
public function getSystemName(): string
{
    return $this->systemName;
}
public function setSystemName(string $systemName): self
{
    $this->systemName = $systemName;

    return $this;
}


  
    // TCMSFieldVarchar
public function getIconFontCssClass(): string
{
    return $this->iconFontCssClass;
}
public function setIconFontCssClass(string $iconFontCssClass): self
{
    $this->iconFontCssClass = $iconFontCssClass;

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, CmsMenuItem>
*/
public function getCmsMenuItemCollection(): Collection
{
    return $this->cmsMenuItemCollection;
}

public function addCmsMenuItemCollection(CmsMenuItem $cmsMenuItem): self
{
    if (!$this->cmsMenuItemCollection->contains($cmsMenuItem)) {
        $this->cmsMenuItemCollection->add($cmsMenuItem);
        $cmsMenuItem->setCmsMenuCategory($this);
    }

    return $this;
}

public function removeCmsMenuItemCollection(CmsMenuItem $cmsMenuItem): self
{
    if ($this->cmsMenuItemCollection->removeElement($cmsMenuItem)) {
        // set the owning side to null (unless already changed)
        if ($cmsMenuItem->getCmsMenuCategory() === $this) {
            $cmsMenuItem->setCmsMenuCategory(null);
        }
    }

    return $this;
}


  
}
