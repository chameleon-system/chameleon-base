<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopOrderBundleArticle {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopOrderItem|null - Bundle articles of the order */
private \ChameleonSystem\CoreBundle\Entity\ShopOrderItem|null $shopOrderItem = null,
/** @var null|string - Bundle articles of the order */
private ?string $shopOrderItemId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopOrderItem|null - Article belonging to bundle */
private \ChameleonSystem\CoreBundle\Entity\ShopOrderItem|null $bundleArticle = null,
/** @var null|string - Article belonging to bundle */
private ?string $bundleArticleId = null
, 
    // TCMSFieldNumber
/** @var int - Units */
private int $amount = 0, 
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
public function getShopOrderItem(): \ChameleonSystem\CoreBundle\Entity\ShopOrderItem|null
{
    return $this->shopOrderItem;
}
public function setShopOrderItem(\ChameleonSystem\CoreBundle\Entity\ShopOrderItem|null $shopOrderItem): self
{
    $this->shopOrderItem = $shopOrderItem;
    $this->shopOrderItemId = $shopOrderItem?->getId();

    return $this;
}
public function getShopOrderItemId(): ?string
{
    return $this->shopOrderItemId;
}
public function setShopOrderItemId(?string $shopOrderItemId): self
{
    $this->shopOrderItemId = $shopOrderItemId;
    // todo - load new id
    //$this->shopOrderItemId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookup
public function getBundleArticle(): \ChameleonSystem\CoreBundle\Entity\ShopOrderItem|null
{
    return $this->bundleArticle;
}
public function setBundleArticle(\ChameleonSystem\CoreBundle\Entity\ShopOrderItem|null $bundleArticle): self
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
