<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopSearchCacheItem {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopSearchCache|null - Belongs to search cache */
private \ChameleonSystem\CoreBundle\Entity\ShopSearchCache|null $shopSearchCache = null,
/** @var null|string - Belongs to search cache */
private ?string $shopSearchCacheId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopArticle|null - Article */
private \ChameleonSystem\CoreBundle\Entity\ShopArticle|null $shopArticle = null,
/** @var null|string - Article */
private ?string $shopArticleId = null
, 
    // TCMSFieldDecimal
/** @var float - Weight */
private float $weight = 0  ) {}

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
public function getShopSearchCache(): \ChameleonSystem\CoreBundle\Entity\ShopSearchCache|null
{
    return $this->shopSearchCache;
}
public function setShopSearchCache(\ChameleonSystem\CoreBundle\Entity\ShopSearchCache|null $shopSearchCache): self
{
    $this->shopSearchCache = $shopSearchCache;
    $this->shopSearchCacheId = $shopSearchCache?->getId();

    return $this;
}
public function getShopSearchCacheId(): ?string
{
    return $this->shopSearchCacheId;
}
public function setShopSearchCacheId(?string $shopSearchCacheId): self
{
    $this->shopSearchCacheId = $shopSearchCacheId;
    // todo - load new id
    //$this->shopSearchCacheId = $?->getId();

    return $this;
}



  
    // TCMSFieldDecimal
public function getWeight(): float
{
    return $this->weight;
}
public function setWeight(float $weight): self
{
    $this->weight = $weight;

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



  
}
