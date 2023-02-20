<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\Shop;
use ChameleonSystem\CoreBundle\Entity\DataExtranetUser;

class ShopSearchLog {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookupParentID
/** @var Shop|null - Belongs to shop */
private ?Shop $shop = null
, 
    // TCMSFieldVarchar
/** @var string - Search term */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Number of results */
private string $numberOfResults = '', 
    // TCMSFieldLookupParentID
/** @var DataExtranetUser|null - Executed by */
private ?DataExtranetUser $dataExtranetUser = null
  ) {}

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
    // TCMSFieldLookupParentID
public function getShop(): ?Shop
{
    return $this->shop;
}

public function setShop(?Shop $shop): self
{
    $this->shop = $shop;

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
public function getNumberOfResults(): string
{
    return $this->numberOfResults;
}
public function setNumberOfResults(string $numberOfResults): self
{
    $this->numberOfResults = $numberOfResults;

    return $this;
}


  
    // TCMSFieldLookupParentID
public function getDataExtranetUser(): ?DataExtranetUser
{
    return $this->dataExtranetUser;
}

public function setDataExtranetUser(?DataExtranetUser $dataExtranetUser): self
{
    $this->dataExtranetUser = $dataExtranetUser;

    return $this;
}


  
}
