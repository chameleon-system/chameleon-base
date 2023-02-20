<?php
namespace ChameleonSystem\CoreBundle\Entity;


class ShopModuleArticlelistOrderby {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Internal name */
private string $internalname = '', 
    // TCMSFieldVarchar
/** @var string - Public name */
private string $namePublic = '', 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - SQL ORDER BY String */
private string $sqlOrderBy = ''  ) {}

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
public function getInternalname(): string
{
    return $this->internalname;
}
public function setInternalname(string $internalname): self
{
    $this->internalname = $internalname;

    return $this;
}


  
    // TCMSFieldVarchar
public function getNamePublic(): string
{
    return $this->namePublic;
}
public function setNamePublic(string $namePublic): self
{
    $this->namePublic = $namePublic;

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
public function getSqlOrderBy(): string
{
    return $this->sqlOrderBy;
}
public function setSqlOrderBy(string $sqlOrderBy): self
{
    $this->sqlOrderBy = $sqlOrderBy;

    return $this;
}


  
}
