<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\ShopSearchCache;
use ChameleonSystem\CoreBundle\Entity\ShopArticle;

class ShopSearchCacheItem {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var ShopSearchCache|null - Belongs to search cache */
private ?ShopSearchCache $shopSearchCache = null
, 
    // TCMSFieldLookup
/** @var ShopArticle|null - Article */
private ?ShopArticle $shopArticle = null
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
    // TCMSFieldLookup
public function getShopSearchCache(): ?ShopSearchCache
{
    return $this->shopSearchCache;
}

public function setShopSearchCache(?ShopSearchCache $shopSearchCache): self
{
    $this->shopSearchCache = $shopSearchCache;

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


  
}
