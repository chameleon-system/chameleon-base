<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopStatisticGroup {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Field with date */
private string $dateRestrictionField = '`shop_order`.`datecreated`', 
    // TCMSFieldVarchar
/** @var string - Groups */
private string $groups = '', 
    // TCMSFieldText
/** @var string - Query */
private string $query = '', 
    // TCMSFieldVarchar
/** @var string - Field with portal limitation */
private string $portalRestrictionField = '', 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldPosition
/** @var int - Position */
private int $position = 0  ) {}

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
public function getDateRestrictionField(): string
{
    return $this->dateRestrictionField;
}
public function setDateRestrictionField(string $dateRestrictionField): self
{
    $this->dateRestrictionField = $dateRestrictionField;

    return $this;
}


  
    // TCMSFieldVarchar
public function getGroups(): string
{
    return $this->groups;
}
public function setGroups(string $groups): self
{
    $this->groups = $groups;

    return $this;
}


  
    // TCMSFieldText
public function getQuery(): string
{
    return $this->query;
}
public function setQuery(string $query): self
{
    $this->query = $query;

    return $this;
}


  
    // TCMSFieldVarchar
public function getPortalRestrictionField(): string
{
    return $this->portalRestrictionField;
}
public function setPortalRestrictionField(string $portalRestrictionField): self
{
    $this->portalRestrictionField = $portalRestrictionField;

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


  
}
