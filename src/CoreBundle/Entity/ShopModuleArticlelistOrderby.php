<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopModuleArticlelistOrderby {
  public function __construct(
    private string|null $id = null,
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
    // TCMSFieldPosition
/** @var int - Position */
private int $position = 0, 
    // TCMSFieldVarchar
/** @var string - SQL ORDER BY String */
private string $sqlOrderBy = '', 
    // TCMSFieldOption
/** @var string - Sorting direction */
private string $orderDirection = 'ASC', 
    // TCMSFieldText
/** @var string - SQL secondary sorting */
private string $sqlSecondaryOrderByString = ''  ) {}

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
public function getSqlOrderBy(): string
{
    return $this->sqlOrderBy;
}
public function setSqlOrderBy(string $sqlOrderBy): self
{
    $this->sqlOrderBy = $sqlOrderBy;

    return $this;
}


  
    // TCMSFieldOption
public function getOrderDirection(): string
{
    return $this->orderDirection;
}
public function setOrderDirection(string $orderDirection): self
{
    $this->orderDirection = $orderDirection;

    return $this;
}


  
    // TCMSFieldText
public function getSqlSecondaryOrderByString(): string
{
    return $this->sqlSecondaryOrderByString;
}
public function setSqlSecondaryOrderByString(string $sqlSecondaryOrderByString): self
{
    $this->sqlSecondaryOrderByString = $sqlSecondaryOrderByString;

    return $this;
}


  
}
