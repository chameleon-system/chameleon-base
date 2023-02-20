<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopArticleStats {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopArticle|null - Belongs to */
private \ChameleonSystem\CoreBundle\Entity\ShopArticle|null $shopArticle = null,
/** @var null|string - Belongs to */
private ?string $shopArticleId = null
, 
    // TCMSFieldNumber
/** @var int - Sales */
private int $statsSales = 0, 
    // TCMSFieldNumber
/** @var int - Details on views */
private int $statsDetailViews = 0, 
    // TCMSFieldDecimal
/** @var float - Average rating */
private float $statsReviewAverage = 0, 
    // TCMSFieldNumber
/** @var int - Number of ratings */
private int $statsReviewCount = 0  ) {}

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



  
    // TCMSFieldNumber
public function getStatsSales(): int
{
    return $this->statsSales;
}
public function setStatsSales(int $statsSales): self
{
    $this->statsSales = $statsSales;

    return $this;
}


  
    // TCMSFieldNumber
public function getStatsDetailViews(): int
{
    return $this->statsDetailViews;
}
public function setStatsDetailViews(int $statsDetailViews): self
{
    $this->statsDetailViews = $statsDetailViews;

    return $this;
}


  
    // TCMSFieldDecimal
public function getStatsReviewAverage(): float
{
    return $this->statsReviewAverage;
}
public function setStatsReviewAverage(float $statsReviewAverage): self
{
    $this->statsReviewAverage = $statsReviewAverage;

    return $this;
}


  
    // TCMSFieldNumber
public function getStatsReviewCount(): int
{
    return $this->statsReviewCount;
}
public function setStatsReviewCount(int $statsReviewCount): self
{
    $this->statsReviewCount = $statsReviewCount;

    return $this;
}


  
}
