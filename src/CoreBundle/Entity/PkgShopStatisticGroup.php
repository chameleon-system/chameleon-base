<?php
namespace ChameleonSystem\CoreBundle\Entity;


class PkgShopStatisticGroup {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Field with date */
private string $dateRestrictionField = '`shop_order`.`datecreated`', 
    // TCMSFieldVarchar
/** @var string - Groups */
private string $groups = '', 
    // TCMSFieldVarchar
/** @var string - Field with portal limitation */
private string $portalRestrictionField = '', 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = ''  ) {}

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


  
}
