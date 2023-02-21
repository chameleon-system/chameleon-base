<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\Shop;

class ShopBankAccount {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var Shop|null - Belongs to shop */
private ?Shop $shop = null
, 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Account owner */
private string $accountOwner = '', 
    // TCMSFieldVarchar
/** @var string - Bank name */
private string $bankname = '', 
    // TCMSFieldVarchar
/** @var string - Bank code */
private string $bankcode = '', 
    // TCMSFieldVarchar
/** @var string - Account number */
private string $accountNumber = '', 
    // TCMSFieldVarchar
/** @var string - BIC code */
private string $bicCode = '', 
    // TCMSFieldVarchar
/** @var string - IBAN number */
private string $ibannumber = ''  ) {}

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
public function getAccountOwner(): string
{
    return $this->accountOwner;
}
public function setAccountOwner(string $accountOwner): self
{
    $this->accountOwner = $accountOwner;

    return $this;
}


  
    // TCMSFieldVarchar
public function getBankname(): string
{
    return $this->bankname;
}
public function setBankname(string $bankname): self
{
    $this->bankname = $bankname;

    return $this;
}


  
    // TCMSFieldVarchar
public function getBankcode(): string
{
    return $this->bankcode;
}
public function setBankcode(string $bankcode): self
{
    $this->bankcode = $bankcode;

    return $this;
}


  
    // TCMSFieldVarchar
public function getAccountNumber(): string
{
    return $this->accountNumber;
}
public function setAccountNumber(string $accountNumber): self
{
    $this->accountNumber = $accountNumber;

    return $this;
}


  
    // TCMSFieldVarchar
public function getBicCode(): string
{
    return $this->bicCode;
}
public function setBicCode(string $bicCode): self
{
    $this->bicCode = $bicCode;

    return $this;
}


  
    // TCMSFieldVarchar
public function getIbannumber(): string
{
    return $this->ibannumber;
}
public function setIbannumber(string $ibannumber): self
{
    $this->ibannumber = $ibannumber;

    return $this;
}


  
}
