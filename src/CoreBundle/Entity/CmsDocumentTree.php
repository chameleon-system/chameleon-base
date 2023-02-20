<?php
namespace ChameleonSystem\CoreBundle\Entity;


class CmsDocumentTree {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Category name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Depth */
private string $depth = '', 
    // TCMSFieldVarchar
/** @var string - Sort sequence */
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
public function getDepth(): string
{
    return $this->depth;
}
public function setDepth(string $depth): self
{
    $this->depth = $depth;

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
