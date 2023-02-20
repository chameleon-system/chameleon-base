<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\ShopArticle;

class ShopArticleStats {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookupParentID
/** @var ShopArticle|null - Belongs to */
private ?ShopArticle $shopArticle = null
, 
    // TCMSFieldVarchar
/** @var string - Sales */
private string $statsSales = '', 
    // TCMSFieldVarchar
/** @var string - Details on views */
private string $statsDetailViews = '', 
    // TCMSFieldVarchar
/** @var string - Number of ratings */
private string $statsReviewCount = ''  ) {}

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
public function getShopArticle(): ?ShopArticle
{
    return $this->shopArticle;
}

public function setShopArticle(?ShopArticle $shopArticle): self
{
    $this->shopArticle = $shopArticle;

    return $this;
}


  
    // TCMSFieldVarchar
public function getStatsSales(): string
{
    return $this->statsSales;
}
public function setStatsSales(string $statsSales): self
{
    $this->statsSales = $statsSales;

    return $this;
}


  
    // TCMSFieldVarchar
public function getStatsDetailViews(): string
{
    return $this->statsDetailViews;
}
public function setStatsDetailViews(string $statsDetailViews): self
{
    $this->statsDetailViews = $statsDetailViews;

    return $this;
}


  
    // TCMSFieldVarchar
public function getStatsReviewCount(): string
{
    return $this->statsReviewCount;
}
public function setStatsReviewCount(string $statsReviewCount): self
{
    $this->statsReviewCount = $statsReviewCount;

    return $this;
}


  
}
