<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsDocumentTree {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsDocumentTree|null - Parent ID */
private \ChameleonSystem\CoreBundle\Entity\CmsDocumentTree|null $parent = null,
/** @var null|string - Parent ID */
private ?string $parentId = null
, 
    // TCMSFieldVarchar
/** @var string - Category name */
private string $name = '', 
    // TCMSFieldNumber
/** @var int - Depth */
private int $depth = 0, 
    // TCMSFieldBoolean
/** @var bool - Hidden? */
private bool $hidden = false, 
    // TCMSFieldNumber
/** @var int - Sort sequence */
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


  
    // TCMSFieldLookup
public function getParent(): \ChameleonSystem\CoreBundle\Entity\CmsDocumentTree|null
{
    return $this->parent;
}
public function setParent(\ChameleonSystem\CoreBundle\Entity\CmsDocumentTree|null $parent): self
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
public function getDepth(): int
{
    return $this->depth;
}
public function setDepth(int $depth): self
{
    $this->depth = $depth;

    return $this;
}


  
    // TCMSFieldBoolean
public function isHidden(): bool
{
    return $this->hidden;
}
public function setHidden(bool $hidden): self
{
    $this->hidden = $hidden;

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
