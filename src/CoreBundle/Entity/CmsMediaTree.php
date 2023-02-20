<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsMediaTree {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMediaTree|null - Is subitem of */
private \ChameleonSystem\CoreBundle\Entity\CmsMediaTree|null $parent = null,
/** @var null|string - Is subitem of */
private ?string $parentId = null
, 
    // TCMSFieldVarchar
/** @var string - Directoy name */
private string $name = '', 
    // TCMSFieldSmallIconList
/** @var string - Icon */
private string $icon = '', 
    // TCMSFieldVarchar
/** @var string - URL path to the image */
private string $pathCache = '', 
    // TCMSFieldNumber
/** @var int - Position */
private int $entrySort = 0  ) {}

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


  
    // TCMSFieldSmallIconList
public function getIcon(): string
{
    return $this->icon;
}
public function setIcon(string $icon): self
{
    $this->icon = $icon;

    return $this;
}


  
    // TCMSFieldVarchar
public function getPathCache(): string
{
    return $this->pathCache;
}
public function setPathCache(string $pathCache): self
{
    $this->pathCache = $pathCache;

    return $this;
}


  
    // TCMSFieldLookup
public function getParent(): \ChameleonSystem\CoreBundle\Entity\CmsMediaTree|null
{
    return $this->parent;
}
public function setParent(\ChameleonSystem\CoreBundle\Entity\CmsMediaTree|null $parent): self
{
    $this->parent = $parent;
    $this->parentId = $parent?->getId();

    return $this;
}
public function getParentId(): ?string
{
    return $this->parentId;
}
public function setParentId(?string $parentId): self
{
    $this->parentId = $parentId;
    // todo - load new id
    //$this->parentId = $?->getId();

    return $this;
}



  
    // TCMSFieldNumber
public function getEntrySort(): int
{
    return $this->entrySort;
}
public function setEntrySort(int $entrySort): self
{
    $this->entrySort = $entrySort;

    return $this;
}


  
}
