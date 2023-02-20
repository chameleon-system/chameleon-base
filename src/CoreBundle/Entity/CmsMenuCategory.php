<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsMenuCategory {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldPosition
/** @var int - Position */
private int $position = 0, 
    // TCMSFieldVarchar
/** @var string - System name */
private string $systemName = '', 
    // TCMSFieldVarchar
/** @var string -  */
private string $iconFontCssClass = '', 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMenuItem[] - Menu items */
private \Doctrine\Common\Collections\Collection $cmsMenuItemCollection = new \Doctrine\Common\Collections\ArrayCollection()  ) {}

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
public function getCmsMenuItemCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsMenuItemCollection;
}
public function setCmsMenuItemCollection(\Doctrine\Common\Collections\Collection $cmsMenuItemCollection): self
{
    $this->cmsMenuItemCollection = $cmsMenuItemCollection;

    return $this;
}


  
}
