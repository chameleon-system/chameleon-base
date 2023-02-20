<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopBundleArticle {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopArticle|null - Belongs to bundle article */
private \ChameleonSystem\CoreBundle\Entity\ShopArticle|null $shopArticle = null,
/** @var null|string - Belongs to bundle article */
private ?string $shopArticleId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopArticle|null - Article */
private \ChameleonSystem\CoreBundle\Entity\ShopArticle|null $bundleArticle = null,
/** @var null|string - Article */
private ?string $bundleArticleId = null
, 
    // TCMSFieldNumber
/** @var int - Units */
private int $amount = 1, 
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
    // TCMSFieldLookup
public function getShopArticle(): \ChameleonSystem\CoreBundle\Entity\ShopArticle|null
{
    return $this->shopArticle;
}
public function setShopArticle(\ChameleonSystem\CoreBundle\Entity\ShopArticle|null $shopArticle): self
{
    $this->shopArticle = $shopArticle;
    $this->shopArticleId = $shopArticle?->getId();

    return $this;
}
public function getShopArticleId(): ?string
{
    return $this->shopArticleId;
}
public function setShopArticleId(?string $shopArticleId): self
{
    $this->shopArticleId = $shopArticleId;
    // todo - load new id
    //$this->shopArticleId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookup
public function getBundleArticle(): \ChameleonSystem\CoreBundle\Entity\ShopArticle|null
{
    return $this->bundleArticle;
}
public function setBundleArticle(\ChameleonSystem\CoreBundle\Entity\ShopArticle|null $bundleArticle): self
{
    $this->bundleArticle = $bundleArticle;
    $this->bundleArticleId = $bundleArticle?->getId();

    return $this;
}
public function getBundleArticleId(): ?string
{
    return $this->bundleArticleId;
}
public function setBundleArticleId(?string $bundleArticleId): self
{
    $this->bundleArticleId = $bundleArticleId;
    // todo - load new id
    //$this->bundleArticleId = $?->getId();

    return $this;
}



  
    // TCMSFieldNumber
public function getAmount(): int
{
    return $this->amount;
}
public function setAmount(int $amount): self
{
    $this->amount = $amount;

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
