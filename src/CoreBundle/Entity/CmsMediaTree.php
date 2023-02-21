<?php
namespace ChameleonSystem\CoreBundle\Entity;


class CmsMediaTree {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Directoy name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - URL path to the image */
private string $pathCache = '', 
    // TCMSFieldLookup
/** @var CmsMediaTree|null - Is subitem of */
private ?CmsMediaTree $parent = null
, 
    // TCMSFieldVarchar
/** @var string - Position */
private string $entrySort = ''  ) {}

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
public function getParent(): ?CmsMediaTree
{
    return $this->parent;
}

public function setParent(?CmsMediaTree $parent): self
{
    $this->parent = $parent;

    return $this;
}


  
    // TCMSFieldVarchar
public function getEntrySort(): string
{
    return $this->entrySort;
}
public function setEntrySort(string $entrySort): self
{
    $this->entrySort = $entrySort;

    return $this;
}


  
}
