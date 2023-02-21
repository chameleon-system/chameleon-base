<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\Shop;
use ChameleonSystem\CoreBundle\Entity\PkgShopPaymentTransactionType;
use ChameleonSystem\CoreBundle\Entity\DataMailProfile;

class ShopOrderStatusCode {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldLookup
/** @var Shop|null - Belongs to shop */
private ?Shop $shop = null
, 
    // TCMSFieldVarchar
/** @var string - System name / merchandise management code */
private string $systemName = '', 
    // TCMSFieldLookup
/** @var PkgShopPaymentTransactionType|null - Run following transaction, if status is executed */
private ?PkgShopPaymentTransactionType $pkgShopPaymentTransactionType = null
, 
    // TCMSFieldLookup
/** @var DataMailProfile|null - Email profile */
private ?DataMailProfile $dataMailProfile = null
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
public function getSystemName(): string
{
    return $this->systemName;
}
public function setSystemName(string $systemName): self
{
    $this->systemName = $systemName;

    return $this;
}


  
    // TCMSFieldLookup
public function getPkgShopPaymentTransactionType(): ?PkgShopPaymentTransactionType
{
    return $this->pkgShopPaymentTransactionType;
}

public function setPkgShopPaymentTransactionType(?PkgShopPaymentTransactionType $pkgShopPaymentTransactionType): self
{
    $this->pkgShopPaymentTransactionType = $pkgShopPaymentTransactionType;

    return $this;
}


  
    // TCMSFieldLookup
public function getDataMailProfile(): ?DataMailProfile
{
    return $this->dataMailProfile;
}

public function setDataMailProfile(?DataMailProfile $dataMailProfile): self
{
    $this->dataMailProfile = $dataMailProfile;

    return $this;
}


  
}
