<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopListfilter {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Title to be shown on top of the filter on the website */
private string $title = '', 
    // TCMSFieldWYSIWYG
/** @var string - Description text shown on top of the filter */
private string $introtext = '', 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopListfilterItem[] - List filter entries */
private \Doctrine\Common\Collections\Collection $pkgShopListfilterItemCollection = new \Doctrine\Common\Collections\ArrayCollection()  ) {}

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
public function getTitle(): string
{
    return $this->title;
}
public function setTitle(string $title): self
{
    $this->title = $title;

    return $this;
}


  
    // TCMSFieldWYSIWYG
public function getIntrotext(): string
{
    return $this->introtext;
}
public function setIntrotext(string $introtext): self
{
    $this->introtext = $introtext;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getPkgShopListfilterItemCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->pkgShopListfilterItemCollection;
}
public function setPkgShopListfilterItemCollection(\Doctrine\Common\Collections\Collection $pkgShopListfilterItemCollection): self
{
    $this->pkgShopListfilterItemCollection = $pkgShopListfilterItemCollection;

    return $this;
}


  
}
