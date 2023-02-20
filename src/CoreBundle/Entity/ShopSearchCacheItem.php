<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\ShopSearchCache;

class ShopSearchCacheItem {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookupParentID
/** @var ShopSearchCache|null - Belongs to search cache */
private ?ShopSearchCache $shopSearchCache = null
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
public function getShopSearchCache(): ?ShopSearchCache
{
    return $this->shopSearchCache;
}

public function setShopSearchCache(?ShopSearchCache $shopSearchCache): self
{
    $this->shopSearchCache = $shopSearchCache;

    return $this;
}


  
}
