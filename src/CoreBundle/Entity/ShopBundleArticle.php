<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\ShopArticle;

class ShopBundleArticle {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var ShopArticle|null - Belongs to bundle article */
private ?ShopArticle $shopArticle = null
, 
    // TCMSFieldLookup
/** @var ShopArticle|null - Article */
private ?ShopArticle $bundleArticle = null
, 
    // TCMSFieldVarchar
/** @var string - Units */
private string $amount = '1'  ) {}

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
public function getShopArticle(): ?ShopArticle
{
    return $this->shopArticle;
}

public function setShopArticle(?ShopArticle $shopArticle): self
{
    $this->shopArticle = $shopArticle;

    return $this;
}


  
    // TCMSFieldLookup
public function getBundleArticle(): ?ShopArticle
{
    return $this->bundleArticle;
}

public function setBundleArticle(?ShopArticle $bundleArticle): self
{
    $this->bundleArticle = $bundleArticle;

    return $this;
}


  
    // TCMSFieldVarchar
public function getAmount(): string
{
    return $this->amount;
}
public function setAmount(string $amount): self
{
    $this->amount = $amount;

    return $this;
}


  
}
